--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\MatchException;
use Respect\Validation\Validator as v;

class MyClass
{
    public function __toString()
    {
        return 'Return of __toString()';
    }
}

try {
    v::create()
        ->match('/^[a-z]{3}$/')
        ->check(new MyClass());
} catch (MatchException $exception) {
    echo $exception->getMessage().PHP_EOL;
}
?>
--EXPECTF--
"Return of __toString()" must match `/^[a-z]{3}$/`
