<?php
namespace hierarchicalSQL;

/**
 * Tests if a every bracket in a query is properly matched to its pair.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return true if query has valid brackets.
 */
function validBrackets($query, $brackets) {
    $seenBrackets = new \SplStack();
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $bracket = $query[$i];
        // open bracket
        if (array_key_exists($bracket, $brackets)) {
            $seenBrackets->push($bracket);
        // close bracket
        } elseif (in_array($bracket, $brackets)) {
            // too many closings
            if ($seenBrackets->count() == 0) {
                return false;
            }
            // last bracket is opener and is paired with current bracket
            $lastBracket = $seenBrackets->pop();
            if (!array_key_exists($lastBracket, $brackets) || $brackets[$lastBracket] != $bracket) {
                return false;
            }
        }
    }
    // too many openings
    return $seenBrackets->count() == 0;
}

/**
 * Tests if the operators in $query is valid. i.e. in every instance of )*(, where ( is an opening bracket and ) is a closing bracket, * is an operator.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @return void
 */
function validOperators($query, $brackets, $operators) {
    $lastOpIsClose = false;
    $operator = "";
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $char = $query[$i];
        // open bracket
        if (array_key_exists($char, $brackets)) {
            if ($lastOpIsClose) {
                // remove white space
                $operator = \trim($operator);
                // invalid operator
                if (!array_key_exists($operator, $operators)) {
                    return false;
                }
            }
            $lastOpIsClose = false;
        // close bracket
        } elseif (in_array($char, $brackets)) {
            $lastOpIsClose = true;
            $operator = "";
        // not bracket
        } else {
            $operator = $operator . $char;
        }
    }
    return true;
}

/**
 * Calculates the depth of a query.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return int
 */
function depth($query, $brackets) {
    $depth = 0;
    $max = 0;
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $bracket = $query[$i];
        // open bracket
        if (array_key_exists($bracket, $brackets)) {
            $depth++;
            $max = max($max, $depth);
        // close bracket
        } elseif (in_array($bracket, $brackets)) {
            $depth--;
        }
    }
    return $max;
}


/**
 * Tests if a query can be parsed.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubQueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 */
function validQuery($query, $brackets, $operators, $maxDepth, $maxSubqueries) {
    if (!validBrackets($query, $brackets)) {
        return false;
    }
    if (depth($query, $brackets) != $maxDepth) {
        return false;
    }
    
    return true;
}


?>