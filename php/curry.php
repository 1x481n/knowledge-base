<?php
/**
 * Created by IntelliJ IDEA.
 * User: 1x481n
 * Date: 2022/5/7
 * Time: 4:59 PM
 */

// 函数柯里化

function add($x, $y, $z)
{
    return $x + $y + $z;
}


function curry($fn): Closure
{
    $args = func_get_args();
    $outerArgs = array_slice($args, 1);
    $fnRef = new ReflectionFunction($fn);
    $fnArgsNums = $fnRef->getNumberOfParameters();
    $initFnArgsNums = count($outerArgs);
    $closureNums = $fnArgsNums - $initFnArgsNums;
    $totalArgs = $outerArgs;
    $n = 0;
    $closure = function () use ($fn, $closureNums, &$totalArgs, &$n, &$closure) {
        $innerArgs = func_get_args();
        $innerArgsNums = func_num_args();
        $totalArgs = array_merge($totalArgs, $innerArgs);
        $n = $n + $innerArgsNums;
        if ($n == $closureNums) {
            return call_user_func_array($fn, $totalArgs);
        }
        return $closure;
    };

    return $closure;
}


$add = curry('add');
echo $add(1)(2)(3);
echo PHP_EOL;

$add = curry('add');
echo $add(1, 2)(3);
echo PHP_EOL;

$add = curry('add');
echo $add(1, 2, 3);
echo PHP_EOL;

$add = curry('add', 1);
echo $add(2)(3);
echo PHP_EOL;

$add = curry('add', 1);
echo $add(2, 3);
echo PHP_EOL;

$add = curry('add', 1, 2);
echo $add(3);
echo PHP_EOL;

$add = curry('add', 1, 2, 3);
echo $add();
echo PHP_EOL;

