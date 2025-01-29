<?php
namespace A9korn\FormioValidator;

interface ValidatorInterface
{
    public function validate(array $component): array;
}
