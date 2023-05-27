<?php

namespace App\Console\Commands;

use App\Console\Trait\AutoSGO\HandleEquip;
use App\Console\Trait\AutoSGO\HandleHunt;
use App\Console\Trait\AutoSGO\HandleWeak;
use App\Constants\Hunt;
use App\Exceptions\DbLogException;
use App\Exceptions\GetPlayerException;
use App\Exceptions\SgoServerException;
use Exception;

class AutoHunt extends AutoSGO
{
    use HandleHunt, HandleWeak, HandleEquip;

    protected $signature = 'sgo:auto-hunt {--player=}';
    protected $description = '自動狩獵';

    protected ?int $zone;
    protected ?int $stage;
    protected ?int $rush;

    protected const ROUND_PER_MINUTE = 5;
    protected const MAX_ACTION_SECOND_IN_MINUTE = 58;
    protected const USLEEP = 11_000_000;

    public function handle(): void
    {
        $this->player = $this->option('player');
        if (!$this->player) {
            return;
        }

        $this->getServices();

        $round = 0;

        while ($round < self::ROUND_PER_MINUTE && (int) date('s') < self::MAX_ACTION_SECOND_IN_MINUTE) {
            if ($round++ > 0) {
                usleep(self::USLEEP);
            }

            try {
                $this->getSetting();

                $this->zone = $this->setting->hunt->zone ?? null;
                $this->stage = $this->setting->hunt->stage ?? null;
                $this->rush = $this->setting->hunt->rush ?? null;

                if (!$this->zone || !$this->stage) {
                    continue;
                }

                if (!$this->isAutoHuntOn()) {
                    continue;
                }

                $this->getProfile();

                $this->getTime();

                $this->calculateTop();
                $this->calculateBottom();

                if ($this->profile->actionStatus === '閒置') {
                    // 死亡
                    if ($this->profile->hp <= 0) {
                        $this->logDie();
                        // 重生
                        $this->revive();
                        $this->logRevive();
                        continue;
                    }

                    // 冷卻中
                    if ($this->profile->canAttackTime - Hunt::COOLDOWN['hunt'] >= $this->timestamp) {
                        $this->logCoolingDown();
                        // 冷卻時間還剩超過 2 秒，跳到下一循環
                        if ($this->profile->canAttackTime - Hunt::COOLDOWN['hunt'] - $this->timestamp > 2000) {
                            continue;
                        }
                        // 冷卻時間 2 秒以下，停 1 秒後直接狩獵
                        sleep(1);
                    }

                    // 狩獵
                    $this->handleHunt();
                } else {
                    $this->logActing();

                    // 移動中
                    if ($this->profile->actionStatus === '移動' && isset($this->profile->movingCompletionTime) && $this->profile->movingCompletionTime <= $this->timestamp) {
                        // 完成移動
                        $this->completeMove();
                        $this->logCompleteMove();
                        // 安全樓層以下（殺人犯活躍區內）強制吃藥，沒藥吃就回城休息
                        if ($this->forceTakeMedicine()) {
                            continue;
                        }
                        // 繼續狩獵
                        $this->handleHunt();
                        continue;
                    }

                    // 其他行動中
                    if (isset($this->profile->canCompleteAction) && $this->profile->canCompleteAction === true) {
                        // 完成行動
                        $this->logCompleteAction($response = $this->completeAction());
                        $this->profile = $response->profile;
                        // HP 或 SP 未達門檻
                        if ($this->weakSetting->replenish) {
                            // 吃藥
                            if ($this->profile->hp < $this->hpTop->replenish) {
                                $this->takeMedicine('hp');
                            }
                            if ($this->profile->sp < $this->spTop->replenish) {
                                $this->takeMedicine('sp');
                            }
                        } else {
                            // 安全樓層以下（殺人犯活躍區內）強制吃藥，沒藥吃就回城休息
                            $this->forceTakeMedicine();
                            // 休息
                            if ($this->profile->hp < $this->hpTop->rest) {
                                $this->rest();
                                $this->logRest('hp');
                                continue;
                            }
                            if ($this->profile->sp < $this->spTop->rest) {
                                $this->rest();
                                $this->logRest('sp');
                                continue;
                            }
                        }
                        // 繼續狩獵
                        $this->handleHunt();
                        continue;
                    }
                }
            } catch (GetPlayerException $e) {
                $this->handleGetPlayerException($e);
            } catch (SgoServerException $e) {
                $this->handleSgoServerException($e);
            } catch (DbLogException $e) {
                $this->handleDbLogException($e);
            } catch (Exception $e) {
                $this->handleException($e);
            }
        }
    }
}
