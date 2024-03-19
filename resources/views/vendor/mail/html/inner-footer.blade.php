@props([
  'url',
  'text'
])
<article class="container_logo">
<a href="{{ $url }}" class="inner-footer" target="_blank" noreferrer noopener nofollow >{{ $text ?? config('app.name') }} &rarr;</a>

<img src="https://top.cotecmar.com/svg/cotecmar-logo.svg" class="logo_cotecmar" alt="kjzdznsdfkjsa">
</article>