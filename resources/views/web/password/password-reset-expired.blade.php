@extends('web.layouts.web')

@section('title', 'Enlace no válido - PetFinder')

@section('content')
    <div class="status-icon">
        !
    </div>

    <span class="status-label">
        ENLACE NO DISPONIBLE
    </span>

    <h1 class="status-title">
        Enlace no válido o expirado
    </h1>

    <p class="subtitle">
        El enlace que utilizaste ya no es válido o ha expirado.
        Por motivos de seguridad, no es posible continuar con
        el restablecimiento de tu contraseña.
    </p>

    {{-- INFORMACIÓN --}}
    <div class="info-box">
        <strong>¿Qué puedes hacer?</strong>

        <p>
            Solicita nuevamente la recuperación de tu contraseña
            desde la aplicación PetFinder. Recibirás un nuevo enlace
            para completar el proceso.
        </p>
    </div>

    {{-- AVISO DE SEGURIDAD --}}
    <div class="security-notice">
        <span class="security-icon">🛡️</span>

        <span>
            Tu cuenta permanece protegida. Este enlace ya no permite
            modificar tu contraseña.
        </span>
    </div>
@endsection

@push('styles')
    <style>
        .page-card {
            text-align: center;
        }

        .status-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: #fff1f0;
            color: #dc5550;
            font-size: 39px;
            font-weight: bold;
        }

        .status-label {
            display: inline-block;
            padding: 8px 14px;
            margin-bottom: 17px;
            border-radius: 20px;
            background: #fff1f0;
            color: #c2413b;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: .5px;
        }

        .status-title {
            margin: 0;
            color: #123d2b;
            font-size: 27px;
            line-height: 1.3;
        }

        .subtitle {
            margin: 15px 0 25px;
            color: #718078;
            font-size: 15px;
            line-height: 1.8;
        }

        .info-box {
            padding: 18px;
            border: 1px solid #e5eee8;
            border-radius: 12px;
            background: #f7faf8;
            text-align: left;
        }

        .info-box strong {
            display: block;
            margin-bottom: 8px;
            color: #315441;
            font-size: 14px;
        }

        .info-box p {
            margin: 0;
            color: #718078;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 480px) {
            .status-title {
                font-size: 24px;
            }
        }
    </style>
@endpush