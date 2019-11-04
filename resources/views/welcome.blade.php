<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.materialdesignicons.com/2.5.94/css/materialdesignicons.min.css">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <title>Minimal CRM</title>
    </head>
    <body>
        <div id="app"></div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
