@props([
    'title',
    'description',
])

<tr>
    <td bgcolor="#123d2b"
        style="background-color:#123d2b; padding:35px 25px 32px; text-align:center;">

        {{-- Identidad de marca --}}
        <table cellpadding="0" cellspacing="0" border="0" align="center"
            style="margin:0 auto 26px;">
            <tr>
                <td bgcolor="#ffffff"
                    style="background-color:#ffffff; padding:9px 20px; border-radius:30px; text-align:center;">
                    <span style="font-family:Arial,Helvetica,sans-serif; color:#123d2b; font-size:21px; line-height:1.4; font-weight:bold; letter-spacing:0.7px;">
                        PetFinder
                    </span>
                </td>
            </tr>
        </table>

        {{-- Título del correo --}}
        <h1 style="margin:0; color:#ffffff; font-family:Arial,Helvetica,sans-serif; font-size:25px; line-height:1.3; font-weight:bold;">
            {{ $title }}
        </h1>

        {{-- Descripción --}}
        <p style="margin:12px 0 0; color:#c5dfd0; font-family:Arial,Helvetica,sans-serif; font-size:14px; line-height:1.7;">
            {{ $description }}
        </p>

    </td>
</tr>

{{-- Separador verde --}}
<tr>
    <td bgcolor="#22c55e"
        height="6"
        style="height:6px; background-color:#22c55e; font-size:0; line-height:0;">
        &nbsp;
    </td>
</tr>