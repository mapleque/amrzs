<?php

function dump($p)
{
    _dump($p);
    echo "\n";
    print_callstack();
}

function _dump($p, $prefix = '')
{
	if (is_array($p)) {
		echo "[\n";
		foreach($p as $i => $e) {
			echo $prefix;
			echo "\t" . $i . '->';
			_dump($e, $prefix . "\t");
			echo "\n";
		}
		echo $prefix . "]";
	} elseif (is_string($p) || is_int($p)) {
		echo $p;
	} else {
		var_dump($p);
	}
}

function print_callstack()
{
    $e = new Exception;
    echo $e->getTraceAsString() . "\n";
}

assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_CALLBACK, 'amrzs_assert_handler');
function amrzs_assert_handler($file, $line, $code, $desc = '') {
    Base::dieWithError(ERROR_KNOWN, $desc);
}
