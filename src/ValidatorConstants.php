<?php

namespace VS\Validator;

/**
 * Class ValidatorConstants
 * @package VS\Validator
 * @author Varazdat Stepanyan
 */
class ValidatorConstants
{
    const INVALID_RULE_ARGUMENTS_CODE = 1;
    const INVALID_LABEL_ARGUMENT_CODE = 2;
    const INVALID_RULES_ARGUMENT_CODE = 3;
    const INVALID_RULE_CODE = 4;
    const INVALID_RULES_ARGUMENT_INLINE_CODE = 5;

    const INVALID_RULE_ARGUMENTS_MESSAGE = 'The rule %s requires structure %s';
    const INVALID_LABEL_ARGUMENT_MESSAGE = 'Required element index [0] or key [\'label\'] missing';
    const INVALID_RULES_ARGUMENT_MESSAGE = 'Required element index [1] or key [\'rules\'] missing';
    const INVALID_RULE_MESSAGE = 'Invalid validation rule %s';
    const INVALID_RULES_ARGUMENT_INLINE_MESSAGE = 'Required structure is \'fieldName\' => \'rule1|rule2|...|ruleN\'';

    protected const DEFAULT_LANG = 'en';

    protected const MESSAGES = [
        self::DEFAULT_LANG => [
            self::INVALID_RULE_ARGUMENTS_CODE => self::INVALID_RULE_ARGUMENTS_MESSAGE,
            self::INVALID_LABEL_ARGUMENT_CODE => self::INVALID_LABEL_ARGUMENT_MESSAGE,
            self::INVALID_RULES_ARGUMENT_CODE => self::INVALID_RULES_ARGUMENT_MESSAGE,
            self::INVALID_RULE_CODE => self::INVALID_RULE_MESSAGE,
            self::INVALID_RULES_ARGUMENT_INLINE_CODE => self::INVALID_RULES_ARGUMENT_INLINE_MESSAGE,
        ]
    ];

    protected const VALIDATION_MESSAGES = [
        self::DEFAULT_LANG => [
            'required' => 'The field :fieldName is required.',
            'requiredWhenEmpty' => 'The field :fieldName is required when the field :otherField is empty.',
            'max' => 'Maximum length of the field :fieldName characters should be :length.',
            'min' => 'Minimum length of the field :fieldName characters should be :length.',
            'between' => 'The field :fieldName should contain at less :min and no more then :max characters.',
            'email' => 'The field :fieldName should be valid email.',
            'ip' => 'The field :fieldName should be valid IP address.',
            'password' => 'The field :fieldName should contain at least 8 characters, one lowercase letter, one uppercase letter, one number and one non-word character.',
            'url' => 'The field :fieldName should be valid url.',
            'int' => 'The field :fieldName should be of type integer.',
            'string' => 'The field :fieldName should be of type string.',
            'float' => 'The field :fieldName should be of type float.',
            'regexp' => 'The field :fieldName should contain valid regular expression.',
            'macAddress' => 'The field :fieldName should contain valid mac address.',
            'matchWith' => 'The field :fieldName\'s value should match with value of the field :otherField.',
            '!matchWith' => 'The field :fieldName\'s value should not match with value of the field :otherField.',
        ]
    ];

    /**
     * @var array $messages
     */
    protected static $messages = [];
    /**
     * @var array $validationMessages
     */
    protected static $validationMessages = [];

    /**
     * @var callable|null $autovalidate
     */
    protected static $autovalidate;

    /**
     * @var array $rules
     */
    protected static $rules = [];

    /**
     * @param callable|null $autovalidate
     */
    public static function setAutovalidate(?callable $autovalidate): void
    {
        self::$autovalidate = $autovalidate;
    }

    /**
     * @return callable|null
     */
    public static function getAutovalidate(): ?callable
    {
        return self::$autovalidate;
    }

    /**
     * @param array $rules
     */
    public static function setRules(array $rules): void
    {
        self::$rules = $rules;
    }

    /**
     * @param string $name
     * @param callable $implementation
     */
    public static function addRule(string $name, callable $implementation): void
    {
        self::$rules[$name] = $implementation;
    }

    /**
     * @return array
     */
    public static function getRules(): array
    {
        return self::$rules;
    }

    /**
     * @param string $name
     * @return callable
     */
    public static function getRule(string $name): callable
    {
        if (empty(static::$rules[$name]) || !is_callable(static::$rules[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Rule %s dose not exists or is not a callbale',
                $name
            ));
        }

        return static::$rules[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isValidRule(string $name): bool
    {
        return !empty(static::$rules[$name]) && is_callable(static::$rules[$name]);
    }

    /**
     * @param array $messages
     * @param string $lang
     */
    public static function setMessages(array $messages, string $lang = self::DEFAULT_LANG): void
    {
        self::$messages[$lang] = $messages;
    }

    /**
     * @param int $code
     * @param string $lang
     * @return mixed
     */
    public static function getMessage(int $code, string $lang = self::DEFAULT_LANG)
    {
        $message = self::$messages[$lang][$code] ?? self::MESSAGES[$lang][$code] ?? false;

        if (false === $message) {
            throw new \InvalidArgumentException(sprintf(
                'Validator message not found in %s',
                __CLASS__
            ));
        }

        return $message;
    }

    /**
     * @param array $validationMessages
     * @param string $lang
     */
    public static function setValidationMessages(array $validationMessages, string $lang = self::DEFAULT_LANG): void
    {
        self::$validationMessages[$lang] = $validationMessages;
    }

    /**
     * @param string $lang
     * @return array
     */
    public static function getValidationMessages(string $lang = self::DEFAULT_LANG): array
    {
        return self::$validationMessages[$lang] ?? self::VALIDATION_MESSAGES[$lang] ?? [];
    }
}