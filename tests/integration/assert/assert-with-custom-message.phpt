--TEST--
Should throw validator exception when asserting and display full message
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\ValidatorException;
use Respect\Validation\Validator as v;

try {
    v::create()
        ->match('/^[a-z]{3}$/')
        ->match('/^.+$/')
        ->match('/^$/')
        ->assert('123', ['message' => 'Something is not right']);
} catch (ValidatorException $exception) {
    echo $exception->getFullMessage().PHP_EOL;
}
?>
--EXPECTF--
- Something is not right
  - '123' must match `/^[a-z]{3}$/`
  - '123' must match `/^$/`
