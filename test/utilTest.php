<?php
namespace hierarchicalSQL;
require 'hierarchicalSQL.php';

Print("Starting tests...\n");

$valid1 = "(asdf) AND((asdf) AND (asdf))";
$invalid1 = "(()";

$brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
$operators = array('AND' => 'MAX(t1.score, t2.score)');


// normal usage
\hierarchicalSQL\validateQuery($valid1, $brackets, $operators, 3, 10);

?>