<?php

namespace VS\Validator;

use VS\Request\RequestInterface;

/**
 * Class Validator
 * @package VS\Validator
 * @author Varazdat Stepanyan
 */
class Validator implements ValidatorInterface
{
    /**
     * @var array $attributes
     */
    private $attributes = [];
    /**
     * @var RequestInterface $Request
     */
    private $Request;
    /**
     * @var string $type
     */
    private $type;
    /**
     * @var bool $trim
     */
    private $trim = true;
    /**
     * @var array $rules
     */
    private $rules = [];
    /**
     * @var array $errors
     */
    private $errors = [];
    /**
     * @var array $values
     */
    private $values = [];
    /**
     * @var string $lang
     */
    private static $lang = 'en';

    /**
     * Validator constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->Request = $request;
    }

    /**
     * @param string $fieldName
     * @param string $label
     * @param string[] ...$rules
     * @return ValidatorInterface
     */
    public function addRule(string $fieldName, string $label, string ...$rules): ValidatorInterface
    {
        $this->rules[] = [
            $fieldName => [
                'label' => $label,
                'rules' => implode('|', $rules)
            ]
        ];

        return $this;
    }

    /**
     * @param array $rules
     * @return ValidatorInterface
     */
    public function setRules(array $rules): ValidatorInterface
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param ValidatableInterface|null $request
     * @return ValidatorInterface
     * @throws ValidatorException
     * @throws \Exception
     */
    public function run(ValidatableInterface $request = null): ValidatorInterface
    {
        $autoValidate = false;
        if (!count($this->rules) && null !== $request) {
            $this->rules = $request->getValidationRules();
            $autoValidate = $request->autoValidate();
        }

        $this->prepareValidation();

        if ($autoValidate && !$this->isValid()) {
            //TODO::Add logic to do some action for auto Validatable objects
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function required($value): bool
    {
        return !empty($value);
    }

    /**
     * @param $value
     * @param string $filedName
     * @return bool
     */
    public function requiredWhenEmpty($value, string $filedName): bool
    {
        $this->attributes[':otherField'] = ucwords(str_replace('_', ' ', $filedName));
        if (empty($this->Request->get($filedName))) {
            return $this->required($value);
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function macAddress($value): bool
    {
        $this->type = 'string';
        return filter_var($value, FILTER_VALIDATE_MAC) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function regexp($value): bool
    {
        $this->type = 'string';
        return filter_var($value, FILTER_VALIDATE_REGEXP) !== false || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function url($value): bool
    {
        $this->type = 'string';
        return filter_var($value, FILTER_VALIDATE_URL) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function ip($value): bool
    {
        $this->type = 'string';
        return filter_var($value, FILTER_VALIDATE_IP) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function float($value): bool
    {
        $this->type = 'float';
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function email($value): bool
    {
        $this->type = 'string';
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function string($value): bool
    {
        $this->type = 'string';
        return is_string($value) || trim($value) === '';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function int($value): bool
    {
        $this->type = 'int';
        return filter_var($value, FILTER_VALIDATE_INT) !== FALSE || empty($value);
    }

    /**
     * @param mixed $value
     * @param int $length
     * @return bool
     */
    public function max($value, int $length): bool
    {
        $this->attributes[':length'] = $length;
        if ($this->type === 'array') {
            return count($value) <= $length;
        } elseif ($this->type === 'int') {
            return (int)$value <= $length;
        } elseif ($this->type === 'float') {
            return (float)$value <= $length;
        }
        return strlen($value) <= $length;
    }

    /**
     * @param mixed $value
     * @param int $length
     * @return bool
     */
    public function min($value, int $length): bool
    {
        $this->attributes[':length'] = $length;
        if ($this->type === 'array') {
            return count($value) >= $length;
        } elseif ($this->type === 'int') {
            return (int)$value >= $length;
        } elseif ($this->type === 'float') {
            return (float)$value >= $length;
        }
        return strlen($value) >= $length;
    }

    /**
     * @param mixed $value
     * @param string $argument
     * @return bool
     * @throws ValidatorException
     */
    public function between($value, string $argument): bool
    {
        if (strpos($argument, ',') === FALSE) {
            throw new ValidatorException(sprintf(
                ValidatorConstants::getMessage(ValidatorConstants::INVALID_RULE_ARGUMENTS_CODE),
                __FUNCTION__,
                'min,max'
            ),
                ValidatorConstants::INVALID_RULE_ARGUMENTS_CODE
            );
        }
        [$min, $max] = explode(',', $argument);

        $this->attributes[':min'] = $min;
        $this->attributes[':max'] = $max;

        if ($this->type === 'array') {
            $count = count($value);
            return $count >= $min && $count <= $max;
        } elseif ($this->type === 'int') {
            $val = (int)$value;
            return $val >= $min && $val <= $max;
        } elseif ($this->type === 'float') {
            $float = (float)$value;
            return $float >= $min && $float <= $max;
        }
        $length = strlen($value);
        return $length >= $min && $length <= $max;
    }

    /**
     * @param mixed $value
     * @param string $fieldName
     * @return bool
     */
    public function matchWith($value, string $fieldName): bool
    {
        $this->attributes[':otherField'] = $fieldName;
        return $value === (string)($this->trim ? trim($this->Request->get($fieldName)) : $this->Request->get($fieldName));
    }

    /**
     * @throws ValidatorException
     * @throws \Exception
     */
    protected function prepareValidation()
    {
        foreach ($this->rules as $fieldName => $rule) {
            if (is_array($rule)) {
                if (empty($rule[0]) && empty($rule['label'])) {
                    throw new ValidatorException(
                        ValidatorConstants::getMessage(ValidatorConstants::INVALID_LABEL_ARGUMENT_CODE),
                        ValidatorConstants::INVALID_LABEL_ARGUMENT_CODE
                    );
                }
                $label = $rule['label'] ?? $rule[0];

                if (empty($rule[1]) && empty($rule['rules'])) {
                    throw new ValidatorException(
                        ValidatorConstants::getMessage(ValidatorConstants::INVALID_RULES_ARGUMENT_CODE),
                        ValidatorConstants::INVALID_RULES_ARGUMENT_CODE
                    );
                }
                $rules = explode('|', $rule['rules'] ?? $rule[1]);

            } else {
                $label = ucwords(implode(' ', explode('_', $fieldName)));

                if (empty($rule)) {
                    throw new ValidatorException(
                        ValidatorConstants::getMessage(ValidatorConstants::INVALID_RULES_ARGUMENT_INLINE_CODE),
                        ValidatorConstants::INVALID_RULES_ARGUMENT_INLINE_CODE
                    );
                }

                $rules = explode('|', $rule);
            }

            $this->validate($fieldName, $label, $rules);
        }
    }

    /**
     * @param string $lang
     * @return void
     */
    public static function setLang(string $lang): void
    {
        self::$lang = $lang;
    }

    /**
     * @param string $ruleName
     * @param string $label
     * @param string $fieldName
     * @param bool $checkReverse
     */
    protected function resolveResult(string $ruleName, string $label, string $fieldName, bool $checkReverse)
    {
        $messages = ValidatorConstants::getValidationMessages(self::$lang);
        $this->attributes[':fieldName'] = $label;

        if ($checkReverse) {
            $ruleName = "!$ruleName";
        }

        if (isset($messages[$ruleName])) {
            $this->errors[$fieldName] = str_replace(array_keys($this->attributes), array_values($this->attributes), $messages[$ruleName]);
        } else {
            $this->errors[$fieldName] = $ruleName;
        }
    }

    /**
     * @param string $ruleName
     * @throws ValidatorException
     */
    protected function validateRule(string $ruleName)
    {
        if (!method_exists($this, $ruleName)) {
            throw new ValidatorException(sprintf(
                ValidatorConstants::getMessage(ValidatorConstants::INVALID_RULE_CODE),
                $ruleName
            ), ValidatorConstants::INVALID_RULE_CODE);
        }
    }

    /**
     * @param string $rule
     * @return array
     */
    protected function getRuleAndArgument(string $rule): array
    {
        $argument = null;

        if (strpos($rule, ':') !== FALSE) {
            [$ruleName, $argument] = explode(':', $rule);
            $argument = str_replace(['[', ']'], '', $argument);
        } else {
            $ruleName = $rule;
        }

        return [$ruleName, $argument];
    }

    /**
     * @param string $ruleName
     * @param $checkReverse
     */
    protected function resolveRuleNameAndRevers(string &$ruleName, &$checkReverse)
    {
        if (isset($ruleName[0]) && $ruleName[0] === '!') {
            $checkReverse = true;
            $ruleName = str_replace('!', '', $ruleName);
        }
    }

    /**
     * @param string $fieldName
     * @param string $label
     * @param array $rules
     * @throws ValidatorException
     */
    private function validate(string $fieldName, string $label, array $rules)
    {
        $this->trim = !in_array('!trim', $rules, true);
        $checkReverse = false;
        $value = $this->trim ? trim($this->Request->get($fieldName)) : $this->Request->get($fieldName);

        foreach ($rules as $rule) {

            [$ruleName, $argument] = $this->getRuleAndArgument($rule);

            $this->resolveRuleNameAndRevers($ruleName, $checkReverse);
            $this->validateRule($ruleName);

            $this->values[$fieldName] = $value;
            $result = $checkReverse ? $this->{$ruleName}($value, $argument) : !$this->{$ruleName}($value, $argument);

            if ($result) {
                $this->resolveResult($ruleName, $label, $fieldName, $checkReverse);
                break;
            }
        }
    }
}