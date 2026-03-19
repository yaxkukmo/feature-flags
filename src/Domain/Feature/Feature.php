<?php

namespace App\Domain\Feature;

final class Feature
{
    public function __construct(
        private int $id,
        private string $name,
        private bool $enabled = false,
        private array $rules = [],
        private ?int $rolloutPercentage = null
    ) {}

    public function enable(): void {
        $this->enabled = true;
    }

    public function disable(): void {
        $this->enabled = false;
    }

    public function id(): int {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function rolloutPercentage(): ?int {
        return $this->rolloutPercentage;
    }

    /** @return Rule[] */
    public function rules(): array {
        return $this->rules;
    }
}
