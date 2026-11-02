@extends('emails.layouts.app')

@php
    $isSupport = $type === 'support';

    $requestType = $isSupport ? 'Soporte' : 'Feedback';
    $requestColor = $isSupport ? '#2563eb' : '#0f9f6e';
    $requestBackground = $isSupport ? '#eff6ff' : '#ecfdf5';
@endphp

@section('title', 'Nueva solicitud - PetFinder')

@section('header_title', 'Nueva solicitud recibida')

@section('header_description', 'Se ha registrado una nueva solicitud desde PetFinder.')

@section('content')
    <x-emails::badge
        :label="'NUEVO ' . strtoupper($requestType)"
        :color="$requestColor"
        :background="$requestBackground"
    />

    <h2 style="margin:18px 0 8px; color:#1f3027; font-size:22px; line-height:1.3;">
        {{ $isSupport ? 'Solicitud de soporte' : 'Nuevo comentario o sugerencia' }}
    </h2>

    <p style="margin:0 0 26px; color:#718078; font-size:14px; line-height:1.7;">
        {{ $isSupport
            ? 'Un usuario necesita asistencia. A continuación encontrarás los detalles de su solicitud.'
            : 'Un usuario ha compartido una opinión o sugerencia para contribuir a mejorar PetFinder.' }}
    </p>

    {{-- ASUNTO --}}
    <x-emails::info-box>
        <p style="margin:0 0 8px; color:#89978f; font-size:11px; font-weight:bold; letter-spacing:1px;">
            ASUNTO
        </p>

        <p style="margin:0; color:#263b2f; font-size:16px; font-weight:bold; line-height:1.5;">
            {{ $data['subject'] ?? 'Sin asunto' }}
        </p>
    </x-emails::info-box>

    {{-- DESCRIPCIÓN --}}
    <h3 style="margin:28px 0 12px; color:#263b2f; font-size:16px;">
        Descripción
    </h3>

    <x-emails::info-box
        :title="null"
        border="0"
        accent="#22a06b"
        padding="18px 20px"
    >
        <p style="margin:0; color:#46574d; font-size:14px; line-height:1.8; white-space:pre-line;">
            {{ $data['comment'] ?? 'Sin descripción' }}
        </p>
    </x-emails::info-box>

    {{-- FOTOGRAFÍAS --}}
    <x-emails::image-gallery :pictures="$data['pictures'] ?? []" />

    {{-- MENSAJE FINAL --}}
    <x-emails::info-box
        :title="null"
        background="#f0fdf4"
        border="0"
        marginTop="30px"
        padding="17px 20px"
    >
        <p style="margin:0; color:#286343; font-size:13px; line-height:1.7;">
            <strong>Recuerda:</strong> esta solicitud requiere revisión para dar seguimiento
            a las necesidades y comentarios de los usuarios de PetFinder.
        </p>
    </x-emails::info-box>
@endsection