@props(['url', 'public_path_img', 'resource_path_img'])
<div class="container_logo">
    <img src="http://127.0.0.1:8000/images/Potencia_mundial_de_la_vida_AZUL.png" class="logo_gobierno" alt="Gobierno_Logo">
    <a href="{{ $url }}">
        <img src="{{ $resource_path_img }}" class="logo_cotecmar" alt="Cotecmar_Logo">
    </a>
</div>
<br>
