<?php

namespace A9korn\FormioValidator;

class FormioBuilderValidator
{
    private array $components;
    private ValidatorFactory $validatorFactory;

    /**
     * @param array $components
     * @param array<string, class-string<IFormValidator>> $validators
     */
    public function __construct(array $components, array $validators = []) {
        $this->components = $components;
        $this->validatorFactory = new ValidatorFactory($this);

        if (!empty($validators)) {
            $this->registerValidators($validators);
        }
    }

    public function findComponent(string $key): ?array {
        $foundComponent = null;

        $this->eachComponent(function ($component, $currentPath) use ($key, &$foundComponent) {
            if ($currentPath === $key) {
                $foundComponent = $component;
            }
        });

        return $foundComponent;
    }

    private function eachComponent(callable $callback): void {
        $this->iterateComponents($this->components, $callback);
    }

    private function iterateComponents(array $components, callable $callback, string $path = ''): void {
        foreach ($components as $component) {
            $currentPath = $path ? $path . '.' . $component['key'] : $component['key'];

            $callback($component, $currentPath);

            if (!empty($component['components']) && is_array($component['components'])) {
                $this->iterateComponents($component['components'], $callback, $currentPath);
            }

            if (!empty($component['columns']) && is_array($component['columns'])) {
                foreach ($component['columns'] as $column) {
                    if (!empty($column['components'])) {
                        $this->iterateComponents($column['components'], $callback, $path);
                    }
                }
            }

            if (isset($component['rows']) && is_array($component['rows'])) {
                foreach ($component['rows'] as $row) {
                    foreach ($row as $cell) {
                        if (!empty($cell['components'])) {
                            $this->iterateComponents($cell['components'], $callback, $path);
                        }
                    }
                }
            }
        }
    }

    public function validateSchema(): array {
        if (empty($this->components)) {
            return ['Invalid schema: components must be an array'];
        }

        return $this->validate();
    }

    private function validate(): array {
        $errors = [];

        $this->eachComponent(function ($component, $path) use (&$errors) {
            $validator = $this->validatorFactory->createValidator($component);

            if ($validator !== null) {
                $componentErrors = $validator->validate($component);
                if (!empty($componentErrors)) {
                    $errors[$path] = $componentErrors;
                }
            }
        });

        return $errors;
    }

    /**
     * @param array<string, class-string<IFormValidator>> $validators
     *
     * @return void
     */
    public function registerValidators(array $validators): void {
        $this->validatorFactory->registerValidators($validators);
    }

    /**
     * @param string $type
     * @param class-string<IFormValidator> $validatorClass
     *
     * @return void
     */
    public function registerValidator(string $type, string $validatorClass): void {
        $this->validatorFactory->registerValidator($type, $validatorClass);
    }
}
