<?php

namespace App\Services;

use Carbon\CarbonImmutable;

final readonly class Weather
{
    public function __construct(
        public string $city,
        public float $temperature,
        public CarbonImmutable $updatedAt,
    ) {}

    /** @param array{city: string, temperature: float, updated_at: string} $cached */
    public static function fromArray(array $cached): self
    {
        return new self(
            city: $cached['city'],
            temperature: $cached['temperature'],
            updatedAt: CarbonImmutable::parse($cached['updated_at']),
        );
    }

    /** @return array{city: string, temperature: float, updated_at: string} */
    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'temperature' => $this->temperature,
            'updated_at' => $this->updatedAt->format('Y-m-d\\TH:i:s.uP'),
        ];
    }
}
