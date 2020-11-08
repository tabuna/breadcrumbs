<?php

declare(strict_types=1);

namespace Tabuna\Breadcrumbs;

use JsonSerializable;

class Crumb implements JsonSerializable
{
    /**
     * The crumb title.
     *
     * @var string
     */
    protected $title;

    /**
     * The crumb URL.
     *
     * @var string|null
     */
    protected $url;

    /**
     * Construct the crumb instance.
     *
     * @param string      $title
     * @param string|null $url
     *
     */
    public function __construct(string $title, string $url = null)
    {
        $this->title = $title;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'url'   => $this->url(),
        ];
    }

    /**
     * Get the crumb title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Get the crumb URL.
     *
     * @return string|null
     */
    public function url(): ?string
    {
        return $this->url;
    }
}
