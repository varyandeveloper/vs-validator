<?php

namespace VS\Validator;

/**
 * Interface ValidatorInterface
 * @package VS\Validator
 * @author Varazdat Stepanyan
 */
interface ValidatorInterface
{
    /**
     * @param string $name
     * @param $value
     * @return ValidatorInterface
     */
    public function setAttribute(string $name, $value): ValidatorInterface;

    /**
     * @param string $lang
     * @return void
     */
    public static function setLang(string $lang): void;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @param ValidatableInterface|null $request
     * @return ValidatorInterface
     */
    public function run(ValidatableInterface $request = null): ValidatorInterface;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @param array $rules
     * @return ValidatorInterface
     */
    public function setRules(array $rules): ValidatorInterface;

    /**
     * @param string $fieldName
     * @param string $label
     * @param string[] ...$rules
     * @return ValidatorInterface
     */
    public function addRule(string $fieldName, string $label, string ...$rules): ValidatorInterface;

    /**
     * @param $value
     * @return bool
     */
    public function required($value): bool;

    /**
     * @param $value
     * @param string $filedName
     * @return bool
     */
    public function requiredWhenEmpty($value, string $filedName): bool;

    /**
     * @param $value
     * @return bool
     */
    public function macAddress($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function regexp($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function url($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function ip($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function float($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function email($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function string($value): bool;

    /**
     * @param $value
     * @return bool
     */
    public function int($value): bool;

    /**
     * @param $value
     * @param int $length
     * @return bool
     */
    public function max($value, int $length): bool;

    /**
     * @param $value
     * @param int $length
     * @return bool
     */
    public function min($value, int $length): bool;

    /**
     * @param $value
     * @param string $argument
     * @return bool
     */
    public function between($value, string $argument): bool;

    /**
     * @param $value
     * @param string $fieldName
     * @return bool
     */
    public function matchWith($value, string $fieldName): bool;
}