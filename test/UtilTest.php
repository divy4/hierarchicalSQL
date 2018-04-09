<?php
namespace hierarchicalSQL;
require 'util.php';

class UtilTest extends \PHPUnit\Framework\TestCase {

    protected $brackets;
    protected $operators;

    protected function setUp() {
        $this->brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
        $this->operators = array('AND' => 't1.score * t2.score',
                                 'OR' => 'MAX(t1.score, t2.score)');
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

    public function testValidOperators() {
        // no operators
        $this->assertTrue(validOperators("asdf asdf", $this->brackets, $this->operators));
        // all valid operator with space
        $this->assertTrue(validOperators("(asdf) AND (asdf)", $this->brackets, $this->operators));
        // all valid operator without space
        $this->assertTrue(validOperators("(asdf)OR(asdf)", $this->brackets, $this->operators));
        // invalid operator
        $this->assertFalse(validOperators("(asdf)NOT(asdf)", $this->brackets, $this->operators));
    }

    public function testDepth() {
        $this->assertEquals(0, depth("", $this->brackets));
        $this->assertEquals(1, depth("()()", $this->brackets));
        $this->assertEquals(2, depth("(()())(())", $this->brackets));
    }

    public function testNumSubqueries() {
        $this->assertEquals(1, numSubqueries("asdf", $this->brackets));
        $this->assertEquals(1, numSubqueries("(asdf)", $this->brackets));
        $this->assertEquals(2, numSubqueries("(asdf) AND (asdf)", $this->brackets));
        $this->assertEquals(2, numSubqueries("((asdf) AND (asdf))", $this->brackets));
    }

}

?>