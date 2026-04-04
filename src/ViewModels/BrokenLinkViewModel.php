<?php

namespace Ziming\FilamentOhDear\ViewModels;

final readonly class BrokenLinkViewModel
{
    public function __construct(
        public ?int $statusCode,
        public string $crawledUrl,
        public string $foundOnUrl,
        public string $linkText,
        public bool $internal,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode,
            'crawled_url' => $this->crawledUrl,
            'found_on_url' => $this->foundOnUrl,
            'link_text' => $this->linkText,
            'internal' => $this->internal,
        ];
    }
}
