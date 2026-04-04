<?php

namespace Ziming\FilamentOhDear\ViewModels;

use Carbon\CarbonImmutable;

final readonly class CertificateHealthViewModel
{
    /**
     * @param  array<int, CertificateCheckViewModel>  $checks
     */
    public function __construct(
        public ?string $issuer,
        public ?string $validFrom,
        public ?string $validUntil,
        public array $checks,
    ) {}

    public function isHealthy(): bool
    {
        return collect($this->checks)->every(
            static fn (CertificateCheckViewModel $check): bool => $check->passed,
        );
    }

    public function summary(): string
    {
        if ($this->validUntil) {
            $date = CarbonImmutable::parse($this->validUntil)->toFormattedDayDateString();

            if ($this->isHealthy()) {
                return "Valid until {$date}";
            }

            return "Attention required before {$date}";
        }

        return $this->isHealthy() ? 'Certificate looks healthy.' : 'Certificate checks need attention.';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'issuer' => $this->issuer,
            'valid_from' => $this->validFrom,
            'valid_until' => $this->validUntil,
            'summary' => $this->summary(),
            'is_healthy' => $this->isHealthy(),
            'checks' => array_map(
                static fn (CertificateCheckViewModel $check): array => $check->toArray(),
                $this->checks,
            ),
        ];
    }
}
