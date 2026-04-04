<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Illuminate\Support\Str;

final readonly class CertificateCheckViewModel
{
    public function __construct(
        public string $type,
        public bool $passed,
        public ?string $message = null,
    ) {}

    public function label(): string
    {
        return Str::headline(str_replace('-', '_', $this->type));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label(),
            'passed' => $this->passed,
            'message' => $this->message,
        ];
    }
}
