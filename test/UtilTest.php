<?php
namespace hierarchicalSQL;
require 'hierarchicalSQL.php';

class UtilTest extends \PHPUnit\Framework\TestCase {

    protected $brackets;
    protected $operators;

    protected function setUp() {
        $this->brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
        $this->operators = array('AND' => 'MAX(t1.score, t2.score)');
    }

    public function testValidBrackets() {
        // no brackets/non bracket chars
        $this->assertTrue(validBrackets("asdf", $this->brackets));
        // all brackets
        $this->assertTrue(validBrackets("([{}])", $this->brackets));
        // adjacent pairs
        $this->assertTrue(validBrackets("(()())", $this->brackets));
        // wrong order
        $this->assertFalse(validBrackets("([)]", $this->brackets));
        // too many opens
        $this->assertFalse(validBrackets("(()", $this->brackets));
        // too many closes
        $this->assertFalse(validBrackets("())", $this->brackets));
    }

    public function testDepth() {
        $this->assertEquals(0, depth("", $this->brackets));
        $this->assertEquals(0, depth("()()", $this->brackets));
        $this->assertEquals(0, depth("(()())(())", $this->brackets));
    }

}

?>