# Amrzs

A php web frame work.

## Usage

First of or, include this module:
```
<?php

require __DIR__ . '/vendor/amrzs/include.php';

// ... your code here
```

## Class auto loader

Code as follow if there is a new class:
```
<?php

...

ClassLoader::appendMap([
    'YourClassName' => 'yourPHPFile', // the file name without .php
]);
```

## Mysql database

If you want to use Mysql, just use a static method:
```
<?php

...

$ret = DB::select('SELECT * FROM tmp_table WHERE id = ?', [1]);
// the $ret is array map of the result, such as:
// [
//      [
//          'id' => 1,
//          'other key of table row' => 'the value of the row',
//          ... other rows
//      ],
//      ... if there ara more data
// ]

// other method you can use in case such as :
// DB::insert
// DB::update
// DB::delete
// DB::exec
// DB::begin
// DB::commit
// for more @see method in db.php

```

To config the database, there should be a important.php file in your project:
```
// in your project root path
cp  vendor/amrzs/important.example.php important.php
// update important.php to connect your mysql service
```

## Request & Response
The requset and response are all in JSON format. So your request should be a post method with json data.

For an example:

Edit your api file:
```
// example.php
<?php

...

$req = Base::getRequestJson();

Param::checkAndDie([
    'str' => Param::isString(1,10) . ERROR_CODE_STR,
    'num' => Param::isInt(1, 10) . ERROR_CODE_NUM,
], $req);

Base::dieWithResponse([ 'result' => "$req['str']:$req['num']" ]);

```

Send request by ajax:
```
```

The response received:
```
```

## Assert
If you use ```assert``` method in your code any where, the script will die there and response as follow, when assert triger.
```
{
    status : 3,
    err : <your assert message>
}
```
