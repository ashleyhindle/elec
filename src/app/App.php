<?php

declare(strict_types=1);

namespace App;

use SQLite3;
use App\Whc\Site;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use App\Renderers\ClosestSitesRenderer;
use App\Renderers\SelectedSiteRenderer;
use Chewie\Concerns\RegistersRenderers;
use function Laravel\Prompts\multiselect;

class App extends Prompt
{
    use RegistersRenderers;

    /** @var Site[] */
    public array $closestSites = [];
    public int $siteIndex = 0;
    public ?int $selectedSiteIndex = null;
    public int $numberOfClosestSitesToShow = 6;
    public int $columnsPerRow = 3;
    public int $totalWidth;
    public int $totalHeight;
    public ?IpApiResponse $ipResponse = null;
    public string $name = '';
    /** @var string[] */
    public array $categories = ['Cultural', 'Natural', 'Mixed'];

    public function __construct(public SQLite3 $db, public string $ip = '')
    {
        $this->totalWidth = $this->terminal()->cols() - 4;
        $this->totalHeight = $this->terminal()->lines() - 2;

        $this->registerRenderer(ClosestSitesRenderer::class);
    }

    public function run(): self
    {
        $this->ipResponse = $this->geoip();
        $this->name = $this->getName();
        $this->categories = $this->getCategories();
        $this->closestSites = $this->getClosestSites();

        confirm(
            label: 'Do you accept this is super cool?',
            required: 'You must accept the awesomeness to continue.'
        );

        $this->listenForKeys();

        return $this;
    }

    protected function geoip(): IpApiResponse
    {
        return spin(
            message: 'Geolocating with my personal geostationary satellite...',
            callback: function () {
                usleep(950000);

                return (new IpApi($_ENV['IPGEOLOCATION_API_KEY']))->get($this->ip);
            }
        );
    }

    protected function getName(): string
    {
        return text(
            label: 'What\'s your first name?',
            required: true,
            validate: fn(string $value) => match (true) {
                mb_strlen($value) < 2 => 'Your name should probably be more than 1 character, no?',
                mb_strlen($value) > 255 => 'Are you friends with Bobby Tables?',
                default => null
            },
            hint: 'Just your first name please or we may upset the coding deities.'
        );
    }

    /**
     * @return string[]
     */
    protected function getCategories(): array
    {
        $query = $this->db->query('SELECT DISTINCT category FROM sites order by category asc');
        $uniqueCategories = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $uniqueCategories[] = $row['category'];
        }

        $categories = multiselect(
            label: 'Which categories of sites should we show you?',
            // placeholder: 'E.g. Cultural',
            options: $uniqueCategories,
            default: $uniqueCategories,
            // hint: 'If you want to see them all, just press enter.',
            required: 'You must select at least one category.',
        );

        if (empty($categories)) {
            $categories = $uniqueCategories;
        }

        return $categories;
    }

    /**
     * @return Site[]
     */
    protected function getClosestSites(): array
    {
        return spin(
            message: 'Finding World Heritage Sites closest to you...',
            callback: function () {
                usleep(900000);

                return $this->getSitesNear($this->ipResponse->lat ?? 0, $this->ipResponse->lon ?? 0, $this->numberOfClosestSitesToShow);
            }
        );
    }

    public function listenForKeys(): void
    {
        $this->on('key', function ($key) {
            if ($key[0] === "\e") {
                // Only support navigation if we're not showing a selected site
                match ($key) {
                    Key::RIGHT, Key::RIGHT_ARROW => is_null($this->selectedSiteIndex) ? $this->nextSiteIndex() : null,
                    Key::LEFT, Key::LEFT_ARROW => is_null($this->selectedSiteIndex) ? $this->previousSiteIndex() : null,
                    Key::UP, Key::UP_ARROW => is_null($this->selectedSiteIndex) ? $this->previousRowSiteIndex() : null,
                    Key::DOWN, Key::DOWN_ARROW => is_null($this->selectedSiteIndex) ? $this->nextRowSiteIndex() : null,
                    Key::ESCAPE => $this->deselectSite(),
                    default => null,
                };

                return;
            }

            // Keys may be buffered
            foreach (mb_str_split($key) as $key) {
                if ($key === Key::ENTER) {
                    $this->selectSite();
                }

                match ($key) {
                    'q' => $this->quit(),
                    default => null,
                };
            }
        });
    }

    protected function quit(): void
    {
        static::terminal()->exit();
    }

    protected function deselectSite(): void
    {
        $this->selectedSiteIndex = null;
        $this->registerRenderer(ClosestSitesRenderer::class);
    }

    protected function selectSite(): void
    {
        $this->selectedSiteIndex = $this->siteIndex;
        $this->registerRenderer(SelectedSiteRenderer::class);
    }

    protected function nextSiteIndex(): int
    {
        $this->siteIndex = $this->siteIndex === count($this->closestSites) - 1 ? 0 : $this->siteIndex + 1;

        return $this->siteIndex;
    }

    protected function previousSiteIndex(): int
    {
        $this->siteIndex = $this->siteIndex === 0 ? count($this->closestSites) - 1 : $this->siteIndex - 1;

        return $this->siteIndex;
    }

    protected function nextRowSiteIndex(): int
    {
        // Move down 3 positions (to next row)
        $newIndex = $this->siteIndex + $this->columnsPerRow;


        // If we've gone past the end, wrap to the top row in the same column
        if ($newIndex >= count($this->closestSites)) {
            $newIndex = $this->siteIndex % $this->columnsPerRow; // Keep the same column position
        }

        $this->siteIndex = $newIndex;

        return $this->siteIndex;
    }

    protected function previousRowSiteIndex(): int
    {
        // Move up 3 positions (to previous row)
        $newIndex = $this->siteIndex - $this->columnsPerRow;

        // If we've gone above the first row, wrap to the bottom
        if ($newIndex < 0) {
            // Calculate how many complete rows we have
            $numRows = (int) ceil(count($this->closestSites) / $this->columnsPerRow);
            // Get the same column position in the last row
            $newIndex = ($numRows - 1) * $this->columnsPerRow + ($this->siteIndex % $this->columnsPerRow);
            // If this position doesn't exist (last row might not be complete),
            // move up one row
            if ($newIndex >= count($this->closestSites)) {
                $newIndex -= $this->columnsPerRow;
            }
        }

        $this->siteIndex = $newIndex;

        return $this->siteIndex;
    }

    public function value(): true
    {
        return true;
    }

    /**
     * @return Site[]
     */
    public function getSitesNear(float $lat, float $lon, int $limit = 3): array
    {
        $cos_lat_2 = (float) pow(cos($lat * pi() / 180), 2);

        $categoryPlaceholders = implode(',', array_map(fn($category) => ':' . mb_strtolower($category), $this->categories));

        $sql = "SELECT *, ((:lat-latitude)*(:lat-latitude)) + ((:lon-longitude)*(:lon-longitude)) as distance FROM sites WHERE category in ($categoryPlaceholders) ORDER BY ((:lat-latitude)*(:lat-latitude)) + ((:lon-longitude)*(:lon-longitude)*:cos_lat_2) ASC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lat', $lat);
        $stmt->bindValue(':lon', $lon);
        $stmt->bindValue(':cos_lat_2', $cos_lat_2);
        $stmt->bindValue(':limit', $limit);

        foreach ($this->categories as $category) {
            $stmt->bindValue(':' . mb_strtolower($category), $category);
        }

        $result = $stmt->execute();
        $this->closestSites = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->closestSites[] = Site::fromRow($row);
        }

        return $this->closestSites;
    }
}
