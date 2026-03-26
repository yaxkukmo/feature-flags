<?php

namespace App\Tests\Application\Feature\Strategy;

use App\Application\Feature\Strategy\RuleEvaluationStrategy;
use App\Domain\Feature\Feature;
use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleOperator;
use App\Dto\RuleContext;
use PHPUnit\Framework\TestCase;

class RuleEvaluationStrategyTest extends TestCase
{
    public function testItReturnsFalseWhenCountryNotMetRule(): void
    {
        $ruleContext = new RuleContext( country: 'pl', userId: 100, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'country', value: 'us', operator: RuleOperator::EQUALS, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testItReturnsTrueWhenCountryMetRule(): void
    {
        $ruleContext = new RuleContext( country: 'pl', userId: 100, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'country', value: 'pl', operator: RuleOperator::EQUALS, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }

    public function testItReturnsTrueWhenNoRules(): void
    {

        $ruleContext = new RuleContext( country: 'pl', userId: 100, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [ ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }

    public function testItReturnsFalseWhenUserIdNotMetRuleLessThan(): void
    {

        $ruleContext = new RuleContext( country: 'pl', userId: 101, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::LESS_THAN, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testReturnsTrueWhenUserIdMetRuleLessThan(): void
    {
        $ruleContext = new RuleContext( country: 'pl', userId: 99, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::LESS_THAN, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }

    public function testReturnsFalseWhenUserIdNotMetRuleGreaterThan(): void
    {

        $ruleContext = new RuleContext( country: 'pl', userId: 99, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::GREATER_THAN, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testReturnsTrueWhenUserIdMetRuleGreaterThan(): void
    {
        $ruleContext = new RuleContext( country: 'pl', userId: 101, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::GREATER_THAN, id: 1)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);

    }

    public function testReturnsFalseWhenThereAreMoreRulesAndNotAllMetSuccess(): void
    {
        $ruleContext = new RuleContext( country: 'us', userId: 101, plan: 'plan');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::GREATER_THAN, id: 1),
                new Rule( type: 'country', value: 'pl', operator: RuleOperator::EQUALS, id: 2)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertFalse($actual);
    }

    public function testReturnsTrueWhenAllContextItemsMetRules(): void
    {
        $ruleContext = new RuleContext( country: 'pl', userId: 101, plan: 'b');
        $feature = new Feature(
            id: 1,
            rolloutPercentage: 100,
            name: 'someName',
            enabled: true,
            rules: [
                new Rule( type: 'user_id', value: '100', operator: RuleOperator::GREATER_THAN, id: 1),
                new Rule( type: 'country', value: 'pl', operator: RuleOperator::EQUALS, id: 2),
                new Rule( type: 'plan', value: 'b', operator: RuleOperator::EQUALS, id: 2)
            ]
        );
        $strategy = new RuleEvaluationStrategy($ruleContext, $feature);
        $actual = $strategy->evaluate();
        $this->assertTrue($actual);
    }
}
