--FILE--
<?php

date_default_timezone_set('UTC');

require 'vendor/autoload.php';

use Respect\Validation\Exceptions\MatchException;
use Respect\Validation\Validator as v;

try {
    $dateTime = new DateTime();

    v::create()
        ->match('/^[a-z]{3}$/')
        ->check($dateTime);
} catch (MatchException $exception) {
    echo $exception->getMessage().PHP_EOL;
}
?>
--EXPECTF--
%d-%d-%dT%d:%d:%d+00:00 must match `/^[a-z]{3}$/`
