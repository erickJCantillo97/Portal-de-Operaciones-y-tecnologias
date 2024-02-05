@props([
    'url',
    'public_path_img',
    'resource_path_img',
])
<div class="container_logo">
    <img src="{{ asset($public_path_img)}}" class="logo_gobierno" alt="Gobierno_Logo">
    <a href="{{ $url }}">
        <img src="{{ $resource_path_img }}" class="logo_cotecmar" alt="Cotecmar_Logo">
    </a>
</div>
<br>
