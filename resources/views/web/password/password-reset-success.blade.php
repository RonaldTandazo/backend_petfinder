@extends('web.layouts.web')

@section('title', 'Contraseña Restablecida - PetFinder')

@section('content')
    <div class="success-container">
        <div class="success-icon">
            ✓
        </div>

        <h1 class="form-title">
            ¡Contraseña Restablecida!
        </h1>

        <p class="subtitle">
            Tu contraseña se ha actualizado correctamente.
            Ya puedes iniciar sesión en PetFinder utilizando tus nuevas credenciales.
        </p>

        <div class="security-notice">
            <span class="security-icon">🛡️</span>

            <span>
                Tu cuenta está protegida con tu nueva contraseña.
                Recuerda no compartir tus credenciales.
            </span>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .success-container {
        text-align: center;
    }

    .success-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 75px;
        height: 75px;
        margin: 0 auto 25px;
        border-radius: 50%;
        background: #dcfce7;
        color: #168653;
        font-size: 38px;
        font-weight: bold;
    }

    .form-title {
        margin: 0;
        color: #123d2b;
        font-size: 27px;
        line-height: 1.3;
    }

    .subtitle {
        margin: 14px 0 30px;
        color: #718078;
        font-size: 15px;
        line-height: 1.7;
    }
</style>
@endpush