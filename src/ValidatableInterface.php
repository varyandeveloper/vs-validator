<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 3/13/2018
 * Time: 11:55 PM
 */

namespace VS\Validator;

/**
 * Interface ValidatableInterface
 * @package VS\Validator
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