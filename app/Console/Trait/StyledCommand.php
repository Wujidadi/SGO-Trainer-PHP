<?php

namespace App\Console\Trait;

use App\Constants\JsonFlag;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

trait StyledCommand
{
    /**
     * Custom color map.
     *
     * @var array<string, string>
     */
    protected const COLOR = [
        'Gold' => '#FFD700',
        'RoyalBlue' => '#4169E1',
    ];

    /**
     * Custom style list.
     *
     * @var array<string, array<string, string|array>>
     */
    protected const STYLE = [
        'gold-text' => [
            'foreground' => self::COLOR['Gold'],
        ],
        'blue-text' => [
            'foreground' => self::COLOR['RoyalBlue'],
            'background' => 'white',
            'options' => ['underscore'],
        ],
    ];

    /**
     * Register custom styles.
     *
     * @return void
     */
    protected function registerStyles(): void
    {
        foreach (self::STYLE as $name => $style) {
            $this->output->getFormatter()->setStyle(
                $name,
                new OutputFormatterStyle(
                    $style['foreground'] ?? null,
                    $style['background'] ?? null,
                    $style['options'] ?? []
                )
            );
        }
    }

    /**
     * Output style of the console command.
     *
     * @var ?string
     */
    protected ?string $outputStyle = null;

    /**
     * Set the output style of the console command.
     *
     * @param string $styleName
     * @return void
     */
    protected function setOutputStyle(string $styleName): void
    {
        $this->outputStyle = $styleName;
    }

    /**
     * Output the console command result with specified custom style.
     *
     * @param mixed $data
     * @param bool $json
     * @param bool $prettyJson
     * @return void
     */
    protected function export(mixed $data, bool $json = false, bool $prettyJson = true): void
    {
        switch (true) {
            case is_string($data):
            case is_integer($data):
            case is_float($data):
            case is_null($data):
                $this->line($data, $this->outputStyle);
                break;
            case is_bool($data):
                $this->line($data ? 'true' : 'false', $this->outputStyle);
                break;
            case is_array($data):
            case is_object($data):
            default:
                if ($json) {
                    $this->line(json_encode($data, $prettyJson ? JsonFlag::PRETTY : JsonFlag::UNESCAPED), $this->outputStyle);
                } else {
                    $this->line(var_export($data, true), $this->outputStyle);
                }
                break;
        }
    }
}
