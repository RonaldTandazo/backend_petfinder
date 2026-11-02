@extends('web.layouts.web')

@section('title', 'Restablecer Contraseña - PetFinder')

@section('content')
    <h1 class="form-title">Restablecer Contraseña</h1>

    <p class="subtitle">
        Introduce tu nueva contraseña para recuperar el acceso a tu cuenta.
    </p>

    {{-- ERRORES GENERALES --}}
    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <strong>No se pudo restablecer la contraseña.</strong>

            <ul style="margin-bottom: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="reset-password-form"
        method="POST"
        action="{{ route('password.reset.web') }}"
    >
        @csrf

        <input type="hidden" name="token" value="{{ old('token', $token) }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">
        <input type="hidden" name="account_type" value="{{ old('account_type', $accountType) }}">

        {{-- NUEVA CONTRASEÑA --}}
        <div class="field">
            <label for="password">Nueva Contraseña</label>

            <div class="input-wrapper">
                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Introduce tu nueva contraseña"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    @if ($errors->has('password')) aria-invalid="true" @endif
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword('password', this)"
                    aria-label="Mostrar contraseña"
                >◉</button>
            </div>

            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- REQUISITOS DE CONTRASEÑA --}}
        <div class="requirements">
            <p class="requirements-title">
                Tu contraseña debe contener:
            </p>

            <div class="requirement-item" id="req-length">
                <span class="requirement-icon">✓</span>
                <span>Mínimo 8 caracteres</span>
            </div>

            <div class="requirement-item" id="req-letter">
                <span class="requirement-icon">✓</span>
                <span>Al menos una letra</span>
            </div>

            <div class="requirement-item" id="req-digit">
                <span class="requirement-icon">✓</span>
                <span>Al menos un número</span>
            </div>
        </div>

        {{-- CONFIRMAR CONTRASEÑA --}}
        <div class="field">
            <label for="password_confirmation">
                Confirmar Contraseña
            </label>

            <div class="input-wrapper">
                <span class="input-icon">🔒</span>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Confirma tu nueva contraseña"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword('password_confirmation', this)"
                    aria-label="Mostrar contraseña"
                >
                    ◉
                </button>
            </div>

            <p
                class="error"
                id="confirmation-error"
                style="display: none;"
                role="alert"
            >
                Las contraseñas no coinciden.
            </p>

            @error('password_confirmation')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- BOTÓN DE ENVÍO --}}
        <button
            type="submit"
            id="submit-button"
            class="submit-button"
            disabled
        >
            <span id="submit-text">Cambiar Contraseña</span>
        </button>
    </form>

    {{-- AVISO DE SEGURIDAD --}}
    <div class="security-notice">
        <span class="security-icon">🛡️</span>

        <span>
            Tu cuenta estará protegida con tu nueva contraseña.
            No compartas tus credenciales con otras personas.
        </span>
    </div>
@endsection

@push('styles')
<style>
    .form-title {
        margin: 0;
        color: #123d2b;
        font-size: 29px;
        text-align: center;
        line-height: 1.3;
    }

    .subtitle {
        margin: 14px 0 30px;
        color: #718078;
        font-size: 15px;
        line-height: 1.7;
        text-align: center;
    }

    .field {
        margin-bottom: 20px;
    }

    .field label {
        display: block;
        margin-bottom: 9px;
        color: #315441;
        font-size: 13px;
        font-weight: bold;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        border: 1px solid #dce5df;
        border-radius: 12px;
        background: #f9fbf9;
        transition: border-color .2s, box-shadow .2s, opacity .2s;
    }

    .input-wrapper:focus-within {
        border-color: #168653;
        box-shadow: 0 0 0 3px rgba(22, 134, 83, .10);
    }

    .input-icon {
        padding-left: 15px;
        color: #168653;
        font-size: 19px;
    }

    .input-wrapper input {
        width: 100%;
        min-width: 0;
        padding: 16px 12px;
        border: none;
        outline: none;
        background: transparent;
        color: #26332c;
        font-size: 15px;
    }

    .input-wrapper input::placeholder {
        color: #a0aaa4;
    }

    .toggle-password {
        padding: 10px 14px;
        border: none;
        background: transparent;
        color: #718078;
        cursor: pointer;
        font-size: 17px;
    }

    .toggle-password:hover {
        color: #168653;
    }

    .requirements {
        margin: -7px 0 22px;
        padding: 14px 16px;
        border-radius: 10px;
        background: #f7faf8;
        border: 1px solid #e5eee8;
    }

    .requirements-title {
        margin: 0 0 10px;
        color: #315441;
        font-size: 12px;
        font-weight: bold;
    }

    .requirement-item {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 7px 0;
        color: #89978f;
        font-size: 12px;
        transition: color .2s;
    }

    .requirement-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 17px;
        height: 17px;
        border: 1px solid #cbd5ce;
        border-radius: 50%;
        font-size: 10px;
        font-weight: bold;
    }

    .requirement-item.met {
        color: #168653;
    }

    .requirement-item.met .requirement-icon {
        background: #dcfce7;
        border-color: #168653;
        color: #168653;
    }

    .error {
        margin: 7px 0 0;
        color: #dc2626;
        font-size: 12px;
    }

    .alert {
        margin-bottom: 22px;
        padding: 13px 15px;
        border-radius: 9px;
        font-size: 13px;
        line-height: 1.6;
    }

    .alert-error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .submit-button {
        display: block;
        width: 100%;
        margin-top: 28px;
        padding: 17px 20px;
        border: none;
        border-radius: 11px;
        background: #168653;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background .2s, transform .2s, opacity .2s;
    }

    .submit-button:hover {
        background: #116c42;
        transform: translateY(-1px);
    }

    .submit-button:disabled {
        opacity: .55;
        cursor: not-allowed;
        transform: none;
    }

    .submit-button:disabled:hover {
        background: #168653;
    }

    .input-wrapper.loading {
        opacity: .65;
        pointer-events: none;
    }

    @media (max-width: 480px) {
        .form-title {
            font-size: 24px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const passwordInput = document.getElementById('password');
    const confirmationInput = document.getElementById('password_confirmation');
    const confirmationError = document.getElementById('confirmation-error');

    const resetForm = document.getElementById('reset-password-form');
    const submitButton = document.getElementById('submit-button');
    const submitText = document.getElementById('submit-text');

    let isSubmitting = false;

    function updateRequirement(id, isMet) {
        const element = document.getElementById(id);
        element.classList.toggle('met', isMet);
    }

    function validatePasswordRequirements() {
        const password = passwordInput.value;

        const hasMinimumLength = password.length >= 8;
        const hasLetter = /[A-Za-z]/.test(password);
        const hasDigit = /[0-9]/.test(password);

        updateRequirement('req-length', hasMinimumLength);
        updateRequirement('req-letter', hasLetter);
        updateRequirement('req-digit', hasDigit);

        return hasMinimumLength && hasLetter && hasDigit;
    }

    function validateConfirmation() {
        const password = passwordInput.value;
        const confirmation = confirmationInput.value;

        const passwordsMatch =
            confirmation.length > 0 && password === confirmation;

        const showError =
            confirmation.length > 0 && !passwordsMatch;

        confirmationError.style.display = showError ? 'block' : 'none';

        confirmationInput.setCustomValidity(
            showError ? 'Las contraseñas no coinciden.' : ''
        );

        return passwordsMatch;
    }

    function validateForm() {
        const passwordIsValid = validatePasswordRequirements();
        const confirmationIsValid = validateConfirmation();

        const isValid =
            passwordIsValid &&
            confirmationIsValid &&
            passwordInput.value.length > 0 &&
            confirmationInput.value.length > 0;

        submitButton.disabled = !isValid || isSubmitting;
    }

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';
        button.textContent = isPassword ? '◎' : '◉';

        button.setAttribute(
            'aria-label',
            isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    }

    passwordInput.addEventListener('input', validateForm);
    confirmationInput.addEventListener('input', validateForm);

    resetForm.addEventListener('submit', function (event) {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        // Validación final antes de enviar.
        validateForm();

        if (!resetForm.checkValidity() || submitButton.disabled) {
            event.preventDefault();
            resetForm.reportValidity();
            return;
        }

        isSubmitting = true;

        submitButton.disabled = true;
        submitText.textContent = 'Procesando...';

        // Bloquear los campos sin impedir que sus valores se envíen.
        passwordInput.readOnly = true;
        confirmationInput.readOnly = true;

        document.querySelectorAll('.input-wrapper').forEach(wrapper => {
            wrapper.classList.add('loading');
        });
    });

    validateForm();
</script>
@endpush