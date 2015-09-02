--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\MatchException;
use Respect\Validation\Validator as v;

try {
    $object = new stdClass();
    $object->foo = 'abc';

    v::create()
        ->match('/^[a-z]{3}$/')
        ->check($object);
} catch (MatchException $exception) {
    echo $exception->getMessage().PHP_EOL;
}
?>
--EXPECTF--
`[object] (stdClass: { "foo": "abc" })` must match `/^[a-z]{3}$/`
