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

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1725a4;
            padding: 20px;
            padding-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 200px;
            height: 200px;
            max-width: 100px;
            height: auto;
        }

        .employee{
            color: #596aff
        }

        h2 {
            color: #ffffff;
        }

        p {
            color: #ffffff;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #ffffff;
        }
    </style>
</head>

<body>
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
</body>
