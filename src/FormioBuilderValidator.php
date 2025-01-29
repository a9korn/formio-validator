<?php

namespace A9korn\FormioValidator;

class FormioBuilderValidator
{
    private array $components;
    private ValidatorFactory $validatorFactory;

    public function __construct(array $components) {
        $this->components = $components;
        $this->validatorFactory = new ValidatorFactory($this);
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

    private function validate(): array {
        $errors = [];

        $this->eachComponent(function ($component, $path) use (&$errors) {
            $validator = $this->validatorFactory->createValidator($component);

            if ($validator !== null) {
                $componentErrors = $validator->validate($component);
                if ( !empty($componentErrors) ) {
                    $errors[$path] = $componentErrors;
                }
            }
        });

        return $errors;
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

    public function validateSchema(): array
    {
        if (!is_array($this->components)) {
            return ['Invalid schema: components must be an array'];
        }

        return $this->validate();
    }
}
