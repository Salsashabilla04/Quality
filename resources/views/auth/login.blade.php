<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login · Monitoring Complaint & NCR</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #ffffff;
        }

        /* =========================================
           MAIN LAYOUT
        ========================================= */

        .login-container {
            min-height: 100vh;
            display: flex;
            background: #ffffff;
        }


        /* =========================================
           LEFT PANEL
        ========================================= */

        .login-bg {
            width: 42%;
            min-height: 100vh;

            /* Warna dasar */
            background-color: #1F3768;

            /*
             * GANTI GAMBAR DI SINI
             */
            background-image: url('/images/QA.jpg');


            background-size: cover;
            background-position: 55% center;
            background-repeat: no-repeat;
        }  

        /* =========================================
           RIGHT CONTENT
        ========================================= */

        .login-content {
            width: 58%;
            min-height: 100vh;

            background: #ffffff;

            display: flex;
            flex-direction: column;

            padding: 32px 7%;
        }


        /* =========================================
           BRAND
        ========================================= */

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .brand-logo {
            width: 150px;
            height: 150px;

            object-fit: contain;
        }

        .brand-text {
            line-height: 1.1;
        }

        .brand-name {
            font-size: 21px;
            font-weight: 700;

            color: #25233a;
        }

        .brand-subtitle {
            margin-top: 2px;

            font-size: 15px;
            font-weight: 600;

            color: #0db51b;
        }


        /* =========================================
           LOGIN WRAPPER
        ========================================= */

        .login-form-wrapper {
            width: 100%;
            max-width: 520px;

            margin: auto;
        }


        /* =========================================
           TITLE
        ========================================= */

        .login-title {
            margin: 0;

            text-align: center;

            font-size: 28px;
            font-weight: 700;

            color: #373346;
        }

        .login-description {
            margin: 10px 0 48px;

            text-align: center;

            font-size: 14px;
            line-height: 1.6;

            color: #686273;
        }


        /* =========================================
           ERROR
        ========================================= */

        .error-box {
            margin-bottom: 20px;

            padding: 12px 15px;

            border: 1px solid #fecaca;
            border-radius: 8px;

            background: #fff1f2;

            color: #be123c;

            font-size: 13px;
        }

        /* =========================================
           INPUT
        ========================================= */

        .input-group {
            margin-bottom: 21px;
        }

        .input-label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;
            font-weight: 600;

            color: #454052;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #918c99;

            pointer-events: none;
        }

        .input-field {
            width: 100%;
            height: 52px;

            padding: 0 15px 0 45px;

            border: 1px solid #d6d3db;
            border-radius: 7px;

            outline: none;

            background: #ffffff;

            color: #3e394a;

            font-size: 14px;

            transition: all .2s ease;
        }

        .input-field::placeholder {
            color: #96919f;
        }

        .input-field:focus {
            border-color: #0305a7c8;

            box-shadow:
                0 0 0 3px rgba(13, 181, 27, .08);
        }


        /* =========================================
           REMEMBER
        ========================================= */

        .login-options {
            display: flex;
            align-items: center;

            margin: 5px 0 42px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;

            color: #55505e;

            font-size: 13px;

            cursor: pointer;
        }

        .remember input {
            width: 18px;
            height: 18px;

            accent-color: #0305a7c8;
        }


        /* =========================================
           LOGIN BUTTON
        ========================================= */

        .btn-login {
            width: 100%;
            height: 52px;

            border: none;
            border-radius: 28px;

            background: #1F3768;

            color: #ffffff;

            font-size: 14px;
            font-weight: 700;

            cursor: pointer;

            transition: all .2s ease;
        }

        .btn-login:hover {
            background: #172B52;

            transform: translateY(-1px);

            box-shadow:
                0 8px 20px rgba(40, 75, 179, 0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* =========================================
           FOOTER
        ========================================= */

        .login-footer {
            margin-top: auto;
            padding-top: 30px;

            text-align: center;

            color: #77717f;

            font-size: 11px;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 900px) {

            .login-bg {
                display: none;
            }

            .login-content {
                width: 100%;

                padding: 30px 25px;
            }

            .login-form-wrapper {
                max-width: 500px;
            }
        }


        @media (max-width: 500px) {

            .login-content {
                padding: 25px 20px;
            }

            .role-wrapper,
            .demo-wrapper {
                grid-template-columns: 1fr;
            }

            .login-title {
                font-size: 25px;
            }
        }
    </style>
</head>


<body>

<div class="login-container">


    <!-- =====================================
         LEFT IMAGE / PATTERN
    ====================================== -->

    <div class="login-bg">
        <!--
            Tidak perlu HTML pattern lagi.

            Tinggal masukkan gambar:
            public/images/login-pattern.png
        -->
    </div>


    <!-- =====================================
         RIGHT LOGIN CONTENT
    ====================================== -->

    <div class="login-content">


        <!-- BRAND -->

        <div class="brand">

            <!--
                Kalau punya logo perusahaan,
                ganti src ini.
            -->

            <img
                src="/images/logowb.png"
                alt="Logo"
                class="brand-logo"
            >

        </div>


        <!-- LOGIN FORM -->

        <div class="login-form-wrapper">


            <h1 class="login-title">
                Quality Management Portal
            </h1>

            <p class="login-description">
                Sign in to access Complaint Management & NCR Monitoring System.
            </p>


            @if ($errors->any())

                <div class="error-box">
                    {{ $errors->first() }}
                </div>

            @endif


            <form
                method="POST"
                action="{{ route('login.attempt') }}"
            >

                @csrf

                <!-- EMAIL -->

                <div class="input-group">

                    <label class="input-label">
                        Email
                    </label>

                    <div class="input-wrapper">

                        <div class="input-icon">

                            <svg
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"
                                />

                                <rect
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"
                                />

                            </svg>

                        </div>


                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', 'qa@wbn.com') }}"
                            required
                            autofocus
                            placeholder="nama@email.com"
                            class="input-field"
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="input-group">

                    <label class="input-label">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <div class="input-icon">

                            <svg
                                width="18"
                                height="18"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                            >

                                <rect
                                    x="4"
                                    y="10"
                                    width="16"
                                    height="10"
                                    rx="2"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 10V7a4 4 0 018 0v3"
                                />

                            </svg>

                        </div>


                        <input
                            type="password"
                            name="password"
                            value="password"
                            required
                            placeholder="••••••••"
                            class="input-field"
                        >

                    </div>

                </div>


                <!-- REMEMBER -->

                <div class="login-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        <span>
                            Ingat saya
                        </span>

                    </label>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="btn-login"
                >
                    Masuk ke Sistem
                </button>

            </form>

        </div>


        <!-- FOOTER -->

        <div class="login-footer">

            © {{ date('Y') }}
            PT Wahana Bermuda Nusantara
            · Sistem Monitoring Complaint

        </div>


    </div>

</div>

</body>

</html>