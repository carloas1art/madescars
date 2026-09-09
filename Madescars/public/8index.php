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

    <!-- Fuentes: Nexa Pro (si la tienes) o Poppins/Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos generales (Negro, Rojo y Blanco) */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
        }
        
        /* Fuentes personalizadas */
        h1, h2, h3, .font-logo {
            font-family: 'Poppins', sans-serif; /* Nexa Pro si la tienes */
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Fondo animado sutil */
        .bg-animado {
            background: radial-gradient(circle at 20% 30%, #1a1a1a 0%, #0a0a0a 80%);
            animation: moverFondo 12s ease infinite alternate;
        }
        @keyframes moverFondo {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        /* Color Rojo (Reemplaza al Dorado) */
        .text-rojo { color: #DC2626; }
        .bg-rojo { background-color: #DC2626; }
        .border-rojo { border-color: #DC2626; }

        /* Transiciones para tarjetas */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(220, 38, 38, 0.2);
            border-color: #DC2626 !important;
        }

        /* Animación de entrada para el hero */
        .animar-entrada {
            animation: aparecer 2s ease-out;
        }
        @keyframes aparecer {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para el Slider de imágenes */
        .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }
        .slide.activo {
            opacity: 0.6;
        }
        .overlay-hero {
            background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.4));
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
            text-align: center;
            font-size: 35px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .whatsapp-float:hover {
            background-color: #128C7E;
            transform: scale(1.1);
        }

        /* Estilos del mapa */
        .mapa-container {
            width: 100%;
            height: 100%;
            min-height: 350px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #DC2626;
        }

        /* Menú móvil */
        #menu-movil {
            display: none;
        }
    </style>
</head>
<body class="bg-animado">

    <!-- Header/Navegación -->
    <nav class="bg-black/80 backdrop-blur-md text-white p-4 sticky top-0 z-50 border-b-2 border-rojo">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/logos/logo.png" alt="Logo MadeCars" class="h-12 object-contain">
            </div>
            <div class="hidden md:flex space-x-6 font-medium">
                <a href="index.php" class="hover:text-rojo transition">Inicio</a>
                <a href="inventory.html" class="hover:text-rojo transition">Inventario</a>
                <a href="#quienes-somos" class="hover:text-rojo transition">Quiénes Somos</a>
                <a href="#contacto" class="hover:text-rojo transition">Contacto</a>
            </div>
            <div class="md:hidden">
                <button onclick="toggleMenu()" class="text-rojo text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Menú móvil desplegable -->
        <div id="menu-movil" class="md:hidden bg-black/95 mt-2 rounded-lg p-4">
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
            <h1 class="text-5xl md:text-7xl font-bold mb-4 text-rojo drop-shadow-lg">Tu próximo auto está aquí</h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-300 font-light">Encuentra el vehículo perfecto entre más de 500 opciones</p>
            <a href="inventory.html" class="bg-rojo hover:bg-red-600 text-white px-10 py-4 rounded-lg text-lg font-bold uppercase tracking-wider transition shadow-lg shadow-red-900/30">Explorar Inventario</a>
        </div>
    </section>

    <!-- Quiénes Somos -->
    <section id="quienes-somos" class="py-20 bg-gray-950/50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 text-white">¿Quiénes Somos?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gray-900 p-8 rounded-lg shadow-lg border border-gray-800 card-hover">
                    <i class="fas fa-history text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2 text-white">Nuestra Historia</h3>
                    <p class="text-gray-400">Fundada en 2010, MadeCars nació con la visión de revolucionar la compra de vehículos en Latinoamérica, ofreciendo transparencia y confianza.</p>
                </div>
                <div class="bg-gray-900 p-8 rounded-lg shadow-lg border border-gray-800 card-hover">
                    <i class="fas fa-handshake text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2 text-white">Nuestro Compromiso</h3>
                    <p class="text-gray-400">Calidad garantizada, precios justos y asesoría personalizada en cada paso de tu compra.</p>
                </div>
                <div class="bg-gray-900 p-8 rounded-lg shadow-lg border border-gray-800 card-hover">
                    <i class="fas fa-tools text-4xl text-rojo mb-4"></i>
                    <h3 class="text-xl font-bold mb-2 text-white">Nuestros Servicios</h3>
                    <p class="text-gray-400">Venta de autos nuevos y usados, financiamiento flexible, cotización personalizada y posventa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Misión y Visión -->
    <section class="py-20 bg-black">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="bg-gray-900 p-10 rounded-lg shadow-lg border border-rojo/50 card-hover">
                    <i class="fas fa-bullseye text-4xl text-rojo mb-4"></i>
                    <h3 class="text-2xl font-bold mb-3 text-white">Misión</h3>
                    <p class="text-gray-300">Democratizar el acceso a vehículos de calidad, ofreciendo soluciones financieras accesibles y un servicio excepcional que supere las expectativas de nuestros clientes.</p>
                </div>
                <div class="bg-gray-900 p-10 rounded-lg shadow-lg border border-rojo/50 card-hover">
                    <i class="fas fa-eye text-4xl text-rojo mb-4"></i>
                    <h3 class="text-2xl font-bold mb-3 text-white">Visión</h3>
                    <p class="text-gray-300">Ser el concesionario líder en Latinoamérica, reconocido por nuestra innovación, transparencia y compromiso con la satisfacción del cliente.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto (Mapa a la derecha, Información a la izquierda) -->
    <section id="contacto" class="py-20 bg-gray-950/50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12 text-white">Contáctanos</h2>

            <div class="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <!-- Bloque Izquierdo: Información y Redes Sociales -->
                <div class="bg-gray-900 p-8 rounded-2xl border border-gray-700">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-xl text-rojo"></i>
                            </div>
                            <span class="text-gray-300">+1 (809) 469-3746</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-xl text-rojo"></i>
                            </div>
                            <span class="text-gray-300">info@madecars.com - Ventas@madecars.com</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-rojo/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-location-dot text-xl text-rojo"></i>
                            </div>
                            <span class="text-gray-300">Agustin Guerrero, Higüey 23000, República Dominicana</span>
                        </div>
                    </div>

                    <!-- Redes Sociales -->
                    <div class="mt-8 pt-8 border-t border-gray-700">
                        <h4 class="text-lg font-bold text-white mb-4 text-center">Síguenos en</h4>
                        <div class="flex justify-center space-x-6 text-3xl">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/share/1Bn6YTNpck/?mibextid=wwXIfr" target="_blank" class="text-[#1877F2] hover:text-blue-400 transition transform hover:scale-110"><i class="fab fa-facebook"></i></a>
                            <!-- Instagram -->
                            <a href="https://www.instagram.com/madecarsrd?igsi=bTF3NnB5bTJiYjFu" target="_blank" class="bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 bg-clip-text text-transparent hover:scale-110 transition transform"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Bloque Derecho: Google Maps -->
                <div class="mapa-container">
                    <iframe
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
    <footer class="bg-black text-gray-400 py-8 border-t border-rojo">
        <div class="container mx-auto px-4 text-center">
            <!-- Logo del Footer -->
            <img src="img/logos/logo.png" alt="Logo MadeCars" class="h-10 mx-auto mb-4">
            <p>© 2026 MadeCars. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- BOTÓN FLOTANTE DE WHATSAPP -->
    <a href="https://wa.me/18294649902?text=Hola%20MadeCars,%20Me%20interesa%20m%C3%A1s%20informacion%20sobre" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Script para el Slider -->
    <script>
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

        // Función para abrir/cerrar menú móvil
        function toggleMenu() {
            const menu = document.getElementById('menu-movil');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }
    </script>

</body>
</html>