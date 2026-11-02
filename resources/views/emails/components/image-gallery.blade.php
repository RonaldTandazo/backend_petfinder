@props([
    'pictures' => [],
])

@if (!empty($pictures))
    <h3 style="margin:30px 0 8px; color:#263b2f; font-size:16px;">
        Fotografías adjuntas
    </h3>

    <p style="margin:0 0 16px; color:#89978f; font-size:13px;">
        El usuario ha adjuntado las siguientes imágenes:
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        @foreach (array_chunk($pictures, 2) as $row)
            <tr>
                @foreach ($row as $picture)
                    <td width="50%" valign="top" style="padding:5px;">
                        @if (!empty($picture['url']))
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color:#f7faf8; border:1px solid #e5eee8; border-radius:10px; overflow:hidden;">
                                <tr>
                                    <td align="center" style="padding:8px;">
                                        <img
                                            src="{{ $picture['url'] }}"
                                            alt="Fotografía adjunta"
                                            width="250"
                                            style="display:block; width:100%; max-width:250px; height:auto; border:0; border-radius:7px;"
                                        />
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" style="padding:8px 5px 12px;">
                                        <a href="{{ $picture['url'] }}"
                                            target="_blank"
                                            style="color:#168653; font-size:12px; font-weight:bold; text-decoration:none;">
                                            Ver imagen original
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        @endif
                    </td>
                @endforeach

                @if (count($row) === 1)
                    <td width="50%" style="padding:5px;">&nbsp;</td>
                @endif
            </tr>
        @endforeach
    </table>
@endif