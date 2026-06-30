<!DOCTYPE html>
<html lang="es">
<head>
    <title>SmartBerry ONE - Acceso</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" href="assest/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        :root {
            --azul: #001b5d;
            --azul-suave: #f3f6fb;
            --rojo: #d81046;
            --verde: #238b3b;
            --texto: #1f2a44;
            --texto-suave: #6f7b90;
            --borde: #e3e9f0;
            --fondo: #f6f8fb;
            --blanco: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            width: 100%;
            min-height: 100%;
            font-family: 'Montserrat', Arial, sans-serif;
            background: var(--fondo);
            color: var(--texto);
        }

        body {
            overflow-x: hidden;
        }

        .main-container {
            width: 100%;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 440px 1fr;
            background: var(--fondo);
        }

        .left-panel {
            background: var(--blanco);
            border-right: 1px solid var(--borde);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 38px;
            position: relative;
            z-index: 2;
        }

        .login-box {
            width: 100%;
            max-width: 360px;
        }

        .logo-header {
            margin-bottom: 28px;
        }

        .logo-header img {
            width: 100%;
            max-width: 295px;
            height: auto;
            display: block;
            margin-bottom: 20px;
        }

        .system-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--verde);
            background: #f4faf6;
            border: 1px solid #dcefe2;
            border-radius: 999px;
            padding: 7px 11px;
            margin-bottom: 14px;
        }

        .system-label .material-icons-round {
            font-size: 15px;
        }

        .login-title {
            margin: 0;
            color: var(--azul);
            font-size: 1.26rem;
            line-height: 1.35;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            margin: 10px 0 0;
            color: var(--texto-suave);
            font-size: 0.82rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .web-address {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--azul);
            font-size: 0.78rem;
            font-weight: 500;
            text-decoration: none;
        }

        .web-address .material-icons-round {
            font-size: 16px;
            color: var(--rojo);
        }

        .access-card {
            background: var(--blanco);
            border: 1px solid var(--borde);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 14px 34px rgba(0, 27, 93, 0.07);
        }

        .card-title {
            margin: 0 0 14px;
            color: var(--azul);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .button-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .access-btn {
            width: 100%;
            min-height: 68px;
            display: grid;
            grid-template-columns: 38px 1fr 20px;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--borde);
            border-radius: 12px;
            background: var(--blanco);
            color: var(--azul);
            text-decoration: none;
            transition: all 0.18s ease;
        }

        .access-btn:hover {
            border-color: #bfd7c6;
            background: #fafdfb;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 27, 93, 0.06);
        }

        .icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--azul-suave);
            color: var(--azul);
            transition: all 0.18s ease;
        }

        .access-btn:hover .icon-box {
            background: #eef8f1;
            color: var(--verde);
        }

        .icon-box .material-icons-round {
            font-size: 21px;
        }

        .btn-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .btn-title {
            color: var(--azul);
            font-size: 0.86rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .btn-desc {
            color: var(--texto-suave);
            font-size: 0.72rem;
            line-height: 1.35;
            font-weight: 400;
        }

        .arrow-icon {
            color: #9aa6b8;
            font-size: 20px;
            transition: all 0.18s ease;
        }

        .access-btn:hover .arrow-icon {
            color: var(--rojo);
            transform: translateX(2px);
        }

        .values-row {
            margin-top: 16px;
            padding-top: 15px;
            border-top: 1px solid var(--borde);
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .value-item {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: var(--texto-suave);
            font-size: 0.63rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: center;
        }

        .value-item .material-icons-round {
            font-size: 15px;
            color: var(--verde);
        }

        .footer-text {
            margin-top: 18px;
            text-align: center;
            color: #8b96a8;
            font-size: 0.68rem;
            line-height: 1.5;
            font-weight: 400;
        }

        .right-panel {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            background: #0b1f45;
        }

        .slider-bg {
            position: absolute;
            inset: 0;
        }

        .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease;
        }

        .slide.active {
            opacity: 1;
        }

        .image-cover {
            position: absolute;
            inset: 0;
            background: rgba(0, 27, 93, 0.34);
            z-index: 1;
        }

        .right-info {
            position: absolute;
            left: 42px;
            bottom: 42px;
            z-index: 2;
            width: calc(100% - 84px);
            max-width: 520px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.65);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.14);
        }

        .right-info h2 {
            margin: 0;
            color: var(--azul);
            font-size: 1.12rem;
            line-height: 1.35;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .right-info p {
            margin: 9px 0 0;
            color: #5d697c;
            font-size: 0.8rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .right-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
        }

        .right-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 999px;
            background: #f7f9fc;
            border: 1px solid #e5ebf2;
            color: var(--azul);
            font-size: 0.68rem;
            font-weight: 500;
        }

        .right-tag .material-icons-round {
            font-size: 15px;
            color: var(--verde);
        }

        @media (max-width: 980px) {
            .main-container {
                grid-template-columns: 1fr;
            }

            .left-panel {
                order: 2;
                padding: 32px 22px 38px;
                border-right: none;
            }

            .right-panel {
                order: 1;
                min-height: 300px;
            }

            .right-info {
                left: 22px;
                bottom: 22px;
                width: calc(100% - 44px);
                max-width: none;
                padding: 18px;
            }

            .login-box {
                max-width: 420px;
            }
        }

        @media (max-width: 560px) {
            .right-panel {
                min-height: 250px;
            }

            .right-info {
                display: none;
            }

            .logo-header img {
                max-width: 260px;
            }

            .login-title {
                font-size: 1.14rem;
            }

            .login-subtitle {
                font-size: 0.78rem;
            }

            .access-card {
                padding: 16px;
            }

            .access-btn {
                grid-template-columns: 36px 1fr 18px;
                min-height: 66px;
            }

            .values-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .value-item {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

    <main class="main-container">

        <section class="left-panel">
            <div class="login-box">

                <div class="logo-header">
                    <img src="assest/img/logo2.png" alt="SmartBerry ONE">

                    <div class="system-label">
                        <span class="material-icons-round">verified_user</span>
                        Plataforma privada
                    </div>

                    <h1 class="login-title">
                        Acceso a sistemas SmartBerry ONE
                    </h1>

                    <p class="login-subtitle">
                        Selecciona el portal correspondiente para ingresar a la plataforma.
                    </p>

                    <a class="web-address" href="https://www.volcanfoods.cl" target="_blank">
                        <span class="material-icons-round">language</span>
                        www.smartberryone.cl
                    </a>
                </div>

                <div class="access-card">
                    <h2 class="card-title">Seleccionar acceso</h2>

                    <div class="button-grid">

                        <a href="./interno.php" class="access-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">account_circle</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Acceso Interno</span>
                                <span class="btn-desc">Usuarios administrativos y equipo interno.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                        <a href="./productor/" class="access-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">agriculture</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Portal Productores</span>
                                <span class="btn-desc">Acceso para productores registrados.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                    </div>

                    <div class="values-row">
                        <div class="value-item">
                            <span class="material-icons-round">eco</span>
                            Trazabilidad
                        </div>

                        <div class="value-item">
                            <span class="material-icons-round">inventory_2</span>
                            Transparencia
                        </div>

                        <div class="value-item">
                            <span class="material-icons-round">shield</span>
                            Confianza
                        </div>
                    </div>
                </div>

                <div class="footer-text">
                    SmartBerry ONE · Del campo al mundo<br>
                    Acceso exclusivo para usuarios autorizados.
                </div>

            </div>
        </section>

        <section class="right-panel">
            <div class="slider-bg">
                <div class="slide active" style="background-image: url('assest/img/abeja.jpg');"></div>
                <div class="slide" style="background-image: url('assest/img/arandano.jpg');"></div>
                <div class="slide" style="background-image: url('assest/img/esparragos.jpg');"></div>
            </div>

            <div class="image-cover"></div>

            <div class="right-info">
                <h2>Gestión productiva, trazabilidad y control operacional</h2>

                <p>
                    Plataforma diseñada para conectar el trabajo de campo,
                    productores y procesos internos en un entorno seguro.
                </p>

                <div class="right-tags">
                    <div class="right-tag">
                        <span class="material-icons-round">eco</span>
                        Campo
                    </div>

                    <div class="right-tag">
                        <span class="material-icons-round">qr_code_scanner</span>
                        Trazabilidad
                    </div>

                    <div class="right-tag">
                        <span class="material-icons-round">public</span>
                        Exportación
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        const slides = document.querySelectorAll('.slider-bg .slide');
        let current = 0;

        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 5000);
    </script>

</body>
</html>