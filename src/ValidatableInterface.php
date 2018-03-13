<?php

namespace VS\Validator;

/**
 * Interface ValidatableInterface
 * @package VS\Validator
 * @author Varazdat Stepanyan
 */
interface ValidatableInterface
{
    /**
     * @return array
     */
    public function getValidationRules(): array;

    /**
     * @return bool
     */
    public function autoValidate(): bool;
}