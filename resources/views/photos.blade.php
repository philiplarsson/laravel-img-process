<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- CSS -->
        <link rel="stylesheet" href="/css/dropzone.css">

        <!-- JS -->
        <script src="/js/dropzone.js"></script>
    </head>
    <body>
        <h2>Image Upload</h2>
        <ul>
            <li><a href="/">Back to homepage</a></li>
        </ul>
        <h3>Image Dropzone</h3>
        <form action="/image"
              class="dropzone"
              id="my-awesome-dropzone">
        @csrf
        </form>

    </body>
</html>
