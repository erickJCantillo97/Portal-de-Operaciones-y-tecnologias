<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Asignación de Equipos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">

    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .main {
            display: flex;
            overflow: hidden;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #F9FAFB;
        }

        .section {
            position: relative;
            padding-bottom: 2rem;
            padding-top: 2.5rem;
            box-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            background-color: #ffffff;

            @media (min-width: 640px) {
                padding-left: 2.5rem;
                padding-right: 2.5rem;
                border-radius: 0.5rem;
                max-width: 32rem;
            }
        }

        .article-1 {
            max-width: 28rem;
        }

        .header {
            display: flex;
            gap: 6rem;
            text-align: center;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            margin-bottom: 20px;
            /*background-color: #186ee6;*/
        }

        h1 {
            font-size: 3rem;
            color: #424242;
            text-align: center;
        }

        .img-potencia-logo {
            height: 60px;
            width: 160px;
            /*background-color: #4B5563;*/
        }

        .img-cotecmar-logo {
            height: 120px;
            width: 120px;
            /*background-color: #4B5563;*/
        }


        .divide-1 {
            border-top-width: 1px;
        }

        .article-2 {
            margin-top: 1rem;
            font-size: 1rem;
            line-height: 1.5rem;
            line-height: 1.75rem;
            color: #4B5563;
        }

        ul {
            margin-top: 1rem;
            margin-bottom: 3rem;
        }

        ul>li {
            display: flex;
            align-items: center;
            margin-bottom: 14px;
        }

        svg {
            flex: none;
            fill: #e0f2fe;
            stroke: #0ea5e9;
            width: 1.5rem;
            height: 1.5rem;
            stroke-width: 2;
        }

        .link-TOP {
            color: rgb(14, 165, 233)
        }

        hover:link-TOP {
            color: rgb(2, 67, 100)
        }

        .article-3 {
            padding-top: 2rem;
            font-size: 1rem;
            line-height: 1.5rem;
            font-weight: 600;
            line-height: 1.75rem;
        }
    </style>
</head>

{{-- <body>
    <div class="container">
        <div class="header">
            <a href="http://www.cotecmar.com" target="_blank" noreferrer noopener nofollow>
                <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/CotecmarLogo.png"
                    alt="Cotecmar Logo" type="logo" class="logo" />
        </div>
        </a>
        <h2>Estimado <span class="employee">{{ $employee }}</span></h2>
        <p>{{ auth()->user()->name }} le ha asignado el/los siguiente(s) equipo(s): </p>
        @foreach ($tools as $tool)
            <ul>
                <li>{{ $tool->category_id }}</li>
            </ul>
        @endforeach
        <p class="footer">¡Gracias por utilizar nuestro servicio!</p>
    </div>
</body> --}}

<body>
    <main class="main">
        <section class="section">
            <article class="article-1">
                <div class="header">
                        <img src="{{ public_path() . '/public/images/Potencia_mundial_de_la_vida_AZUL.png' }}" alt="Cotecmar Logo" type="logo"
                            class="img-potencia-logo" />
                        <a href="http://www.cotecmar.com" target="_blank" noreferrer noopener nofollow>
                            <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/CotecmarLogo.png"
                                alt="Cotecmar Logo" type="logo" class="img-cotecmar-logo" />
                        </a>
                </div>
                <div>
                    <h1>TOP</h1>
                </div>
                <div class="divide-1">
                    <div class="article-2">
                        <p>An advanced online playground for Tailwind CSS, including support for things like:</p>
                        <ul>
                            <li>
                                <svg stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="11" />
                                    <path d="m8 13 2.165 2.165a1 1 0 0 0 1.521-.126L16 9" fill="current" />
                                </svg>
                                <p style="margin-left:1rem;">Holaaaa</p>
                            </li>
                        </ul>
                        <p>Estamos para servirte.</p>
                    </div>
                    <div class="article-3">
                        <p style="color:rgb(17, 24, 39)">Para más información visitar: </p>
                        <p>
                            <a href="https://top.cotecmar.com/" class="link-TOP">
                                https://top.cotecmar.com/
                                &rarr;</a>
                        </p>
                    </div>
                </div>
                </div>
                </div>
    </main>
</body>
