<?php

namespace A9korn\FormioValidator;

use App\BaseValidator;

class TextFieldValidator extends BaseValidator
{
    protected array $requiredFields = ['key', 'type', 'input'];

    public function validate(array $component): array
    {
        $errors = parent::validate($component);

        return $errors;
    }
}
