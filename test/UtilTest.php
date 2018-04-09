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
        $this->assertTrue(\hierarchicalSQL\validBrackets("asdf", $this->brackets));
        // all brackets
        $this->assertTrue(\hierarchicalSQL\validBrackets("([{}])", $this->brackets));
        // adjacent pairs
        $this->assertTrue(\hierarchicalSQL\validBrackets("(()())", $this->brackets));
        // wrong order
        $this->assertFalse(\hierarchicalSQL\validBrackets("([)]", $this->brackets));
        // too many opens
        $this->assertFalse(\hierarchicalSQL\validBrackets("(()", $this->brackets));
        // too many closes
        $this->assertFalse(\hierarchicalSQL\validBrackets("())", $this->brackets));
    }

}

?>