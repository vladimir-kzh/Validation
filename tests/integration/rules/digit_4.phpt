--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\DigitException;
use Respect\Validation\Validator as v;

try {
    v::not(v::digit())->check(1);
} catch (DigitException $e) {
    echo $e->getMainMessage();
}
?>
--EXPECTF--
1 must not contain digits (0-9)
