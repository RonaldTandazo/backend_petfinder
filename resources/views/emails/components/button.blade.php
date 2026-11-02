@props([
    'url',
    'label',
    'background' => '#168653',
])

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding:12px 0 25px;">
            <a href="{{ $url }}"
                target="_blank"
                style="display:inline-block; background-color:{{ $background }}; color:#ffffff; padding:15px 30px; border-radius:8px; font-size:15px; font-weight:bold; text-decoration:none;">
                {{ $label }}
            </a>
        </td>
    </tr>
</table>