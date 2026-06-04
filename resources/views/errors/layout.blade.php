<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'GardenNGrow') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary-400: #52b788;
            --primary-500: #2d9c6a;
            --primary-600: #2d6a4f;
            --primary-700: #1e4f3c;
            --primary-800: #163829;
            --bg: #f8faf5;
            --text: #1b1b1b;
            --text-muted: #64748b;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated leaf background */
        .leaves {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .leaf {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.12;
            animation: fall linear infinite;
        }
        @keyframes fall {
            0% { transform: translateY(-10vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.12; }
            90% { opacity: 0.12; }
            100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }

        /* Container */
        .error-container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
            max-width: 560px;
            width: 100%;
        }

        /* Glowing error code */
        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: clamp(6rem, 18vw, 10rem);
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(135deg, var(--primary-400), var(--primary-700));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            margin-bottom: 0.25rem;
            animation: pulse-glow 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { filter: drop-shadow(0 0 8px rgba(82, 183, 136, 0.3)); }
            50% { filter: drop-shadow(0 0 20px rgba(82, 183, 136, 0.5)); }
        }

        /* Decorative line */
        .divider {
            width: 64px;
            height: 3px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--primary-400), var(--primary-600));
            margin: 1.25rem auto;
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.75rem;
        }

        .error-message {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            color: #fff;
            box-shadow: 0 4px 16px rgba(45, 106, 79, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(45, 106, 79, 0.4);
        }

        .btn-secondary {
            background: var(--card-bg);
            color: var(--primary-600);
            border: 1.5px solid rgba(45, 106, 79, 0.25);
        }
        .btn-secondary:hover {
            background: rgba(45, 106, 79, 0.06);
            border-color: var(--primary-500);
            transform: translateY(-2px);
        }

        /* Icon inside error card */
        .error-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
            background: linear-gradient(135deg, rgba(82, 183, 136, 0.12), rgba(45, 106, 79, 0.08));
        }

        /* Footer branding */
        .error-footer {
            margin-top: 3rem;
            color: var(--text-muted);
            font-size: 0.8rem;
            opacity: 0.6;
        }
        .error-footer a {
            color: var(--primary-500);
            text-decoration: none;
        }
        .error-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    {{-- Floating leaves background --}}
    <div class="leaves" aria-hidden="true">
        <script>
            (function() {
                var container = document.querySelector('.leaves');
                var emojis = ['🍃', '🌿', '☘️', '🌱', '🍀'];
                for (var i = 0; i < 18; i++) {
                    var span = document.createElement('span');
                    span.className = 'leaf';
                    span.textContent = emojis[i % emojis.length];
                    span.style.left = (Math.random() * 100) + '%';
                    span.style.fontSize = (1 + Math.random() * 1.2) + 'rem';
                    span.style.animationDuration = (8 + Math.random() * 12) + 's';
                    span.style.animationDelay = (Math.random() * 10) + 's';
                    container.appendChild(span);
                }
            })();
        </script>
    </div>

    <div class="error-container">
        @yield('body')

        <div class="error-footer">
            <a href="{{ url('/') }}">{{ config('app.name', 'GardenNGrow') }}</a>
        </div>
    </div>
</body>
</html>
