<?php
namespace hierarchicalSQL;

require 'hierarchicalSQL.php';

Print("Starting tests...\n");

$validQuery = "(asdf) AND((asdf) AND (asdf))";
$invalidQuery = "(()";
$parser = function($query) {
    return "Select * FROM table";
};
$brackets = array('(' => ')',
                  '[' => ']',
                  '{' => '}');
$operators = array('AND' => 'MAX(t1.score, t2.score)');
$maxDepth = 3;
$maxSubQueries = 10;

// empty
\hierarchicalSQL\validateBrackets("", $brackets);
// all brackets
\hierarchicalSQL\validateBrackets("({[]})", $brackets);
// wrong order
try {
    \hierarchicalSQL\validateBrackets("([)]", $brackets);
} catch ($err) {
}
\hierarchicalSQL\validateBrackets("", $brackets);
\hierarchicalSQL\validateBrackets("", $brackets);

?>