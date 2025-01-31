<?php

namespace A9korn\FormioValidator;

class ValidatorFactory
{
    /**
     * @var array<string, class-string<IFormValidator>>
     */
    private array $validators = [
        'baseValidator' => BaseValidator::class,
        'number'        => NumberValidator::class,
        'textfield'     => TextFieldValidator::class,
    ];

    private array $validatorInstances = [];
    private FormioBuilderValidator $formValidator;

    public function __construct(FormioBuilderValidator $formValidator) {
        $this->formValidator = $formValidator;
    }

    /**
     * @param array $component
     * @return IFormValidator|null
     */
    public function createValidator(array $component): ?IFormValidator {
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

    /**
     * @param string $type
     * @param class-string<IFormValidator> $validatorClass
     *
     * @return void
     */
    public function registerValidator(string $type, string $validatorClass): void {
        if (!is_subclass_of($validatorClass, IFormValidator::class)) {
            throw new \InvalidArgumentException("Validator class must implement ValidatorInterface");
        }
        $this->validators[$type] = $validatorClass;
        unset($this->validatorInstances[$type]);
    }

    /**
     * @param array<string, class-string<IFormValidator>> $validators
     *
     * @return void
     */
    public function registerValidators(array $validators): void {
        foreach ($validators as $type => $validatorClass) {
            $this->registerValidator($type, $validatorClass);
        }
    }
}
