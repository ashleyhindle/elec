<?php

declare(strict_types=1);

namespace App\Renderers;

use App\App;
use App\IpApi;
use Chewie\Concerns\Aligns;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use Laravel\Prompts\Themes\Default\Renderer;

class HomeRenderer extends Renderer
{
    use Aligns;

    /**
     * Invoke
     */
    public function __invoke(App $app): static
    {
        $width = $app->terminal()->cols() - 8;
        $height = $app->terminal()->lines() - 2;

        $ipIsInternal = $app->isIpInternal();
        $this->line('Your IP is ' . $app->ip . ' right? ' . ($ipIsInternal ? '🤔' : '👍'));

        $name = text(
            label: 'What\'s your first name?',
            required: true,
            validate: fn (string $value) => match (true) {
                mb_strlen($value) < 2 => 'Your name should probably be more than 1 character, no?',
                mb_strlen($value) > 255 => 'Are you friends with Bobby Tables?',
                default => null
            },
            hint: 'Just your first name please or we may upset the coding deities.'
        );

        $response = spin(
            message: 'Seeing if we like it...',
            callback: fn () => (new IpApi)->get($app->ip)
        );

        $lines = [
            '👋 Howdy ' . $name . '!',
            '',
            'Welcome to ELEC where we share a tiny amount of functionality around World Heritage Sites.',
            '',
            '❤️',
            'Your IP info: ' . print_r($response, true),
        ];

        $this->center($lines, $width, $height)
            ->each($this->line(...));

        return $this;
    }
}
