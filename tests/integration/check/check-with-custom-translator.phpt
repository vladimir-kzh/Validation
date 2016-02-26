--TEST--
Should change message according to defined context properties
--FILE--
<?php
require 'vendor/autoload.php';

use Respect\Validation\Exceptions\MatchException;
use Respect\Validation\Validator as v;

v::getDefaultFactory()
    ->setTranslator(function ($message) {
        $ptBR = [
            '{{placeholder}} must match `{{pattern}}`' => '{{placeholder}} deve casar com `{{pattern}}`',
        ];

        if (isset($ptBR[$message])) {
            return $ptBR[$message];
        }

        return $message;
    });

try {
    v::create()
        ->match('/^[a-z]{3}$/')
        ->check('123');
} catch (MatchException $exception) {
    echo $exception->getMessage().PHP_EOL;
}
?>
--EXPECTF--
"123" deve casar com `/^[a-z]{3}$/`
