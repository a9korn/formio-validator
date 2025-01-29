<?php

namespace A9korn\FormioValidator;

class NumberValidator extends BaseValidator implements ValidatorInterface
{
    protected array $requiredFields = ['key', 'type', 'input', 'delimiter'];

    public function validate(array $component): array
    {
        $errors = parent::validate($component);

        if (isset($component['requireDecimal']) && !is_bool($component['requireDecimal'])) {
            $errors[] = "'requireDecimal' must be boolean";
        }

        if (isset($component['delimiter']) && !is_bool($component['delimiter'])) {
            $errors[] = "'delimiter' must be boolean";
        }

        return $errors;
    }
}
