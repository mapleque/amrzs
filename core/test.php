<?php
$a = 'abc';
function abc($str) {
    echo "abc:$str\n";
}
$a('hello');

$b = function($str) {
    echo "ccc:$str";
    echo __FUNCTION__ . "\n";
};
$b('what');

function ccc($func) {
    $func(__FUNCTION__);
}

ccc(function($str){
    echo __FUNCTION__. "\n";
    echo "$str\n";
});

assert_options(ASSERT_WARNING, 0)
assert_options(ASSERT_CALLBACK, 'amrzs_assert_handler');
function amrzs_assert_handler($file, $line, $code, $desc = '') {
    echo "amrzs assert at $file:$line: $code\n$desc\n";
}

assert(false, 'here is assert');
