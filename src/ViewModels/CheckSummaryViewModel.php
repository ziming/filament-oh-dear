<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Illuminate\Support\Str;

final readonly class CheckSummaryViewModel
{
    public function __construct(
        public string $type,
        public ?string $result,
        public ?string $summary,
    ) {}

    public function label(): string
    {
        return match ($this->type) {
            'uptime' => 'Uptime',
            'performance' => 'Performance',
            'certificate_health' => 'Certificate',
            'broken_links' => 'Broken links',
            default => Str::headline(str_replace('-', '_', $this->type)),
        };
    }

    public function isHealthy(): bool
    {
        return $this->result === 'succeeded';
    }

    public function isProblematic(): bool
    {
        return in_array($this->result, ['warning', 'failed', 'errored-or-timed-out'], true);
    }

    public function color(): string
    {
        return match ($this->result) {
            'succeeded' => 'success',
            'warning' => 'warning',
            'failed', 'errored-or-timed-out' => 'danger',
            default => 'gray',
        };
    }

    public function displaySummary(string $fallback = 'No data available.'): string
    {
        return filled($this->summary) ? (string) $this->summary : $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label(),
            'result' => $this->result,
            'summary' => $this->summary,
            'display_summary' => $this->displaySummary(),
            'color' => $this->color(),
            'is_healthy' => $this->isHealthy(),
            'is_problematic' => $this->isProblematic(),
        ];
    }
}
