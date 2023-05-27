<?php

namespace App\Console\Trait\AutoSGO;

use App\Exceptions\SgoServerException;

/**
 * 處理各種行動
 *
 * @used-by \App\Console\Commands\AutoSGO
 */
trait HandleAction
{
    /**
     * 各種行動處理的基底方法
     *
     * @param string $action
     * @param mixed ...$args
     * @return object
     * @throws SgoServerException
     */
    private function handleAction(string $action, mixed ...$args): object
    {
        if (is_string($response = $this->service->$action(...$args))) {
            throw new SgoServerException($response, SgoServerException::GENERAL);
        }
        return $response;
    }

    /**
     * 休息
     *
     * @return void
     * @throws SgoServerException
     */
    protected function rest(): void
    {
        $this->handleAction('rest');
    }

    /**
     * 移動
     *
     * @param int $zone
     * @return void
     * @throws SgoServerException
     */
    protected function move(int $zone): void
    {
        $this->handleAction('move', $zone);
    }

    /**
     * 完成移動
     *
     * @return object
     * @throws SgoServerException
     */
    protected function completeMove(): object
    {
        return $this->handleAction('completeMove');
    }

    /**
     * 進入岔路
     *
     * @param int $zone
     * @return object
     * @throws SgoServerException
     */
    protected function path(int $zone): object
    {
        return $this->handleAction('path', $zone);
    }

    /**
     * 回城
     *
     * @return void
     * @throws SgoServerException
     */
    protected function goHome(): void
    {
        $this->handleAction('goHome');
    }

    /**
     * 完成行動
     *
     * @return object
     * @throws SgoServerException
     */
    protected function completeAction(): object
    {
        return $this->handleAction('completeAction');
    }

    /**
     * 狩獵
     *
     * @param int $type
     * @return object
     * @throws SgoServerException
     */
    protected function hunt(int $type): object
    {
        return $this->handleAction('hunt', $type);
    }

    /**
     * 使用物品
     *
     * @param int $id
     * @param int $quantity
     * @return object
     * @throws SgoServerException
     */
    protected function useItem(int $id, int $quantity): object
    {
        return $this->handleAction('useItem', $id, $quantity);
    }

    /**
     * 鍛造
     *
     * @return object
     * @throws SgoServerException
     */
    protected function forge(): object
    {
        $payload = $this->setting->forge;
        return $this->handleAction('forge', $payload);
    }

    /**
     * 完成鍛造
     *
     * @return object
     * @throws SgoServerException
     */
    protected function completeForge(): object
    {
        return $this->handleAction('completeForge');
    }

    /**
     * 重生
     *
     * @return void
     * @throws SgoServerException
     */
    protected function revive(): void
    {
        $this->handleAction('revive');
    }
}
