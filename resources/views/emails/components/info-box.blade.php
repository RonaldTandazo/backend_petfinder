@props([
    'title' => null,
    'background' => '#f7faf8',
    'border' => '1px solid #e5eee8',
    'accent' => null,
    'marginTop' => '0',
    'padding' => '20px 22px',
])

<table width="100%" cellpadding="0" cellspacing="0" border="0"
    style="
        margin-top:{{ $marginTop }};
        background-color:{{ $background }};
        border:{{ $border }};
        border-left:{{ $accent ? '4px solid ' . $accent : '0' }};
        border-radius:10px;
    ">
    <tr>
        <td style="padding:{{ $padding }};">
            @if ($title)
                <p style="margin:0 0 10px; color:#263b2f; font-size:14px; font-weight:bold;">
                    {{ $title }}
                </p>
            @endif

            {{ $slot }}
        </td>
    </tr>
</table>