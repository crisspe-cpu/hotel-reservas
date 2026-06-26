{{--
    ╔══════════════════════════════════════════════════════════════╗
    ║  PERSONALIZACIÓN DEL LOGIN - HOTEL                          ║
    ╠══════════════════════════════════════════════════════════════╣
    ║  FONDO:  Cambia $hotelBackground (línea ~20)                ║
    ║          → URL de imagen o color CSS (ej: '#1a1a2e')        ║
    ║  IMAGEN: Cambia $sideImage (línea ~21)                      ║
    ║          → URL de la foto que aparece a la izquierda        ║
    ║  LOGO:   Cambia $hotelName (línea ~22) y $hotelLogo (~23)   ║
    ╚══════════════════════════════════════════════════════════════╝
--}}

@php
    // ▼▼▼ PERSONALIZA AQUÍ ▼▼▼
    $hotelBackground = asset('https://lh3.googleusercontent.com/rd-gg-dl/AFfU-fJS_QMVSk_G1qcm2zEUkGI53wgDZE-7vpNIQGdlXh1sbj-xQzf-gnEmLNtfnu5ht03HaWH2LqlLZljgfkLl44YY2eaVNFiQwUp5M4Cs8fOC3m25X9SZcfi1LRV4jFuEl-YoKEdl7Cm5_LnNKTMcaM0_MonD1kEGjhVZ-1vbZNlPaiFoXp1TegQBksN1QmEXFzOu8GrO8CU0r7LufqnyHxMGXNxTn9tf6hQ7dpGv5D_f0xeTmU0Tt87s0JdnjYkVUJiDCsKZXxi_RMtFYz6NcWkA-pXndTg8nYDzOfYwqjhoTh0TEO9CuUzXrmAXtJ3mDufKIi9glpUco_Ws42z7HlNF98FMonIH-tlfsGQrW5U75Ikj3kfjVH7GbHDNpj87wJTud4rjjqlCLG-aEYYLQ_I3WO52r0-puWSNJmIy3ACKIewPcqJBMQok2POHzeIVb35GtQxWEi8AQP6we86nPqUz0YwSEF4lHswSw3_kAioaYZLxgci-EJS_6bABIc5VkDHmDLqrK2rHREBUj2EGzEjaAu78nW-CdW3j09WU6jfifhXbFpkEloOcSMSEVfkrC9Ud70zfZDj2DPXHnD7MyQNfJOFwSKvZk2GualgObgljC2qbmn5751pnI01yJDDrR281GWY0kyo0vwrnuhejpNTHKwv-zxW7uV-8QNJ0pN6n6Xi7hRZ1WZAkWbKf5t9WZFJFcAdJ8nLJ3fONmw9dQReZP2i9_J8_y-8I6HwTkdY9q9WAGpRaLZxaMb9wUuqHNQxNj2cRetDmwv-Oi-bGZrQ9ADhRoxmGCw3EwzTR2FjoghlVEXA1PIO-O9BFNun_XTMQOcrI6zXhJ-n05WwVwghleGgKzt4d8GcyVvYAJZzMKLZD3gBxn55nGG2HEb2zkSgqn-YZ-i2TJILLHzf9a8ciTvQoSr8bigVYEAodHQ_JAjdkbEejCDmD8WH33ViB5EC84-hVuZwjciiyEpTh3bsGMPQfTS06xNkZuF6JRaQkBZTJxGk91Vad2VIdGU6ylaQzobXyyj2DUowZJop1vNsADQ_SnrHaMEqfYfz87ncxJViDlBjuNnTkkyRVw2mbbFHHryNU7MJAN_p0Q6xyLnRUZPPHJv9xkYqhbWPyPrNeQjyzLsnPoMyb7uHQ9NwOPfZ4iF9Eei2vqOj5K45AJDkeH-vahmEXZ-ryQPtDKOszOwMTCbcolvmAB224DtpAUbcE3R4_CzWtidBykVWSdmmOaLKWLE1wZDEJXM0_Ka0TahnkKuMnuYGf=s1024-rj');   // URL o ruta de tu fondo
    $sideImage       = asset('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsr_Fas2MkBm7igiulT9T6H_KhOjSKAjtkriRbDzVqEdE1FrKrovod8Pc&s=10'); // Foto lateral del hotel
    $hotelName       = 'Hotel Palacio del Rey';               // Nombre del hotel
    $hotelLogo       = null;                           // URL de tu logo (o null para usar solo texto)
    // ▲▲▲ FIN DE PERSONALIZACIÓN ▲▲▲
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hotelName }} — Iniciar sesión</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Reset & base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            /* FONDO: imagen de hotel con overlay oscuro */
            background-image:
                linear-gradient(135deg, rgba(0,30,60,.55) 0%, rgba(0,10,30,.45) 100%),
                url('{{ $hotelBackground }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* ── Tarjeta principal ── */
        .card {
            display: flex;
            width: min(860px, 95vw);
            min-height: 480px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.45), 0 0 0 1px rgba(255,255,255,.08);
        }

        /* ── Panel izquierdo: imagen del hotel ── */
        .card__image {
            flex: 0 0 42%;
            background-image: url('{{ $sideImage }}');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card__image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,20,50,.60) 0%, transparent 60%);
        }

        .card__image-caption {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            z-index: 1;
            color: #fff;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: .04em;
            line-height: 1.4;
        }

        /* ── Panel derecho: formulario ── */
        .card__form {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 44px;
        }

        /* Avatar */
        .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #1565c0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }
        .avatar svg { width: 38px; height: 38px; fill: #fff; }

        /* Título */
        .form-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.45rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #1a1a2e;
            margin-bottom: 28px;
        }

        /* Campos */
        .field {
            width: 100%;
            margin-bottom: 16px;
        }
        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 5px;
            letter-spacing: .02em;
        }
        .field input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d0d5dd;
            border-radius: 8px;
            font-size: .92rem;
            color: #1a1a2e;
            background: #f8f9fc;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .field input:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 3px rgba(21,101,192,.12);
            background: #fff;
        }

        /* Errores de validación */
        .field-error {
            font-size: .76rem;
            color: #c0392b;
            margin-top: 4px;
        }

        /* Remember me */
        .remember {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }
        .remember input[type="checkbox"] {
            accent-color: #1565c0;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }
        .remember label {
            font-size: .82rem;
            color: #555;
            cursor: pointer;
        }

        /* Acciones */
        .form-actions {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .link-register {
            font-size: .82rem;
            color: #1565c0;
            text-decoration: underline;
            text-underline-offset: 2px;
            white-space: nowrap;
        }
        .link-register:hover { color: #0d47a1; }

        .btn-login {
            background: #1565c0;
            color: #fff;
            font-size: .88rem;
            font-weight: 500;
            letter-spacing: .04em;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background .2s, transform .1s;
            white-space: nowrap;
        }
        .btn-login:hover { background: #0d47a1; }
        .btn-login:active { transform: scale(.97); }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .card__image { display: none; }
            .card__form  { padding: 36px 28px; }
        }
    </style>
</head>
<body>


    <div class="card">

        {{-- ── Panel izquierdo: foto del hotel ── --}}
        <div class="card__image">
            <div class="card__image-caption">
                {{ $hotelName }}<br>
                <span style="font-size:.85rem; opacity:.8;">Bienvenido de vuelta</span>
            </div>
        </div>

        {{-- ── Panel derecho: formulario ── --}}
        <div class="card__form">

            {{-- Avatar --}}
            <div class="avatar">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
            </div>

            <h1 class="form-title">Iniciar Sesión</h1>

            {{-- Estado de sesión --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" style="width:100%">
                @csrf

                {{-- Correo --}}
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="ejemplo@correo.com"
                           required
                           autofocus
                           autocomplete="username">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="field">
                    <label for="password">Contraseña</label>
                    <input id="password"
                           type="password"
                           name="password"
                           placeholder="••••••••"
                           required
                           autocomplete="current-password">
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="remember">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Recordarme</label>
                </div>

                {{-- Acciones --}}
                <div class="form-actions">
                    

                    <button type="submit" class="btn-login">
                        Iniciar sesión
                    </button>
                </div>

                {{-- Contraseña olvidada (oculto para mantener funcionalidad) --}}
                @if (Route::has('password.request'))
                    <div style="margin-top:14px; text-align:center;">
                        
                    </div>
                @endif

            </form>
        </div>

    </div>

</body>
</html>