@extends('emails.layouts.app')

@section('title', 'Restablecimiento de contraseña - PetFinder')

@section('header_title', 'Recuperación de contraseña')

@section('header_description', 'Hemos recibido una solicitud para restablecer tu contraseña.')

@section('content')
    <x-emails::badge label="SEGURIDAD DE LA CUENTA" />

    <h2 style="margin:20px 0 8px; color:#1f3027; font-size:22px; line-height:1.3;">
        Hola, {{ $name ?? 'usuario' }}
    </h2>

    <p style="margin:0 0 24px; color:#718078; font-size:14px; line-height:1.8;">
        Recibimos una solicitud para cambiar la contraseña de tu cuenta de PetFinder.
        Para continuar, utiliza el siguiente botón:
    </p>

    <x-emails::button
        :url="$resetUrl"
        label="Restablecer contraseña"
    />

    <x-emails::info-box title="Información importante">
        <p style="margin:0 0 10px; color:#46574d; font-size:13px; line-height:1.8;">
            Por motivos de seguridad, este enlace tiene una vigencia de
            <strong>1 hora</strong> y solo puede utilizarse una vez.
        </p>

        <p style="margin:0; color:#46574d; font-size:13px; line-height:1.8;">
            Si no solicitaste restablecer tu contraseña, puedes ignorar este correo.
            Tu contraseña actual seguirá siendo la misma.
        </p>
    </x-emails::info-box>

    {{-- ENLACE ALTERNATIVO --}}
    <p style="margin:25px 0 8px; color:#89978f; font-size:12px; line-height:1.7;">
        Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:
    </p>

    <p style="margin:0; word-break:break-all; font-size:12px; line-height:1.7;">
        <a href="{{ $resetUrl }}" style="color:#168653; text-decoration:underline;">
            {{ $resetUrl }}
        </a>
    </p>

    <x-emails::info-box
        :title="null"
        background="#f0fdf4"
        border="0"
        marginTop="30px"
        padding="17px 20px"
    >
        <p style="margin:0; color:#286343; font-size:13px; line-height:1.7;">
            <strong>Recuerda:</strong> PetFinder nunca te solicitará tu contraseña
            por correo electrónico. No compartas este enlace con otras personas.
        </p>
    </x-emails::info-box>
@endsection