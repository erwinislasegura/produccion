<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistemas Internos - SmartBerry ONE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" href="assest/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        :root {
            --azul: #001b5d;
            --rojo: #d81046;
            --verde: #238b3b;
            --texto: #1f2a44;
            --texto-suave: #6f7b90;
            --borde: #e3e9f0;
            --fondo: #f6f8fb;
            --blanco: #ffffff;
            --azul-suave: #f3f6fb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            font-family: 'Montserrat', Arial, sans-serif;
            background: var(--fondo);
            color: var(--texto);
        }

        body {
            overflow-x: hidden;
        }

        .container {
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
            padding: 34px;
            position: relative;
            z-index: 2;
        }

        .panel-content {
            width: 100%;
            max-width: 360px;
        }

        .logo-header {
            margin-bottom: 26px;
        }

        .logo-header img {
            width: 100%;
            max-width: 290px;
            height: auto;
            display: block;
            margin-bottom: 18px;
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

        .page-title {
            color: var(--azul);
            font-size: 1.22rem;
            line-height: 1.35;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            margin-top: 9px;
            color: var(--texto-suave);
            font-size: 0.8rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .web-address {
            margin-top: 13px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--azul);
            font-size: 0.76rem;
            font-weight: 500;
            text-decoration: none;
        }

        .web-address .material-icons-round {
            font-size: 16px;
            color: var(--rojo);
        }

        .systems-card {
            background: var(--blanco);
            border: 1px solid var(--borde);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 14px 34px rgba(0, 27, 93, 0.07);
        }

        .card-title {
            margin-bottom: 14px;
            color: var(--azul);
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .button-grid {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .system-btn {
            width: 100%;
            min-height: 62px;
            display: grid;
            grid-template-columns: 36px 1fr 20px;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border: 1px solid var(--borde);
            border-radius: 12px;
            background: var(--blanco);
            color: var(--azul);
            text-decoration: none;
            transition: all 0.18s ease;
        }

        .system-btn:hover {
            border-color: #bfd7c6;
            background: #fafdfb;
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 27, 93, 0.06);
        }

        .icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--azul-suave);
            color: var(--azul);
            transition: all 0.18s ease;
        }

        .icon-box .material-icons-round {
            font-size: 20px;
        }

        .system-btn:hover .icon-box {
            background: #eef8f1;
            color: var(--verde);
        }

        .btn-content {
            display: flex;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .btn-title {
            color: var(--azul);
            font-size: 0.84rem;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .btn-desc {
            color: var(--texto-suave);
            font-size: 0.7rem;
            line-height: 1.35;
            font-weight: 400;
        }

        .arrow-icon {
            color: #9aa6b8;
            font-size: 19px;
            transition: all 0.18s ease;
        }

        .system-btn:hover .arrow-icon {
            color: var(--rojo);
            transform: translateX(2px);
        }

        .module-summary {
            margin-top: 15px;
            padding-top: 14px;
            border-top: 1px solid var(--borde);
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .summary-item {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: var(--texto-suave);
            font-size: 0.62rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: center;
        }

        .summary-item .material-icons-round {
            font-size: 15px;
            color: var(--verde);
        }

        .footer-text {
            margin-top: 18px;
            text-align: center;
            color: #8b96a8;
            font-size: 0.67rem;
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
            color: var(--azul);
            font-size: 1.06rem;
            line-height: 1.38;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .right-info p {
            margin-top: 9px;
            color: #5d697c;
            font-size: 0.78rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .right-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
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
            font-size: 0.67rem;
            font-weight: 500;
        }

        .right-tag .material-icons-round {
            font-size: 15px;
            color: var(--verde);
        }

        @media (max-width: 980px) {
            .container {
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

            .panel-content {
                max-width: 420px;
            }
        }

        @media (max-width: 560px) {
            .right-panel {
                min-height: 245px;
            }

            .right-info {
                display: none;
            }

            .logo-header img {
                max-width: 260px;
            }

            .page-title {
                font-size: 1.12rem;
            }

            .page-subtitle {
                font-size: 0.76rem;
            }

            .systems-card {
                padding: 16px;
            }

            .system-btn {
                grid-template-columns: 35px 1fr 18px;
                min-height: 60px;
            }

            .btn-title {
                font-size: 0.8rem;
            }

            .btn-desc {
                font-size: 0.68rem;
            }

            .module-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .summary-item {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

    <main class="container">

        <section class="left-panel">
            <div class="panel-content">

                <div class="logo-header">
                    <img src="assest/img/logo2.png" alt="SmartBerry ONE">

                    <div class="system-label">
                        <span class="material-icons-round">admin_panel_settings</span>
                        Sistemas internos
                    </div>

                    <h1 class="page-title">
                        Módulos internos SmartBerry ONE
                    </h1>

                    <p class="page-subtitle">
                        Selecciona el módulo correspondiente para continuar con la gestión interna.
                    </p>

                    <a class="web-address" href="https://smartberryone.cl" target="_blank">
                        <span class="material-icons-round">language</span>
                        smartberryone.cl
                    </a>
                </div>

                <div class="systems-card">
                    <h2 class="card-title">Seleccionar módulo</h2>

                    <div class="button-grid">

                        <a href="fruta/vista/iniciarSession.php" class="system-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">local_florist</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Fruta</span>
                                <span class="btn-desc">Gestión y control de fruta.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                        <a href="material/vista/iniciarSession.php" class="system-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">inventory_2</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Material</span>
                                <span class="btn-desc">Control y administración de materiales.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                        <a href="exportadora/vista/iniciarSession.php" class="system-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">flight_takeoff</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Exportadora</span>
                                <span class="btn-desc">Gestión del área exportadora.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                        <a href="estadistica/vista/iniciarSession.php" class="system-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">bar_chart</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Estadísticas</span>
                                <span class="btn-desc">Indicadores, información y reportes.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                        <a href="calidad/vista/iniciarSession.php" class="system-btn">
                            <span class="icon-box">
                                <span class="material-icons-round">verified</span>
                            </span>

                            <span class="btn-content">
                                <span class="btn-title">Calidad</span>
                                <span class="btn-desc">Gestión y control de calidad.</span>
                            </span>

                            <span class="material-icons-round arrow-icon">arrow_forward</span>
                        </a>

                    </div>

                    <div class="module-summary">
                        <div class="summary-item">
                            <span class="material-icons-round">eco</span>
                            Campo
                        </div>

                        <div class="summary-item">
                            <span class="material-icons-round">qr_code_scanner</span>
                            Trazabilidad
                        </div>

                        <div class="summary-item">
                            <span class="material-icons-round">shield</span>
                            Control
                        </div>
                    </div>
                </div>

                <div class="footer-text">
                    SmartBerry ONE · Acceso exclusivo para usuarios autorizados.
                </div>

            </div>
        </section>

        <section class="right-panel">
            <div class="slider-bg">
                <div class="slide active" style="background-image:url('http://190.13.179.171:18069/smartberries%20-23-24/assest/img/abeja.jpg')"></div>
                <div class="slide" style="background-image:url('http://190.13.179.171:18069/smartberries%20-23-24/assest/img/arandano.jpg')"></div>
                <div class="slide" style="background-image:url('http://190.13.179.171:18069/smartberries%20-23-24/assest/img/esparragos.jpg')"></div>
            </div>

            <div class="image-cover"></div>

            <div class="right-info">
                <h2>Gestión interna simple, ordenada y segura</h2>

                <p>
                    Acceso centralizado a los módulos operacionales de SmartBerry ONE,
                    diseñado para mantener control, trazabilidad y continuidad de procesos.
                </p>

                <div class="right-tags">
                    <div class="right-tag">
                        <span class="material-icons-round">local_florist</span>
                        Fruta
                    </div>

                    <div class="right-tag">
                        <span class="material-icons-round">inventory_2</span>
                        Material
                    </div>

                    <div class="right-tag">
                        <span class="material-icons-round">verified</span>
                        Calidad
                    </div>

                    <div class="right-tag">
                        <span class="material-icons-round">bar_chart</span>
                        Reportes
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;

        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000);
    </script>

</body>
</html>