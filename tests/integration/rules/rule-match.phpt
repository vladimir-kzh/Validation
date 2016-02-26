--TEST--
Should validate Match rule
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Factory;
use Respect\Validation\Rules\Match;
use Respect\Validation\Validator;

$values = [
    '/^.+$/' => 'What ever is in here',
    '/^[a-z]{3}$/' => 'abc',
];

$factory = new Factory();
foreach ($values as $pattern => $value) {
    $validator = new Validator($factory);
    $validator->addRule(new Match($pattern));
    echo $pattern.': '.$value.' ~> '.json_encode($validator->validate($value)).PHP_EOL;
}
?>
--EXPECTF--
/^.+$/: What ever is in here ~> true
/^[a-z]{3}$/: abc ~> true
