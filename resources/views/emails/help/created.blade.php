
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva solicitud - PetFinder</title>
</head>

<body style="margin:0; padding:0; background-color:#f1f5f3; font-family:Arial,Helvetica,sans-serif; color:#26332c;">

    @php
        $isSupport = $type === 'support';
        $requestType = $isSupport ? 'Soporte' : 'Feedback';
        $requestColor = $isSupport ? '#2563eb' : '#0f9f6e';
        $requestBackground = $isSupport ? '#eff6ff' : '#ecfdf5';
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#f1f5f3; padding:35px 12px;">

        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden;">

                    <!-- ENCABEZADO -->
                    <tr>
                        <td style="background-color:#123d2b; padding:35px 30px 32px; text-align:center;">

                            <div style="display:inline-block; background-color:#ffffff; padding:10px 18px; border-radius:30px; margin-bottom:18px;">
                                <span style="font-size:20px; font-weight:bold; color:#123d2b; letter-spacing:0.5px;">
                                    PetFinder
                                </span>
                            </div>

                            <h1 style="margin:0; color:#ffffff; font-size:25px; line-height:1.3; font-weight:bold;">
                                Nueva solicitud recibida
                            </h1>

                            <p style="margin:10px 0 0; color:#c5dfd0; font-size:14px; line-height:1.6;">
                                Se ha registrado una nueva solicitud desde PetFinder.
                            </p>

                        </td>
                    </tr>

                    <!-- FRANJA DECORATIVA -->
                    <tr>
                        <td style="height:5px; background-color:#22c55e; font-size:0; line-height:0;">
                            &nbsp;
                        </td>
                    </tr>

                    <!-- CONTENIDO -->
                    <tr>
                        <td style="padding:32px 30px;">

                            <!-- TIPO DE SOLICITUD -->
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:{{ $requestBackground }}; padding:8px 14px; border-radius:20px;">
                                        <span style="font-size:12px; font-weight:bold; color:{{ $requestColor }}; letter-spacing:0.5px;">
                                            ● NUEVO {{ strtoupper($requestType) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:18px 0 8px; color:#1f3027; font-size:22px; line-height:1.3;">
                                {{ $isSupport ? 'Solicitud de soporte' : 'Nuevo comentario o sugerencia' }}
                            </h2>

                            <p style="margin:0 0 26px; color:#718078; font-size:14px; line-height:1.7;">
                                {{ $isSupport
                                    ? 'Un usuario necesita asistencia. A continuación encontrarás los detalles de su solicitud.'
                                    : 'Un usuario ha compartido una opinión o sugerencia para contribuir a mejorar PetFinder.' }}
                            </p>

                            <!-- ASUNTO -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color:#f7faf8; border:1px solid #e5eee8; border-radius:10px;">

                                <tr>
                                    <td style="padding:20px 22px;">

                                        <p style="margin:0 0 8px; color:#89978f; font-size:11px; font-weight:bold; letter-spacing:1px;">
                                            ASUNTO
                                        </p>

                                        <p style="margin:0; color:#263b2f; font-size:16px; font-weight:bold; line-height:1.5;">
                                            {{ $data['subject'] ?? 'Sin asunto' }}
                                        </p>

                                    </td>
                                </tr>
                            </table>

                            <!-- DESCRIPCIÓN -->
                            <h3 style="margin:28px 0 12px; color:#263b2f; font-size:16px;">
                                Descripción
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color:#f7faf8; border-left:4px solid #22a06b; border-radius:6px;">

                                <tr>
                                    <td style="padding:18px 20px;">

                                        <p style="margin:0; color:#46574d; font-size:14px; line-height:1.8; white-space:pre-line;">
                                            {{ $data['comment'] ?? 'Sin descripción' }}
                                        </p>

                                    </td>
                                </tr>
                            </table>

                            {{-- FOTOGRAFÍAS --}}
                            @if (!empty($data['pictures']) && count($data['pictures']) > 0)
                                <h3 style="margin:30px 0 8px; color:#263b2f; font-size:16px;">
                                    Fotografías adjuntas
                                </h3>

                                <p style="margin:0 0 16px; color:#89978f; font-size:13px;">
                                    El usuario ha adjuntado las siguientes imágenes:
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    @foreach (array_chunk($data['pictures'], 2) as $row)
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
                                                                        alt="Fotografía {{ $loop->parent->iteration }}"
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

                            <!-- MENSAJE FINAL -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-top:30px; background-color:#f0fdf4; border-radius:10px;">

                                <tr>
                                    <td style="padding:17px 20px;">

                                        <p style="margin:0; color:#286343; font-size:13px; line-height:1.7;">
                                            <strong>Recuerda:</strong> esta solicitud requiere revisión para dar seguimiento a las necesidades y comentarios de los usuarios de PetFinder.
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- PIE DE CORREO -->
                    <tr>
                        <td style="background-color:#f7faf8; border-top:1px solid #e8eee9; padding:25px 30px; text-align:center;">

                            <p style="margin:0 0 8px; color:#315441; font-size:15px; font-weight:bold;">
                                PetFinder
                            </p>

                            <p style="margin:0 0 12px; color:#89978f; font-size:12px; line-height:1.6;">
                                Conectando mascotas con quienes pueden darles un hogar.
                            </p>

                            <p style="margin:0; color:#a0aaa4; font-size:11px; line-height:1.6;">
                                Este correo fue generado automáticamente por PetFinder.<br>
                                Por favor, no respondas directamente a este mensaje.
                            </p>

                        </td>
                    </tr>

                </table>

                <!-- TEXTO EXTERIOR -->
                <p style="margin:18px 0 0; color:#a0aaa4; font-size:11px;">
                    &copy; {{ date('Y') }} PetFinder. Todos los derechos reservados.
                </p>

            </td>
        </tr>

    </table>

</body>
</html>