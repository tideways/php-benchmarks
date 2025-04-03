<!-- resources/views/components/layout.blade.php -->

<html>
    <head>
        <title>{{ $title ?? 'Laravel 12' }}</title>
    </head>
    <body>
        <h1>Laravel 12</h1>
        <hr/>
        {{ $slot }}
    </body>
</html>
