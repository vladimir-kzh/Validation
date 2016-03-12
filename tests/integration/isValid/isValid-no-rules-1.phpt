--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Validator as v;

var_dump(
    v::create()
        ->isValid('Whatever')
);
?>
--EXPECTF--
bool(true)
