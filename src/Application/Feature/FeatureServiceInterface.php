<?php

namespace App\Application\Feature;

use App\Domain\Feature\Feature;
use App\Dto\RuleContext;

interface FeatureServiceInterface
{
    public function isEnabled(string $name, RuleContext $context): bool;
    public function getFeatureByName(string $name): Feature;
}
