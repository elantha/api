<?php

namespace Grizmar\Api\Validators;

use Validator;

trait RequestValidator
{
    private $validationRules = [];

    abstract  protected function initializeValidationRules(): self;

    final protected function setValidationRules(array $rules): self
    {
        $this->validationRules = array_merge($this->validationRules, $rules);

        return $this;
    }

    final protected function appendValidationRule(string $key, string $rule): self
    {
        $currentRule = array_get($this->validationRules, $key, false);
        $newRule = $currentRule
            ? $currentRule . '|' . $rule
            : $rule;

        array_set($this->validationRules, $key, $newRule);

        return $this;
    }
}
