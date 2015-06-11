--TEST--
Should validate Optional rule
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Rules\Match;
use Respect\Validation\Rules\Optional;
use Respect\Validation\Validator;

$validator = new Validator();
$validator->addRule(
    new Optional(
        new Match('/^[a-z]+$/')
    )
);

var_dump($validator->validate(null));
var_dump($validator->validate(''));
var_dump($validator->validate(0));
?>
--EXPECTF--
bool(true)
bool(true)
bool(false)
