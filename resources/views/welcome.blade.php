<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <style>

            .button {
                padding: 5px;
                background-color: aliceblue;
                width: 150px;
                text-align: center;
                margin: 10px;
                border-radius: 10px;
            }

            .button a {
                text-decoration: none;
            }

            .flex-container {
                display: flex;
                flex-wrap: wrap;
            }

            /* Thumbnail center and crop from https://jonathannicol.com/blog/2014/06/16/centre-crop-thumbnails-with-css/ */
            .thumbnail {
                position: relative;
                width: 200px;
                height: 200px;
                overflow: hidden;
                margin: 10px;
            }

            .thumbnail img {
                position: absolute;
                left: 50%;
                top: 50%;
                height: 100%;
                width: auto;
                -webkit-transform: translate(-50%,-50%);
                    -ms-transform: translate(-50%,-50%);
                        transform: translate(-50%,-50%);
            }
        </style>
    </head>
    <body>
    <h2>Welcome!</h2>

    @if (session('status'))
            <i>
                {{ session('status') }}
            </i>
    @endif

    <h4>Images</h4>
    <div class="button">
        <a href="/images">Add new image</a>
    </div>

    <div class="flex-container">
        @forelse ($images as $image)
            <div class="thumbnail">
                <a href="/storage/processed_images/{{ $image->filename }}">
                    <img src="/storage/processed_images/{{ $image->thumbnail_filename }}" alt="">
                </a>
            </div>
        @empty
            <p>No images...</p>
        @endforelse
    </div>


    </body>
</html>
