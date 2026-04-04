<?php

namespace Ziming\FilamentOhDear\ViewModels;

final readonly class UserViewModel
{
    /**
     * @param  array<int, TeamViewModel>  $teams
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $photoUrl,
        public array $teams,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photoUrl,
            'teams' => array_map(
                static fn (TeamViewModel $team): array => $team->toArray(),
                $this->teams,
            ),
        ];
    }
}
