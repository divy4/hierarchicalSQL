<?php
namespace hierarchicalSQL;
require 'SQLQuery.php';


class SQLQueryTest extends \PHPUnit\Framework\TestCase {

    public function testSingle() {
        $query = new SQLQuery('col1', 'tbl1');
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1'], ['tbl1']);
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
    }

    public function testMultiple() {
        $query = new SQLQuery('col1,col2', 'tbl1,tbl2');
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
        $query = new SQLQuery(['col1', 'col2'], ['tbl1', 'tbl2']);
        $this->assertEquals('SELECT col1,col2 FROM tbl1,tbl2', (string)$query);
    }

    public function testAs() {
        $query = new SQLQuery('col1 as c1', 'tbl1');
        $this->assertEquals('SELECT col1 as c1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1 as c1'], ['tbl1']);
        $this->assertEquals('SELECT col1 as c1 FROM tbl1', (string)$query);
    }

    public function testWhere() {
        $query = new SQLQuery('col1', 'tbl1', 'col1 > 0 AND col1 < 10');
        $this->assertEquals('SELECT col1 FROM tbl1 WHERE (col1 > 0 AND col1 < 10)', (string)$query);
        $query = new SQLQuery(['col1'], ['tbl1'], ['col1 > 0', 'col1 < 10']);
        $this->assertEquals('SELECT col1 FROM tbl1 WHERE (col1 > 0) AND (col1 < 10)', (string)$query);
    }

    public function testMath() {
        $query = new SQLQuery('col1 - col2 as c1', 'tbl1');
        $this->assertEquals('SELECT col1 - col2 as c1 FROM tbl1', (string)$query);
        $query = new SQLQuery(['col1 - col2 as c1'], ['tbl1']);
        $this->assertEquals('SELECT col1 - col2 as c1 FROM tbl1', (string)$query);
    }

    public function testSubQuery() {
        $query = new SQLQuery('col1', '(SELECT col1, col2 FROM tbl2, tbl3) as tbl1');
        $this->assertEquals('SELECT col1 FROM (SELECT col1, col2 FROM tbl2, tbl3) as tbl1', (string)$query);
        $query = new SQLQuery(['col1'], ['(SELECT col1, col2 FROM tbl2, tbl3) as tbl1']);
        $this->assertEquals('SELECT col1 FROM (SELECT col1, col2 FROM tbl2, tbl3) as tbl1', (string)$query);
    }

    public function testMixed() {
        $query = new SQLQuery('col1', ['tbl1']);
        $this->assertEquals('SELECT col1 FROM tbl1', (string)$query);
    }
    
}