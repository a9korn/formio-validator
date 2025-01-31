## HOW TO USE

## ⚡ Quick setup ##

### Install library ###
```composer require a9korn/formio-validator```

### Script for test: ###

- copy data-example/schema.json - to your-project directory
- create file [script_name].php
```
<?php

use A9korn\FormioValidator\FormioBuilderValidator;

require __DIR__ . '/vendor/autoload.php';

$schema = file_get_contents(__DIR__ . '/data-example/schema-test.json');
$schema_array = json_decode($schema, true);

try {
    $validator = new FormioBuilderValidator($schema_array['components']);
    $errors = $validator->validateSchema();

    print_r($errors);
} catch (Exception $e) {
    print_r($e);
}

```

## How to Create Custom Validator ##

Create custom class **_implements IFormValidator_**
```php
<?php

namespace App;

use A9korn\FormioValidator\BaseValidator;
use A9korn\FormioValidator\IFormValidator;

class ButtonValidator extends BaseValidator implements IFormValidator
{
    protected array $requiredFields = ['key', 'type', 'input'];

    public function validate(array $component): array {
        $errors = parent::validate($component);

        return array_merge(
            $errors,
            $this->myValidator($component)
        );
    }

    public function myValidator(array $component): array {
        $errors = [];

        // TODO - validation logic

        return $errors;
    }
}

```

add custom validators array as argument
```php
    $myValidators = [
        'button' => \App\ButtonValidator::class
    ];

    $validator = new FormioBuilderValidator($schema_array['components'], $myValidators);
    $errors = $validator->validateSchema();
```

or register custom validator
```php
    $validator = new FormioBuilderValidator($schema_array['components']);
    $validator->registerValidator('button',\App\ButtonValidator::class);
```