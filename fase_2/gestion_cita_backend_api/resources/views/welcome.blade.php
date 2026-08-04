<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Gestion de Citas') }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f8fb;
            --surface: #ffffff;
            --surface-soft: #eef6f4;
            --text: #1f2933;
            --muted: #65758b;
            --line: #d8e1e8;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --accent: #2563eb;
            --ok: #15803d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
        }

        .topbar {
            border-bottom: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
        }

        .topbar-inner,
        .content {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar-inner {
            min-height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-weight: 800;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 0 12px;
            border: 1px solid #bbdfc8;
            border-radius: 999px;
            background: #eefbf2;
            color: var(--ok);
            font-size: 14px;
            font-weight: 650;
            white-space: nowrap;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--ok);
        }

        .content {
            padding: 54px 0 44px;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(300px, 0.88fr);
            gap: 28px;
            align-items: stretch;
        }

        .panel,
        .metric,
        .module {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
        }

        .intro {
            padding: 36px;
        }

        .eyebrow {
            margin: 0 0 14px;
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: 0;
        }

        h1 {
            margin: 0;
            max-width: 760px;
            font-size: clamp(36px, 6vw, 68px);
            line-height: 0.98;
            letter-spacing: 0;
        }

        .lead {
            max-width: 690px;
            margin: 22px 0 0;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
        }

        .button.primary {
            background: var(--primary);
            color: white;
        }

        .button.secondary {
            border: 1px solid var(--line);
            color: var(--text);
            background: white;
        }

        .summary {
            display: grid;
            gap: 14px;
            padding: 20px;
            background: var(--surface-soft);
        }

        .metric {
            padding: 18px;
        }

        .metric-label {
            margin: 0 0 6px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 650;
        }

        .metric-value {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }

        .section-title {
            margin: 38px 0 16px;
            font-size: 22px;
        }

        .modules {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .module {
            padding: 20px;
        }

        .module h2 {
            margin: 0 0 8px;
            font-size: 17px;
        }

        .module p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .footer {
            margin-top: 34px;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 820px) {
            .hero,
            .modules {
                grid-template-columns: 1fr;
            }

            .intro {
                padding: 26px;
            }

            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
                justify-content: center;
                padding: 14px 0;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="topbar-inner">
                <div class="brand" aria-label="Gestion de Citas">
                    <span class="brand-mark">GC</span>
                    <span>Gestion de Citas</span>
                </div>
                <div class="status">
                    <span class="status-dot"></span>
                    API fase 2 disponible
                </div>
            </div>
        </header>

        <main class="content">
            <section class="hero">
                <div class="panel intro">
                    <p class="eyebrow">Sistema medico administrativo</p>
                    <h1>Agenda clinica y reservas en un solo backend</h1>
                    <p class="lead">
                        Servicio Laravel para administrar pacientes, medicos, categorias, reservas,
                        estados de atencion, tipos de pago y usuarios internos.
                    </p>
                    <div class="actions">
                        <a class="button primary" href="/up">Ver estado</a>
                        <a class="button secondary" href="{{ url('/') }}">Inicio</a>
                    </div>
                </div>

                <aside class="panel summary" aria-label="Resumen tecnico">
                    <div class="metric">
                        <p class="metric-label">Entorno</p>
                        <p class="metric-value">{{ app()->environment() }}</p>
                    </div>
                    <div class="metric">
                        <p class="metric-label">Framework</p>
                        <p class="metric-value">Laravel {{ app()->version() }}</p>
                    </div>
                    <div class="metric">
                        <p class="metric-label">Logs</p>
                        <p class="metric-value">Rotacion diaria</p>
                    </div>
                </aside>
            </section>

            <h2 class="section-title">Modulos preparados</h2>
            <section class="modules" aria-label="Modulos de la aplicacion">
                <article class="module">
                    <h2>Pacientes</h2>
                    <p>Registro de datos personales, contacto, informacion clinica basica y preferencias.</p>
                </article>
                <article class="module">
                    <h2>Medicos</h2>
                    <p>Gestion de profesionales, especialidades por categoria y disponibilidad operativa.</p>
                </article>
                <article class="module">
                    <h2>Reservas</h2>
                    <p>Agenda de citas con paciente, medico, pago, estado, sintomas y trazabilidad.</p>
                </article>
            </section>

            <p class="footer">
                {{ config('app.name', 'Gestion de Citas') }} · Fase 2 · {{ now()->format('Y-m-d') }}
            </p>
        </main>
    </div>
</body>
</html>
