<?php
/**
 * For Test Echo
 */
function dump($p, $prefix = '')
{
	if (is_array($p)) {
		echo "[\n";
		foreach($p as $i => $e) {
			echo $prefix;
			echo "\t" . $i . '->';
			dump($e, $prefix . "\t");
			echo "\n";
		}
		echo $prefix . "]";
	} elseif (is_string($p) || is_int($p)) {
		echo $p;
	} else {
		var_dump($p);
	}
}

function dump_card($p)
{
	foreach($p as $e) {
		echo
			//'[',$e['index'],']',
			//'[',$e['value'],']',
			'|',$e['name'],'|';
	}
}

function print_callstack()
{
	$e = new Exception;
	var_dump($e->getTraceAsString());
}
