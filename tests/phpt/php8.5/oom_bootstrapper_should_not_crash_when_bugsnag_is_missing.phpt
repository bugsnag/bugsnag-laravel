--TEST--
The OomBootstrapper should not crash when bugsnag is not in the DI container
--FILE--
<?php

use Bugsnag\BugsnagLaravel\OomBootstrapper;

function app($alias = null) {
    echo "'app' was called!\n";

    if ($alias === 'bugsnag') {
        return null;
    }

    if ($alias !== null) {
        throw new UnexpectedValueException("Unknown alias '{$alias}' given");
    }

    throw new BadFunctionCallException("This fake 'app' should always be called with an alias");
}

require __DIR__.'/../../../src/OomBootstrapper.php';

(new OomBootstrapper())->bootstrap();

ini_set('memory_limit', '5M');

$i = 0;

gc_disable();

while ($i++ < 12345678) {
    $a = new stdClass;
    $a->b = $a;
}

echo "No OOM!\n";
?>
--SKIPIF--
<?php
if (PHP_VERSION_ID < 80500) {
    echo 'SKIP — this case is already tested in PHP <8.5';
}
?>
--EXPECTF--
Fatal error: Allowed memory size of %d bytes exhausted (tried to allocate %d bytes) in %s on line %d
Stack trace:
#0 {main}
'app' was called!
