@props([
  'inner_footer_url',
])
<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>
{{-- @component('mail::image',  [
  'url' => 'http://www.cotecmar.com',
  'public_path_img' => 'images/Potencia_mundial_de_la_vida_AZUL.png',
  'resource_path_img' => 'https://minciencias.gov.co/sites/default/files/cotecmar.png'
  ])
@endcomponent --}}
{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset




{{-- Inner Footer --}}
Para más información visitar:
@component('mail::inner-footer',  ['url' => $inner_footer_url])@endcomponent

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ config('app.name') }}. @lang('All rights reserved.') {{ date('Y') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
