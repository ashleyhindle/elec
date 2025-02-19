<?php

declare(strict_types=1);

namespace App;

use Laravel\Prompts\Prompt;
use App\Renderers\HomeRenderer;
use Chewie\Concerns\RegistersRenderers;

class App extends Prompt
{
    use RegistersRenderers;

    /*
     * Constructor
     */
    public function __construct(public string $ip = '')
    {
        $this->registerRenderer(HomeRenderer::class);
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): true
    {
        return true;
    }

    public function isIpInternal(?string $ip = null): bool
    {
        // filter_var returns false if the IP is in a private range, otherwise it returns the IP
        // so if the result is false, the IP is private/internal or invalid
        return filter_var(
            $ip ?? $this->ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
