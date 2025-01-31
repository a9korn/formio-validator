<?php
namespace A9korn\FormioValidator;

interface IFormValidator
{
    /**
     * @param array $component
     *
     * @return string[] - errors
     */
    public function validate(array $component): array;
}
