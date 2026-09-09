<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MadeCars - Tu Concesionario de Confianza</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logos/favicon.png">

    <!-- Icono para App / Versión Móvil -->
    <meta name="apple-mobile-web-app-title" content="MadeCars">
    <meta name="apple-touch-icon" content="img/logos/logoMovil-128x128.png">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Variables de color para ambos temas */
        :root {
            --bg-principal: #000000;
            --bg-header: #000000;
            --bg-footer: #000000;
            --texto-principal: #ffffff;
            --texto-secundario: #9ca3af;
            --bg-tarjeta: #000000;
            --borde-tarjeta: #ffffff;
            --hover-tarjeta: rgba(220, 38, 38, 0.3);
            --texto-titulo: #ffffff;
            
            /* Botón Inventario */
            --btn-inventario-bg: #DC2626;
            --btn-inventario-texto: #ffffff;
            
            /* Mapa */
            --filtro-mapa: invert(90%) hue-rotate(180deg) contrast(90%) saturate(50%);
        }

        body.light-mode {
            --bg-principal: #ffffff;
            --bg-header: #000000;
            --bg-footer: #000000;
            --texto-principal: #1f2937;
            --texto-secundario: #4b5563;
            --bg-tarjeta: #ffffff;
            --borde-tarjeta: #e5e7eb;
            --hover-tarjeta: rgba(220, 38, 38, 0.2);
            --texto-titulo: #1f2937;
            
            /* Botón Inventario (Tema claro) */
            --btn-inventario-bg: #000000;
            --btn-inventario-texto: #ffffff;
            
            /* Mapa (Tema claro) */
            --filtro-mapa: none;
        }

        /* Estilos generales */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-principal);
            color: var(--texto-principal);
            overflow-x: hidden;
        }
        
        h1, h2, h3, .font-logo {
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .text-rojo { color: #DC2626; }
        .bg-rojo { background-color: #DC2626; }
        .border-rojo { border-color: #DC2626; }

        /* Transiciones para tarjetas */
        .card-hover {
            background-color: var(--bg-tarjeta);
            border: 1px solid var(--borde-tarjeta);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--hover-tarjeta);
            border-color: #DC2626;
        }

        .animar-entrada {
            animation: aparecer 2s ease-out;
        }
        @keyframes aparecer {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .slide.activo {
            opacity: 1;
            filter: saturate(130%) contrast(105%);
        }
        .overlay-hero {
            background: linear-gradient(to top, rgba(0,0,0,0.5), rgba(0,0,0,0.4));
        }

        body.light-mode .overlay-hero {
            background: linear-gradient(to top, rgba(255,255,255,0.5), rgba(255,255,255,0.4));
        }

        /* Botón Inventario (cambia según tema) */
        .btn-inventario {
            background-color: var(--btn-inventario-bg);
            color: var(--btn-inventario-texto);
            transition: all 0.3s ease;
        }
        .btn-inventario:hover {
            opacity: 0.9;
        }

        /* Botón Flotante de WhatsApp */
        .whatsapp-float {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
            width: 65px;
            height: 65px;
            background-color: #25D366;
            color: white;
            border-radius: 50px;
            font-size: 35px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .whatsapp-float:hover { background-color: #128C7E; transform: scale(1.1); }

        /* Estilos del mapa (cambia según tema) */
        .mapa-container {
            width: 100%;
            height: 100%;
            min-height: 350px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--borde-tarjeta);
            background-color: var(--bg-tarjeta);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .mapa-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px var(--hover-tarjeta);
            border-color: #DC2626;
        }

        #menu-movil { display: none; }

        /* Botón de cambio de tema */
        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover {
            transform: scale(1.1);
        }

        /* Header y Footer */
        .header-tema { background-color: var(--bg-header); }
        .footer-tema { background-color: var(--bg-footer); color: var(--texto-secundario); }
        
        /* Letras siempre blancas en header y footer */
        .header-tema a, .footer-tema, .header-tema, .footer-tema p {
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-animado" id="body-tema">

    <!-- Header/Navegación -->
    <nav class="header-tema backdrop-blur-md p-4 sticky top-0 z-50 border-b-2 border-rojo">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/logos/logo.png" alt="Logo MadeCars" class="h-12 object-contain">
            </div>
            <div class="hidden md:flex items-center space-x-6 font-medium">
                <a href="index.php" class="hover:text-rojo transition">Inicio</a>
                <a href="inventory.html" class="hover:text-rojo transition">Inventario</a>
                <a href="#quienes-somos" class="hover:text-rojo transition">Quiénes Somos</a>
                <a href="#contacto" class="hover:text-rojo transition">Contacto</a>
                
                <!-- Botón de cambio de tema (desktop) -->
                <button onclick="toggleTheme()" class="theme-toggle bg-rojo/10 text-rojo hover:bg-rojo hover:text-white">
                    <i id="icono-tema" class="fas fa-sun"></i>
                </button>
            </div>
            <div class="md:hidden flex items-center space-x-4">
                <button onclick="toggleTheme()" class="theme-toggle bg-rojo/10 text-rojo hover:bg-rojo hover:text-white">
                    <i id="icono-tema-movil" class="fas fa-sun"></i>
                </button>
                <button onclick="toggleMenu()" class="text-rojo text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Menú móvil desplegable -->
        <div id="menu-movil" class="md:hidden mt-2 rounded-lg p-4" style="background-color: var(--bg-header);">
            <a href="index.php" class="block py-2 text-white hover:text-rojo transition">Inicio</a>
            <a href="inventory.html" class="block py-2 text-white hover:text-rojo transition">Inventario</a>
            <a href="#quienes-somos" class="block py-2 text-white hover:text-rojo transition">Quiénes Somos</a>
            <a href="#contacto" class="block py-2 text-white hover:text-rojo transition">Contacto</a>
        </div>
    </nav>

    <!-- Hero Section con Carrusel de Imágenes -->
    <section class="relative h-[700px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0" id="slider-hero">
            <div class="slide activo" style="background-image: url('img/carrusel/auto1.jpg');"></div>
            <div class="slide" style="background-image: url('img/carrusel/auto2.jpg');"></div>
            <div class="slide" style="background-image: url('img/carrusel/auto3.jpg');"></div>
            <div class="overlay-hero absolute inset-0"></div>
        </div>
        <div class="relative text-center px-4 animar-entrada">
            <h1 class="text-5xl md:text-7xl font-bold mb-4 drop-shadow-lg" style="color: var(--texto-titulo);">Tu próximo auto está aquí</h1>
            <p class="text-xl md:text-2xl mb-8 font-light" style="color: var(--texto-secundario);">Encuentra el vehículo perfecto entre más de 500 opciones</p>
            <a href="inventory.html" class="btn-inventario px-10 py-4 rounded-lg text-lg font-bold uppercase tracking-wider transition shadow-lg">Explorar Inventario</a>
        </div>
    </section>

    <!-- Quiénes Somos -->
    <section id="quienes-somos" class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12" style="color: var(--texto-titulo);">¿Quiénes Somos?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover p-8 rounded-lg shadow-lg">
                    <i class="fas fa-history text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2" style="color: var(--texto-titulo);">Nuestra Historia</h3>
                    <p style="color: var(--texto-secundario);">Fundada en 2010, MadeCars nació con la visión de revolucionar la compra de vehículos en Latinoamérica, ofreciendo transparencia y confianza.</p>
                </div>
                <div class="card-hover p-8 rounded-lg shadow-lg">
                    <i class="fas fa-handshake text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2" style="color: var(--texto-titulo);">Nuestro Compromiso</h3>
                    <p style="color: var(--texto-secundario);">Calidad garantizada, precios justos y asesoría personalizada en cada paso de tu compra.</p>
                </div>
                <div class="card-hover p-8 rounded-lg shadow-lg">
                    <i class="fas fa-tools text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2" style="color: var(--texto-titulo);">Nuestros Servicios</h3>
                    <p style="color: var(--texto-secundario);">Venta de autos nuevos y usados, financiamiento flexible, cotización personalizada y posventa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Misión y Visión -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="card-hover p-10 rounded-lg shadow-lg">
                    <i class="fas fa-bullseye text-4xl text-rojo mb-4"></i>
                    <h3 class="text-2xl font-bold mb-3" style="color: var(--texto-titulo);">Misión</h3>
                    <p style="color: var(--texto-secundario);">Democratizar el acceso a vehículos de calidad, ofreciendo soluciones financieras accesibles y un servicio excepcional que supere las expectativas de nuestros clientes.</p>
                </div>
                <div class="card-hover p-10 rounded-lg shadow-lg">
                    <i class="fas fa-eye text-4xl text-rojo mb-4"></i>
                    <h3 class="text-2xl font-bold mb-3" style="color: var(--texto-titulo);">Visión</h3>
                    <p style="color: var(--texto-secundario);">Ser el concesionario líder en Latinoamérica, reconocido por nuestra innovación, transparencia y compromiso con la satisfacción del cliente.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-20">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12" style="color: var(--texto-titulo);">Contáctanos</h2>

            <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <div class="card-hover p-8 rounded-2xl">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-xl text-rojo"></i>
                            </div>
                            <span style="color: var(--texto-secundario);">+1 (809) 469-3746</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-xl text-rojo"></i>
                            </div>
                            <span style="color: var(--texto-secundario);">info@madecars.com - Ventas@madecars.com</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-location-dot text-xl text-rojo"></i>
                            </div>
                            <span style="color: var(--texto-secundario);">Agustin Guerrero, Higüey 23000, República Dominicana</span>
                        </div>
                    </div>

                    <!-- Redes Sociales -->
                    <div class="mt-8 pt-8 border-t" style="border-color: var(--borde-tarjeta);">
                        <h4 class="text-lg font-bold mb-4 text-center" style="color: var(--texto-titulo);">Síguenos en</h4>
                        <div class="flex justify-center space-x-6 text-3xl">
                            <a href="https://www.facebook.com/share/1Bn6YTNpck/?mibextid=wwXIfr" target="_blank" class="text-[#1877F2] hover:text-blue-400 transition transform hover:scale-110"><i class="fab fa-facebook"></i></a>
                            <a href="https://www.instagram.com/madecarsrd?igsi=bTF3NnB5bTJiYjFu" target="_blank" class="bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 bg-clip-text text-transparent hover:scale-110 transition transform"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <div class="mapa-container">
                    <iframe
                        id="mapa-tema"
                        src="https://www.google.com/maps?q=Madecarsrd,+Agustin+Guerrero,+Hig%C3%BCey+23000,+Rep%C3%BAblica+Dominicana&output=embed"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-tema py-8 border-t border-rojo">
        <div class="container mx-auto px-4 text-center">
            <img src="img/logos/logo.png" alt="Logo MadeCars" class="h-10 mx-auto mb-4">
            <p>© 2026 MadeCars. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Botón Flotante de WhatsApp -->
    <a href="https://wa.me/18294649902?text=Hola%20MadeCars,%20Me%20interesa%20m%C3%A1s%20informacion%20sobre" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script>
        // Cambiar tema
        function toggleTheme() {
            const body = document.getElementById('body-tema');
            body.classList.toggle('light-mode');
            
            const icono = document.getElementById('icono-tema');
            const iconoMovil = document.getElementById('icono-tema-movil');
            const mapa = document.getElementById('mapa-tema');
            
            // Cambiar icono: Sol (oscuro) / Luna (claro)
            if (body.classList.contains('light-mode')) {
                icono.className = 'fas fa-moon';
                iconoMovil.className = 'fas fa-moon';
                // Mapa en tema claro
                mapa.style.filter = 'none';
                // Guardar preferencia
                localStorage.setItem('theme', 'light');
            } else {
                icono.className = 'fas fa-sun';
                iconoMovil.className = 'fas fa-sun';
                // Mapa en tema oscuro
                mapa.style.filter = 'invert(90%) hue-rotate(180deg) contrast(90%) saturate(50%)';
                // Guardar preferencia
                localStorage.setItem('theme', 'dark');
            }
        }

        // Cargar tema guardado al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            const temaGuardado = localStorage.getItem('theme');
            const body = document.getElementById('body-tema');
            const icono = document.getElementById('icono-tema');
            const iconoMovil = document.getElementById('icono-tema-movil');
            const mapa = document.getElementById('mapa-tema');
            
            if (temaGuardado === 'light') {
                body.classList.add('light-mode');
                icono.className = 'fas fa-moon';
                iconoMovil.className = 'fas fa-moon';
                mapa.style.filter = 'none';
            } else {
                icono.className = 'fas fa-sun';
                iconoMovil.className = 'fas fa-sun';
                mapa.style.filter = 'invert(90%) hue-rotate(180deg) contrast(90%) saturate(50%)';
            }
        });

        // Slider
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('#slider-hero .slide');
            let currentSlide = 0;
            function siguienteSlide() {
                slides[currentSlide].classList.remove('activo');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('activo');
            }
            setInterval(siguienteSlide, 5000);
        });

        // Menú móvil
        function toggleMenu() {
            const menu = document.getElementById('menu-movil');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
    </script>

</body>
</html>