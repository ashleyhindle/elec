<?php

declare(strict_types=1);

namespace App\Renderers;

use App\App;
use App\Whc\Site;

class ClosestSitesRenderer extends AppRenderer
{
    public function __invoke(App $app): static
    {
        $this->app = $app;
        $theirHour = (int) ($this->app->ipResponse?->currentTime->format('H') ?? date('H'));

        $this->lines[] = sprintf('%s %s from %s! ❤️%s', $this->appropriateGreeting($theirHour), $this->app->name, $this->app->ipResponse->city, PHP_EOL);
        $this->renderClosestSites();
        $this->renderInstructions();
        $this->output = implode(PHP_EOL, $this->lines);

        return $this;
    }

    /**
     * @return string[]
     */
    protected function renderClosestSites(): array
    {
        $closestSitesBoxWidth = (int) floor($this->app->totalWidth / $this->app->columnsPerRow) - 4;
        $closestSitesBoxHeight = (int) floor($this->app->totalHeight / 4) - 6; // we want to fit opening info, 2 rows of sites, and the footer bar
        if (count($this->app->closestSites) > 0) {
            $this->lines[] = sprintf('Here are the closest %d %s World Heritage Sites to you: %s', $this->app->numberOfClosestSitesToShow, implode(', ', $this->app->categories), PHP_EOL);
            $siteBoxes = collect($this->app->closestSites)->map(function (Site $site, int $index) use ($closestSitesBoxWidth, $closestSitesBoxHeight) {
                $safeSiteName = mb_substr($site->nameEn, 0, $closestSitesBoxWidth - 4);

                return explode(PHP_EOL, $this->getBoxOutput(
                    title: $this->app->siteIndex === $index ? $this->cyan($safeSiteName) : $this->dim($safeSiteName),
                    body: strip_tags($site->shortDescriptionEn),
                    footer: "{$site->category}, {$site->dateInscribed}",
                    info: "# {$site->idNo}",
                    color: $this->app->siteIndex === $index ? 'cyan' : 'dim',
                    width: $closestSitesBoxWidth,
                    lines: $closestSitesBoxHeight
                ));
            });

            // Split sites into rows of 3 (half of total sites)
            $rows = $siteBoxes->chunk($this->app->numberOfClosestSitesToShow / ($this->app->numberOfClosestSitesToShow / $this->app->columnsPerRow));
            foreach ($rows as $row) {
                collect($row->shift())
                    ->zip(...$row)
                    ->map(fn ($lines) => $lines->implode(''))
                    ->each(function ($line) {
                        $this->lines[] = $line;
                    });
            }
        } else {
            $this->lines[] = 'No sites found near you.';
        }

        return $this->lines;
    }

    /**
     * Escape is only valid if the user has selected a site
     */
    protected function getInstructions(): string
    {
        return 'Arrow keys to navigate, Enter for fullscreen, q to quit';
    }
}
