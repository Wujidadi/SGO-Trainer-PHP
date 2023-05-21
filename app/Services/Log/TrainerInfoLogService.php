<?php

namespace App\Services\Log;

use App\Constants\JsonFlag;
use App\Exceptions\DbLogException;
use App\Models\TrainerInfoLogs;
use App\Utilities\Log\LogFacade;
use Carbon\Carbon;
use Exception;

class TrainerInfoLogService
{
    protected string $playerName;
    protected string $category;
    protected string $message;

    protected const MIN_SAME_LOG_INTERVAL_IN_MINUTE = 3;

    public function setPlayer(string $playerName): static
    {
        $this->playerName = $playerName;
        return $this;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @throws DbLogException
     */
    public function write(mixed ...$message): int
    {
        if (empty($this->playerName)) {
            throw new DbLogException('Player has not been set');
        }

        if (empty($this->category)) {
            throw new DbLogException('Category has not been set');
        }

        $count = count($message);
        if ($count === 0) {
            throw new DbLogException('Message is empty');
        }
        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                if (is_object($message[$i]) || is_array($message[$i])) {
                    $message[$i] = json_encode($message[$i], JsonFlag::UNESCAPED);
                }
            }
        }
        $this->message = call_user_func_array('sprintf', $message);

        try {
            if (!$this->hasQuickSameLog()) {
                TrainerInfoLogs::create([
                    'time' => Carbon::now()->format('Y-m-d H:i:s.u'),
                    'player_name' => $this->playerName,
                    'category' => $this->category,
                    'message' => $this->message,
                ]);
            }
            return 0;
        } catch (Exception $e) {
            LogFacade::laravel()->error(
                "%s (%s) %s\n%s",
                $e::class,
                $e->getCode(),
                $e->getMessage(),
                $e->getTraceAsString()
            );
            return 1;
        }
    }

    protected function hasQuickSameLog(): bool
    {
        $lastLog = TrainerInfoLogs::where('player_name', $this->playerName)
            ->orderByDesc('time')
            ->first();
        if (!$lastLog) {
            return false;
        }
        if ($lastLog->category !== $this->category) {
            return false;
        }
        if ($lastLog->message !== $this->message) {
            return false;
        }
        if ($lastLog->time->diffInMinutes() >= self::MIN_SAME_LOG_INTERVAL_IN_MINUTE) {
            return false;
        }
        return true;
    }
}
