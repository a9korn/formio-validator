## HOW TO USE

## ⚡ Quick setup ##

### Install library ###
```composer require a9korn/formio-validator```

### Create test script: ###

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
