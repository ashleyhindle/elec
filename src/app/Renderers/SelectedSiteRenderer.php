<?php

declare(strict_types=1);

namespace App\Renderers;

use App\App;

class SelectedSiteRenderer extends AppRenderer
{
    public function __invoke(App $app): static
    {
        $this->app = $app;
        $theirHour = (int) ($this->app->ipResponse?->currentTime->format('H') ?? date('H'));

        $this->lines[] = sprintf('%s %s from %s! ❤️%s', $this->appropriateGreeting($theirHour), $this->app->name, $this->app->ipResponse->city, PHP_EOL);

        $this->renderSelectedSite($this->app->selectedSiteIndex);

        $this->renderInstructions();
        $this->output = implode(PHP_EOL, $this->lines);

        return $this;
    }

    /**
     * @return string[]
     */
    protected function renderSelectedSite(int $siteIndex): array
    {
        $currentSite = $this->app->closestSites[$siteIndex];
        $description = collect(explode(PHP_EOL, strip_tags($currentSite->shortDescriptionEn)))
            ->map(fn ($line) => mb_str_split($line, $this->app->totalWidth - 4))
            ->flatten()
            ->map(fn ($line) => trim($line))
            ->implode(PHP_EOL);

        $bodyLines = [];
        $bodyLines[] = $this->bold('Date of Inscription: ') . $currentSite->dateInscribed;
        $bodyLines[] = $this->bold('Criteria: ') . $currentSite->criteriaTxt;
        $bodyLines[] = $this->bold('Property : ') . $currentSite->areaHectares . ' ha';
        $bodyLines[] = $this->bold('Dossier: ') . $currentSite->idNo . $currentSite->revBis;
        $bodyLines[] = '';
        $bodyLines[] = $this->dim($currentSite->statesNameEn . ', ' . $currentSite->regionEn);
        $bodyLines[] = $this->dim($currentSite->latitude . ', ' . $currentSite->longitude);

        $headerText = 'All the details of your selected site: ' . $currentSite->nameEn;
        $headerLine = $this->bold($this->black($headerText));
        $headerPadding = str_repeat(' ', (int)floor(($this->app->terminal()->cols() - mb_strlen($headerText)) / 2));

        $this->lines[] = $this->bgGreen($headerPadding . $headerLine . $headerPadding);
        $this->lines[] = '';
        $this->lines[] = $this->getBoxOutput(
            title: $currentSite->nameEn,
            body: $description . PHP_EOL . PHP_EOL . implode(PHP_EOL, $bodyLines),
            footer: '',
            color: 'green',
            info: 'https://whc.unesco.org/en/list/' . (string) $currentSite->idNo,
            width: $this->app->totalWidth,
            lines: null
        );

        return $this->lines;
    }

    /**
     * Escape is only valid if the user has selected a site
     */
    protected function getInstructions(): string
    {
        return 'Escape to go back, q to quit';
    }
}
