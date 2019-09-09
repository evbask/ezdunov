<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

    </head>
        <body>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
        <script type="text/javascript">
            let socket = io(':6001');

            socket.on('message', function (msg) {
                console.log('From server:',msg);
            });

        </script>
        </body>
</html>
