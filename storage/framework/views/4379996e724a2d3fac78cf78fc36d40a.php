<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MadeCars - Tu Concesionario de Confianza</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/logos/favicon.png')); ?>">

        <!-- Icono para App / Versión Móvil -->
    <meta name="apple-mobile-web-app-title" content="MadeCars">
    <meta name="apple-touch-icon" href="<?php echo e(asset('img/logos/logoMovil-128x128.png')); ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Configuración de Tailwind -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'brand-red': '#DC2626',
                        'brand-black': '#000000',
                        'brand-white': '#FFFFFF',
                        'whatsapp': '#25D366',
                    }
                }
            }
        }
    </script>

    <style>
    body {
        font-family: 'Poppins', sans-serif;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    h1, h2, h3, h4, h5, h6, .font-title {
        font-family: 'Montserrat', sans-serif;
    }

    /* Variables CSS globales para cambio de tema */
    :root {
        --bg-principal: #000000;
        --texto-principal: #ffffff;
        --texto-secundario: #9ca3af;
        --bg-tarjeta: #000000;
        --borde-tarjeta: #ffffff;
        --hover-tarjeta: rgba(220, 38, 38, 0.3);
        --texto-titulo: #ffffff;
        --filtro-mapa: invert(90%) hue-rotate(180deg) contrast(90%) saturate(50%);
        --overlay-hero: linear-gradient(to top, rgba(0,0,0,0.5), rgba(0,0,0,0.4));
    }

    html.light-mode {
        --bg-principal: #ffffff;
        --texto-principal: #1f2937;
        --texto-secundario: #4b5563;
        --bg-tarjeta: #ffffff;
        --borde-tarjeta: #e5e7eb;
        --hover-tarjeta: rgba(220, 38, 38, 0.2);
        --texto-titulo: #1f2937;
        --filtro-mapa: none;
        --overlay-hero: linear-gradient(to top, rgba(255,255,255,0.5), rgba(255,255,255,0.4));
    }
</style>
</head>
<body class="transition-colors duration-300" id="body-tema" style="background-color: var(--bg-principal); color: var(--texto-principal);">

    <!-- HEADER -->
    <header class="bg-brand-black border-b-2 border-brand-red sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?php echo e(url('/')); ?>" class="text-white font-montserrat font-bold text-2xl tracking-wider">
                        <img src="<?php echo e(asset('img/logos/logo.png')); ?>" alt="Logo MadeCars" class="h-12 object-contain">
                    </a>
                </div>

                <!-- Menú Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo e(url('/')); ?>" class="text-white hover:text-brand-red font-montserrat font-medium transition-colors">Inicio</a>
                    <a href="<?php echo e(url('/inventario')); ?>" class="text-white hover:text-brand-red font-montserrat font-medium transition-colors">Inventario</a>
                    <a href="<?php echo e(url('/#quienes-somos')); ?>" class="text-white hover:text-brand-red font-montserrat font-medium transition-colors">Quiénes Somos</a>
                    <a href="<?php echo e(url('/#contacto')); ?>" class="text-white hover:text-brand-red font-montserrat font-medium transition-colors">Contacto</a>

                    <!-- Botón de Tema -->
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-brand-red/10 text-brand-red hover:bg-brand-red hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i id="theme-icon" class="fas fa-sun text-xl"></i>
                    </button>
                </div>

                <!-- Menú Mobile -->
                <div class="md:hidden flex items-center space-x-4">
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-brand-red/10 text-brand-red hover:bg-brand-red hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i id="theme-icon-movil" class="fas fa-sun text-xl"></i>
                    </button>
                    <button onclick="toggleMobileMenu()" class="text-white">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Menú Mobile Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden bg-brand-black border-t border-brand-red">
            <div class="px-4 py-4 space-y-3">
                <a href="<?php echo e(url('/')); ?>" class="block text-white hover:text-brand-red font-montserrat">Inicio</a>
                <a href="<?php echo e(url('/inventario')); ?>" class="block text-white hover:text-brand-red font-montserrat">Inventario</a>
                <a href="<?php echo e(url('/#quienes-somos')); ?>" class="block text-white hover:text-brand-red font-montserrat">Quiénes Somos</a>
                <a href="<?php echo e(url('/#contacto')); ?>" class="block text-white hover:text-brand-red font-montserrat">Contacto</a>
            </div>
        </div>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-grow">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- FOOTER -->
    <footer class="bg-brand-black border-t-2 border-brand-red py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="text-white font-montserrat font-bold text-2xl tracking-wider mb-4">
                <div class="text-white font-montserrat font-bold text-2xl tracking-wider mb-4">
                    <img src="<?php echo e(asset('img/logos/logo.png')); ?>" alt="Logo MadeCars" class="h-10 mx-auto object-contain">
                </div>
            </div>
            <p class="text-gray-400 text-sm">© 2026 MadeCars. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- BOTÓN WHATSAPP FLOTANTE -->
    <a href="https://wa.me/18294649902?text=Hola%20MadeCars,%20Me%20interesa%20m%C3%A1s%20informacion%20sobre" target="_blank" class="fixed bottom-6 right-6 bg-whatsapp text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform z-50">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>

    <!-- SCRIPT GLOBAL DE TEMA -->
    <script>
        // Función global para cambiar tema
        function toggleTheme() {
            const html = document.documentElement;
            const body = document.getElementById('body-tema');
            const icon = document.getElementById('theme-icon');
            const iconMovil = document.getElementById('theme-icon-movil');
            const mapa = document.getElementById('mapa-tema');
            const heroTitle = document.getElementById('hero-title');
            const heroSubtitle = document.getElementById('hero-subtitle');

            // Toggle light-mode
            html.classList.toggle('light-mode');
            if (body) {
                body.classList.toggle('light-mode');
            }

            const isLight = html.classList.contains('light-mode');

            // Actualizar icono
            if (icon) {
                icon.className = isLight ? 'fas fa-moon text-xl' : 'fas fa-sun text-xl';
            }
            if (iconMovil) {
                iconMovil.className = isLight ? 'fas fa-moon text-xl' : 'fas fa-sun text-xl';
            }

            // Actualizar mapa si existe
            if (mapa) {
                mapa.style.filter = isLight ? 'none' : 'invert(90%) hue-rotate(180deg) contrast(90%) saturate(50%)';
            }

            // Actualizar hero si existe
            if (heroTitle) {
                heroTitle.style.color = isLight ? '#1f2937' : '#ffffff';
            }
            if (heroSubtitle) {
                heroSubtitle.style.color = isLight ? '#4b5563' : '#9ca3af';
            }

            // Guardar preferencia
            localStorage.setItem('theme', isLight ? 'light' : 'dark');

            // Forzar reflow para asegurar que los cambios se apliquen
            void document.documentElement.offsetWidth;
        }

        // Menú mobile
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Cargar tema guardado al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            const temaGuardado = localStorage.getItem('theme');
            const html = document.documentElement;
            const body = document.getElementById('body-tema');
            const icon = document.getElementById('theme-icon');
            const iconMovil = document.getElementById('theme-icon-movil');
            const mapa = document.getElementById('mapa-tema');
            const heroTitle = document.getElementById('hero-title');
            const heroSubtitle = document.getElementById('hero-subtitle');

            if (temaGuardado === 'light') {
                html.classList.add('light-mode');
                if (body) {
                    body.classList.add('light-mode');
                }

                // Actualizar icono
                if (icon) {
                    icon.className = 'fas fa-moon text-xl';
                }
                if (iconMovil) {
                    iconMovil.className = 'fas fa-moon text-xl';
                }

                // Actualizar mapa
                if (mapa) {
                    mapa.style.filter = 'none';
                }

                // Actualizar hero
                if (heroTitle) {
                    heroTitle.style.color = '#1f2937';
                }
                if (heroSubtitle) {
                    heroSubtitle.style.color = '#4b5563';
                }
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Madecars\resources\views/layouts/app.blade.php ENDPATH**/ ?>