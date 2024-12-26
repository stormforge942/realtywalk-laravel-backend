<!DOCTYPE html>
<html lang="en">
    <head>
        <base href="./">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <link rel="stylesheet" href="{{ mix('css/general.css') }}">
        <link rel="stylesheet" href="{{ mix('css/property.css') }}"/>
        <script src="https://maps.googleapis.com/maps/api/js?key={{config('app.google_maps_api_key')}}"></script>
        <script src="{{ asset('js/markwithlabel.js') }}"></script>
        <script src="{{ asset('js/md5.min.js') }}"></script>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-9R5ENGNGZJ"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-9R5ENGNGZJ');
        </script>

        @include('properties.feature_scripts',['position' => 'header'])
        <style>
            html,
            body {
                height: 100%;
                margin: 0px;
            }
            h3 a {
                color: rgb(89, 88, 88) !important;
                }
            #left {
                border-right: none !important;
            }
            #right {
                border-left: 1px solid black;
            }
            p a {
            color: #c41230!important;
            text-decoration: none;
            }
            .polygon-label {
                overflow: visible !important;
                width: auto;
                text-align: center;
                z-index: 2000;
                pointer-events: none;
                user-select: none;
                transform: translateY(-10px);
            }
            .polygon-label div {
                position: relative;
                margin: 0 auto;
                color: black;
                background-color: white;
                font-size: 14px;
                font-weight: normal;
                text-align: center;
                border: 2px solid #ffc501;
                padding: 2px 5px;
                border-radius: 2px;
            }
            .lds-ripple {
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
            }
            .lds-ripple div {
                position: absolute;
                border: 4px solid #012e55;
                opacity: 1;
                border-radius: 50%;
                animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            }
            .lds-ripple div:nth-child(2) {
            animation-delay: -0.5s;
            }
            @keyframes lds-ripple {
                0% {
                    top: 36px;
                    left: 36px;
                    width: 0;
                    height: 0;
                    opacity: 1;
                }
                100% {
                    top: 0px;
                    left: 0px;
                    width: 72px;
                    height: 72px;
                    opacity: 0;
                }
            }
            .lds-ripple {
                display:block;
                margin: 0 auto;
            }
            .modal-backdrop {
                z-index: 97;
            }
        </style>
    </head>
    <body class="c-app properties">
        <div class="c-wrapper">
            <div class="c-body">
                <main id="app" class="c-main">
                    <!-- Component will go here -->
                    <App></App>
                </main>
            </div>
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.1.2/js/plugins/sortable.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.4.0/tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.js"></script>
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector:'.desc-editor',
                width: "100%",
                height: 400,
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });

            if ($("#add")) {
                let linkNum = 1;
                //add more inputs for links
                $('#add').click(function(e) {
                    e.preventDefault();
                    linkNum++;
                    $('#links-container').append(`
                        <div class="links">
                            <input class="form-control" type="text" name="links[label][]" placeholder="Label" />
                            <input class="form-control ml-2" type="text" name="links[url][]" placeholder="URL (https://)" />
                            <button id="${linkNum}" type="button" class="btn btn-default remove">Remove</button>
                        </div>
                    `)
                });

                $(document).on("click", ".remove", function(){
                    let $this = $(this);
                    //remove the parent of the current link
                    $this.parent().remove();
                });
            }
        })
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
	@include('properties.feature_scripts',['position' => 'footer'])
    <!-- remove boostrap cdn, already loads jquery, laravel conflicts with it -->
    <!--<script src="https://code.jquery.com/jquery-3.1.0.js"></script>-->
    <!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>-->
</html>
