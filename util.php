<?php
namespace hierarchicalSQL;

/**
 * Adds brackets to a subquery.
 *
 * @param string $subquery Part of a query, when split by its operators.
 * @param array $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param string $open The opening bracket that should be used to encase the subquery.
 * @param string $close The closing bracket that should be used to encase the subquery.
 * @return string $subquery with added brackets, null if there exists a bracket with non-whitespace characters on the wrong side of it.
 */
function addBracketsSubquery(string $subquery, array $brackets, string $open, string $close) {
    $subquery = \trim($subquery);
    $qLen = strlen($subquery);
    $lastOpen = null;
    $firstClose = null;
    $seenNonBracket = false;
    for ($i = 0; $i < $qLen; $i++) {
        $char = $subquery[$i];
        // open bracket
        if (array_key_exists($char, $brackets)) {
            if ($seenNonBracket) {
                return null;
            }
            $lastOpen = $i;
        // close bracket
        } elseif (in_array($char, $brackets)) {
            if (is_null($firstClose)) {
                $firstClose = $i;
            }
        // non-whitespace
        } elseif (!ctype_space($char)) {
            if (is_integer($firstClose)) {
                return null;
            }
            $seenNonBracket = true;
        }
    }
    // no brackets
    if (is_null($lastOpen) && is_null($firstClose)) {
        return $open . $subquery . $close;
    // only opens
    } elseif (is_null($firstClose)) {
        return substr($subquery, 0, $lastOpen + 1) . $open . substr($subquery, $lastOpen + 1, $qLen - $lastOpen - 1) . $close;
    // only closes
    } elseif (is_null($lastOpen)) {
        return $open . substr($subquery, 0, $firstClose) . $close . substr($subquery, $firstClose, $qLen - $firstClose);
    // encased
    } else {
        return $subquery;
    }
}

/**
 * Adds missing brackets to a query.
 * 
 * @param string $query The text of a query.
 * @param array $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param array $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @return string A modified version of the string with brackets added around each query. null if an error occurred.
 */
function addBrackets(string $query, array $brackets, array $operators) {
    // find operator positions
    $pattern = '/^|$|' . join('|', array_keys($operators)) . '/';
    $matches = array();
    $numMatches = \preg_match_all($pattern, $query, $matches, PREG_OFFSET_CAPTURE);
    if (is_null($numMatches)) {
        return null;
    }
    // split query by operators
    $open = array_keys($brackets)[0];
    $close = $brackets[$open];
    $matches = $matches[0];
    $subqueries = array();
    for ($i = 1; $i < $numMatches; $i++) {
        $start = $matches[$i - 1][1] + strlen($matches[$i - 1][0]);
        $subquery = substr($query, $start, $matches[$i][1] - $start);
        $subqueries[$i - 1] = $matches[$i - 1][0] . addBracketsSubquery($subquery, $brackets, $open, $close);
    }
    //$subqueries[0] = addBracketsSubquery(, $brackets, $open, $close) ;
    return $open . join('', $subqueries) . $close;
}

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
 * Calculates the number of subqueries in a query. i.e. every instance of (*), where ( is an opening bracket, ) is a closing bracket, and * doesn't contain a bracket.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @return int
 */
function numSubqueries($query, $brackets) {
    $lastOpIsOpen = false;
    $subqueries = 0;
    $qLen = strlen($query);
    // for char in query
    for ($i = 0; $i < $qLen; $i++) {
        $bracket = $query[$i];
        // open bracket
        if (array_key_exists($bracket, $brackets)) {
            $lastOpIsOpen = true;
        // close bracket
        } elseif (in_array($bracket, $brackets)) {
            // only count query if last bracket was an opening
            if ($lastOpIsOpen) {
                $lastOpIsOpen = false;
                $subqueries++;
            }
        }
    }
    // mininum number of subqueries is 1, since every tree has at least 1 leaf.
    return max(1, $subqueries);
}

/**
 * Tests if a query can be parsed.
 *
 * @param [String] $query The text of a query.
 * @param [Array[String => String]] $brackets An associative array that maps an openning bracket to it's closing bracket.
 * @param [Array[String => String]] $operators An associative Array that maps an operator's string (e.g. 'OR') to a string that represents how the "score" column should be calculated in SQL when preforming a natural join between tables t1 and t2 that both contain a "score" column.
 * @param [Integer] $maxDepth The maximum depth of brackets the query can contain.
 * @param [Integer] $maxSubqueries The maximum number of subqueries (i.e. the number of $baseParser calls) the query can contain.
 */
function validQuery($query, $brackets, $operators, $maxDepth, $maxSubqueries) {
    if (depth($query, $brackets) > $maxDepth) {
        print("\nasdf: depth\n");
        return false;
    } elseif (numSubqueries($query, $brackets) > $maxSubqueries) {
        print("\nasdf: num sub\n");
        return false;
    } elseif (!validBrackets($query, $brackets)) {
        print("\nasdf: brackets\n");
        return false;
    } elseif (!validOperators($query, $brackets, $operators)) {
        return false;
    }
    return true;
}


?>