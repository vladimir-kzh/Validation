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
        ->assert('123', ['label' => 'custom label']);
} catch (ValidatorException $exception) {
    echo $exception->getFullMessage().PHP_EOL;
}
?>
--EXPECTF--
- All rules must pass for custom label
  - custom label must match `/^[a-z]{3}$/`
  - custom label must match `/^$/`
