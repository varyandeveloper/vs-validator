<?php

/**
 * Class ValidatorTest
 * @author Varazdat Stepanyan
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \VS\Validator\ValidatorInterface $Validator
     */
    protected $Validator;

    public function setUp()
    {
        /**
         * @var \VS\Request\RequestInterface $request
         */
        $request = \VS\Request\Request::getInstance();

        // lets imagine that for submitted with current fields
        $request->bind([
            'first_name' => 'Jon',
            'last_name' => 'Doe',
            'age' => 35,
            'ip_address' => '192.168.103.1',
            'l_name' => 'Doe'
        ]);

        $this->Validator = new VS\Validator\Validator($request);
    }

    public function testRequired()
    {
        $this->Validator->setRules([
            'f_name' => 'required',
        ])->run();

        $this->assertTrue(!$this->Validator->isValid());

        $actual = $this->Validator->getErrors();
        $expected = [
            'f_name' => 'The field F Name is required.'
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testRequiredWhenEmpty()
    {
        $this->Validator->setRules([
            'username' => 'requiredWhenEmpty:[email]',
            'email' => 'requiredWhenEmpty:[username]'
        ])->run();

        $this->assertTrue(!$this->Validator->isValid());

        $actual = $this->Validator->getErrors();
        $expected = [
            'username' => 'The field Username is required when the field Email is empty.',
            'email' => 'The field Email is required when the field Username is empty.',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testValidIp()
    {
        $this->Validator->setRules([
            'ip_address' => 'ip'
        ])->run();

        $this->assertTrue($this->Validator->isValid());

        $this->assertEquals([], $this->Validator->getErrors());
    }
}