@props([
  'url',
])
<article>
<a href="{{ $url }}" class="inner-footer" target="_blank" noreferrer noopener nofollow >{{ config('app.name') }} &rarr;</a>
</article>
