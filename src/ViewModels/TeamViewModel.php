<?php

namespace Ziming\FilamentOhDear\ViewModels;

final readonly class TeamViewModel
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $role = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
        ];
    }
}
