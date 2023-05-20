<?php

namespace App\Console\Trait\AutoSGO;

use App\Exceptions\GetPlayerException;
use App\Exceptions\SgoServerException;
use App\Utilities\Log\LogFacade;
use Throwable;

/**
 * 處理例外狀況
 *
 * @used-by \App\Console\Commands\AutoSGO
 */
trait HandleException
{
    /**
     * 處理一般例外
     *
     * @param Throwable $e
     * @return void
     */
    protected function handleException(Throwable $e): void
    {
        LogFacade::trainer()->error(
            "%s (%s) %s\n%s",
            $e::class,
            $e->getCode(),
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }

    /**
     * 處理查詢玩家資料例外
     *
     * @param GetPlayerException $e
     * @return void
     */
    protected function handleGetPlayerException(GetPlayerException $e): void
    {
        LogFacade::trainer()->error('%s: %s', $this->player, $e->getMessage());
    }

    /**
     * 處理 SGO 伺服器例外
     *
     * @param SgoServerException $e
     * @return void
     */
    protected function handleSgoServerException(SgoServerException $e): void
    {
        $logPrefix = [
            SgoServerException::GENERAL => 'SGO error response: ',
            SgoServerException::GET_PROFILE => 'SGO get profile error: ',
        ];

        $code = $e->getCode();
        $message = $e->getMessage();

        if (preg_match('/<title>(.*)<\/title>/', $e->getMessage(), $matches)) {
            $message = $logPrefix[$code] . $matches[1];
        } else if (preg_match('/<h1>(.*)<\/h1>/', $e->getMessage(), $matches)) {
            $message = $matches[1];
        }

        LogFacade::trainer()->error($message);
    }
}
