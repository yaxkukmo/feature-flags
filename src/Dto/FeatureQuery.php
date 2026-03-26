<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FeatureQuery
{
    public function __construct(
        #[Assert\Positive]
        public readonly int $page = 1,
        #[Assert\Positive]
        public readonly int $limit = 50,
        #[Assert\Length(min: 2, max: 255)]
        public readonly ?string $search = null,
        #[Assert\Choice(choices: ['name', 'enabled', 'rolloutPercentage'])]
        public readonly string $sortBy = 'name',
        #[Assert\Choice(choices: ['ASC', 'DESC'])]
        public readonly string $sortDir = 'ASC'

    ) { }
}
