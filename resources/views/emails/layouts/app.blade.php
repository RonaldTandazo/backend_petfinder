<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PetFinder')</title>
</head>

<body style="margin:0; padding:0; background-color:#f1f5f3; font-family:Arial,Helvetica,sans-serif; color:#26332c;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#f1f5f3; padding:35px 12px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden;">

                    {{-- ENCABEZADO --}}
                    <x-emails::header
                        :title="View::yieldContent('header_title')"
                        :description="View::yieldContent('header_description')"
                        :illustration="View::yieldContent('illustration', '🐶 🐱')"
                    />

                    {{-- CONTENIDO --}}
                    <tr>
                        <td style="padding:32px 30px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- PIE --}}
                    <x-emails::footer />
                </table>

                {{-- TEXTO EXTERIOR --}}
                <p style="margin:18px 0 0; color:#a0aaa4; font-size:11px;">
                    &copy; {{ date('Y') }} PetFinder. Todos los derechos reservados.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>