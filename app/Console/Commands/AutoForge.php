<?php

namespace App\Console\Commands;

use App\Console\Trait\AutoSGO\HandleForge;
use App\Exceptions\DbLogException;
use App\Exceptions\GetPlayerException;
use App\Exceptions\NullMineException;
use App\Exceptions\SgoServerException;
use Exception;

class AutoForge extends AutoSGO
{
    use HandleForge;

    protected $signature = 'sgo:auto-forge {--player=}';
    protected $description = '自動鍛造';

    protected ?int $zone;
    protected ?int $stage;
    protected ?int $rush;

    protected const ROUND_PER_MINUTE = 2;
    protected const MAX_ACTION_SECOND_IN_MINUTE = 58;
    protected const USLEEP = 30_000_000;

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
                if ($this->isForgePayloadNotFilled()) {
                    break;
                }

                if (!$this->isAutoForgeOn()) {
                    continue;
                }

                $this->getProfile();

                $this->getTime();

                if ($this->profile->actionStatus === '閒置') {
                    // 鍛造
                    $this->handleForge();
                } else {
                    $this->logActing();

                    // 鍛造可完成
                    if ($this->profile->actionStatus === '鍛造' && $this->canCompleteForge()) {
                        $this->logCompleteAction($response = $this->completeForge());
                        $this->profile = $response->profile;
                        // 繼續鍛造
                        $this->handleForge();
                        continue;
                    }

                    // 移動中
                    if ($this->profile->actionStatus === '移動' && isset($this->profile->movingCompletionTime) && $this->profile->movingCompletionTime <= $this->timestamp) {
                        // 完成移動
                        $response = $this->completeMove();
                        $this->profile = $response->profile;
                        $this->logCompleteMove();
                        // 鍛造
                        $this->handleForge();
                        continue;
                    }

                    // 其他行動中
                    if (isset($this->profile->canCompleteAction) && $this->profile->canCompleteAction === true) {
                        // 完成行動
                        $this->logCompleteAction($response = $this->completeAction());
                        $this->profile = $response->profile;
                        // 鍛造
                        $this->handleForge();
                        continue;
                    }
                }
            } catch (GetPlayerException $e) {
                $this->handleGetPlayerException($e);
            } catch (SgoServerException $e) {
                $this->handleSgoServerException($e);
            } catch (NullMineException $e) {
                $this->handleNullMineException($e);
            } catch (DbLogException $e) {
                $this->handleDbLogException($e);
            } catch (Exception $e) {
                $this->handleException($e);
            }
        }
    }
}
