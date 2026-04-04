<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final readonly class DowntimePeriodViewModel
{
    public function __construct(
        public int $id,
        public string $startedAt,
        public ?string $endedAt,
        public ?string $notes = null,
    ) {}

    public function startedAtDisplay(): string
    {
        return CarbonImmutable::parse($this->startedAt)->toDayDateTimeString();
    }

    public function endedAtDisplay(): string
    {
        if (! $this->endedAt) {
            return 'Ongoing';
        }

        return CarbonImmutable::parse($this->endedAt)->toDayDateTimeString();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'started_at_display' => $this->startedAtDisplay(),
            'ended_at_display' => $this->endedAtDisplay(),
            'notes' => filled($this->notes) ? Str::limit(strip_tags((string) $this->notes), 140) : null,
        ];
    }
}
