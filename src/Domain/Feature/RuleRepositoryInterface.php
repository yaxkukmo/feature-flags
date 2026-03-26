<?php

namespace App\Domain\Feature;


interface RuleRepositoryInterface
{
    public function delete(int $id):void;
    public function save(Rule $rule, string $name): void;

}
