--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\MatchException;
use Respect\Validation\Validator as v;

try {
    $array = ['abc', '123'];
    v::create()
        ->match('/^[a-z]{3}$/')
        ->check($array);
} catch (MatchException $exception) {
    echo $exception->getMessage().PHP_EOL;
}
?>
--EXPECTF--
`["abc","123"]` must match `/^[a-z]{3}$/`
