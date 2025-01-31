<?php

namespace A9korn\FormioValidator;

class TextFieldValidator extends BaseValidator implements IFormValidator
{
    protected array $requiredFields = ['key', 'type', 'input'];

    public function validate(array $component): array
    {
        $errors = parent::validate($component);

        return $errors;
    }
}
