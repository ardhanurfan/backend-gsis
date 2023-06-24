@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Ganesha Student Innovation Summit')
<img src="/logo_gsis.svg" class="logo" alt="GSIS Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
