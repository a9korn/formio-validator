<?php

namespace A9korn\FormioValidator;

class ValidatorFactory
{
    private array $validators = [
        'baseValidator' => BaseValidator::class,
        'number'        => NumberValidator::class,
        'textfield'     => TextFieldValidator::class,
        //        'datagrid' => DataGridValidator::class,
        //        'table' => TableValidator::class,
        //        'columns' => ColumnsValidator::class,
    ];

    private array $validatorInstances = [];
    private FormioBuilderValidator $formValidator;

    public function __construct(FormioBuilderValidator $formValidator) {
        $this->formValidator = $formValidator;
    }

    public function createValidator(array $component): ?ValidatorInterface {
        if (!isset($component['type'])) {
            return null;
        }

        $type = $component['type'];

        if (!isset($this->validators[$type])) {
            $type = 'baseValidator';
        }

        if (isset($this->validatorInstances[$type])) {
            return $this->validatorInstances[$type];
        }

        if (isset($this->validators[$type])) {
            $validatorClass = $this->validators[$type];
            $this->validatorInstances[$type] = new $validatorClass($this->formValidator);
            return $this->validatorInstances[$type];
        }

        return null;
    }

    public function registerValidator(string $type, string $validatorClass): void {
        if (!is_subclass_of($validatorClass, ValidatorInterface::class)) {
            throw new \InvalidArgumentException("Validator class must implement ValidatorInterface");
        }
        $this->validators[$type] = $validatorClass;
        unset($this->validatorInstances[$type]);
    }
}
