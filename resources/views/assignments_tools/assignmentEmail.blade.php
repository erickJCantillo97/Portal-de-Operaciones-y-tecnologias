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
</head>

<body>
    <a href="http://www.cotecmar.com" target="_blank" noreferrer noopener nofollow>
        <img src="https://www.cotecmar.com/sites/default/files/media/imagenes/2021-12/CotecmarLogo.png"
            alt="Cotecmar Logo" type="logo" style="width: 100px; height: 100px;" />
    </a>
    <h1>Estimado {{ $employee->name }}</h1>
    <h3>{{ auth()->user()->name }} le ha asignado el/los siguiente(s) equipo(s): </h3>
    @foreach ($tools as $tool)
        <ul>
            <li>{{ $tools->category_id }}</li>
        </ul>
    @endforeach
    <br>
    <hr size="2px" color="black">
