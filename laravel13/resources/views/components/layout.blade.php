<!-- resources/views/components/layout.blade.php -->

<html>
    <head>
        <title>{{ $title ?? 'Laravel 13' }}</title>
    </head>
    <body>
        <h1>Laravel 13</h1>
        <hr/>
        {{ $slot }}
    </body>
</html>
