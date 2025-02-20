<?php

declare(strict_types=1);

namespace App\Renderers;

use App\App;
use Chewie\Concerns\Aligns;
use Laravel\Prompts\Themes\Default\Renderer;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;

abstract class AppRenderer extends Renderer
{
    use Aligns;
    use DrawsBoxes;

    public App $app;

    /** @var string[] */
    public array $lines = [];

    /**
     * @return string[]
     */
    protected function renderInstructions(): array
    {
        $bottomPadding = $this->app->totalHeight - mb_substr_count(collect($this->lines)->implode(PHP_EOL), PHP_EOL);

        if ($bottomPadding > 0) {
            $this->lines = array_merge($this->lines, array_fill(0, $bottomPadding, ''));
        }

        $this->centerHorizontally($this->getInstructions(), $this->app->totalWidth)->each(function ($line) {
            $this->lines[] = $line;
        });
        $this->lines[] = PHP_EOL;

        return $this->lines;
    }

    abstract protected function getInstructions(): string;

    protected function getBoxOutput(string $title, string $body, string $footer, string $info, string $color, int $width, ?int $lines = 12): string
    {
        // Reset the output string
        $this->output = '';

        // Set the minWidth to the desired width, the box method
        // uses this to calculate how wide the box should be
        $this->minWidth = $width;

        // We need to split the body into chunks to fit the width. Right now it's a whole piece of text with spaces/newlines, but we need each line to be $width wide
        $bodyLines = collect(explode(PHP_EOL, $body))
            ->map(function ($line) use ($width) {
                $split = mb_str_split($line, $width);

                return empty($split) ? PHP_EOL : $split; // Retain any blank lines
            })
            ->flatten();

        $bodyLineCount = count($bodyLines);

        if ($lines) {
            if ($bodyLineCount < $lines) {
                $bodyLines->push(...array_fill(0, $lines - $bodyLineCount, ''));
            } else {
                $bodyLines = $bodyLines->take($lines);
                $lastLine = mb_substr($bodyLines->pop() ?? '', 0, -3) . '...';
                $bodyLines->push($lastLine);
            }
        }

        $this->box(
            title: $title,
            body: $bodyLines->map(fn ($line) => trim($line, ' '))->implode(PHP_EOL),
            footer: $footer,
            info: $info,
            color: $color,
        );

        $content = $this->output;

        $this->output = '';

        return $content;
    }

    protected function appropriateGreeting(int $hour): string
    {
        return match(true) {
            $hour >= 0 && $hour < 6 => '🌙 Howdy, burning the midnight oil a little there',
            $hour >= 6 && $hour < 12 => '🌞 Good morning',
            $hour >= 12 && $hour < 18 => '🌆 Good afternoon',
            $hour >= 18 && $hour < 24 => '🌃 Good evening',
            default => '👋 Well howdy there',
        };
    }
}
