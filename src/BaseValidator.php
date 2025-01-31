<?php

namespace A9korn\FormioValidator;

class BaseValidator implements IFormValidator
{
    protected FormioBuilderValidator $formValidator;

    protected array $requiredFields = ['key', 'type'];

    public function __construct(FormioBuilderValidator $formValidator) {
        $this->formValidator = $formValidator;
    }

    public function validate(array $component): array {
        return array_merge(
            $this->validateRequired($component),
            $this->validateConditional($component),
            $this->validateDataType($component)
        );
    }

    protected function validateRequired(array $component): array {
        $errors = [];
        foreach ($this->requiredFields as $field) {
            if (!isset($component[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }
        return $errors;
    }

    protected function validateConditional(array $component): array {
        $errors = [];
        if (isset($component['conditional'])) {
            $conditional = $component['conditional'];

            if (!isset($conditional['when'])) {
                $errors[] = "Conditional missing 'when' field";
                return $errors;
            }

            $targetComponent = $this->formValidator->findComponent($conditional['when']);
            if ($targetComponent === null) {
                $errors[] = "Conditional references non-existent component: {$conditional['when']}";
            }
        }
        return $errors;
    }

    protected function validateDataType(array $component): array {
        $errors = [];
        if (isset($component['input']) && !is_bool($component['input'])) {
            $errors[] = "'input' field must be boolean";
        }
        if (isset($component['tableView']) && !is_bool($component['tableView'])) {
            $errors[] = "'tableView' field must be boolean";
        }
        if (isset($component['label']) && !is_string($component['label'])) {
            $errors[] = "'label' field must be string";
        }
        return $errors;
    }
}
