@props([
    'label',
    'color' => '#0f9f6e',
    'background' => '#ecfdf5',
])

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="background-color:{{ $background }}; padding:8px 14px; border-radius:20px;">
            <span style="font-size:12px; font-weight:bold; color:{{ $color }}; letter-spacing:0.5px;">
                ● {{ $label }}
            </span>
        </td>
    </tr>
</table>