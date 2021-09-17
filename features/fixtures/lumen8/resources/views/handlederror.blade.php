<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lumen</title>
    </head>
    <body>
        <?php app('bugsnag')->notifyError("Handled error", "This is a handled error") ?>
    </body>
</html>
