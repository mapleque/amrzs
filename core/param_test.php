<?php

require __DIR__ . '/../include.php';

/**
 * test_suites := [ <suite>, ... ]
 * suite := [
 *      rule => $rule,
 *      cases => <cases>,
 * ]
 * cases := [ <case>, ... ]
 * case := [
 *      data => $param,
 *      status => $status
 * ]
 */
$test_suites = [
    // empty case should pass forever
    [
        'rule' => [],
        'cases' => [
            [
                'data' => [],
                'status' => 0,
            ],
        ],
    ],
    // isString
    [
        'rule' => [
            'str' => Param::isString(1,10) . 30001,
        ],
        'cases' => [
            [
                'data' => [
                    'str' => 'abcde',
                ],
                'status' => 0,
            ],
            [
                'data' => [
                    'str' => 12345,
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                    'str' => 'abcdeabcdeabcde',
                    'int' => 5,
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                    'str' => '',
                ],
                'status' => 30001,
            ],
        ],
    ],
    // isInt
    [
        'rule' => [
            'int' => Param::isInt(1,10) . 30001,
        ],
        'cases' => [
            [
                'data' => [
                    'int' => 5,
                ],
                'status' => 0,
            ],
            [
                'data' => [
                    'int' => 'abcde',
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                    'int' => 15,
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                    'int' => 0,
                ],
                'status' => 30001,
            ],
        ],
    ],
    // func
    [
        'rule' => [
            'v' => Param::func(function($v){
                return $v === 'abc';
            }) . 30001,
        ],
        'cases' => [
            [
                'data' => [
                    'v' => 'abc',
                ],
                'status' => 0,
            ],
            [
                'data' => [
                    'v' => 'a',
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                ],
                'status' => 30001,
            ],
            [
                'data' => [
                    'v' => 'abc',
                    'b' => 'ccc',
                ],
                'status' => 0,
            ],
        ],
    ],
    // filter, sort, range
    [
        'rule' => [
            'filter' => [[
                'id' => Param::isInt(1,10) . 10001,
                'date' => Param::IS_DATE_RANGE . 10004,
            ]],
            'sort' => Param::isSort([ 'id' ]) . 10002,
            'range' => Param::IS_INT_RANGE . 10003,
        ],
        'cases' => [
            [
                'data' => [
                    'filter' => [
                        [
                            'id' => 1,
                            'date' => ['2017-05-01', '2017-07-01'],
                        ]
                    ],
                    'sort' => [
                        'id' => true,
                    ],
                    'range' => [0,1],
                ],
                'status' => 0,
            ],
        ],
    ],
];

function test($rule, $param, $status)
{
    $real = Param::check($rule, $param);
    if ($real !== $status) {
        echo 'check faild on expect != real' . "\n";
        dump([
            'rule' => $rule,
            'param' => $param,
            'expect' => $status,
            'real' => $real,
        ]);
        echo '[FAILD] case faild!'. "\n";
        die;
    }
}

foreach ($test_suites as $suite) {
    foreach ($suite['cases'] as $case) {
        test($suite['rule'], $case['data'], $case['status']);
    }
}

echo '[OK] All Cases Pass!' . "\n";
