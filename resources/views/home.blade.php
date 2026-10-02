@extends('layouts.app')

@section('content')
<style>
    /* Variables de color para ambos temas */
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

        /* Botón Explorar Inventario (modo oscuro) */
        --btn-inventario-bg: #DC2626;
        --btn-inventario-hover: #B91C1C;
        --btn-inventario-texto: #ffffff;
    }

    html.light-mode,
    body.light-mode {
        --bg-principal: #ffffff;
        --texto-principal: #1f2937;
        --texto-secundario: #4b5563;
        --bg-tarjeta: #ffffff;
        --borde-tarjeta: #e5e7eb;
        --hover-tarjeta: rgba(220, 38, 38, 0.2);
        --texto-titulo: #1f2937;
        --filtro-mapa: none;
        --overlay-hero: linear-gradient(to top, rgba(255,255,255,0.5), rgba(255,255,255,0.4));

        /* Botón Explorar Inventario (modo claro) */
        --btn-inventario-bg: #000000;
        --btn-inventario-hover: #1f2937;
        --btn-inventario-texto: #ffffff;
    }

    /* Carrusel con fade */
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
        background: var(--overlay-hero);
    }

    /* Tarjetas con sombra roja */
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

    /* Mapa */
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

    /* Botón Explorar Inventario */
    #btn-inventario {
        background-color: var(--btn-inventario-bg);
        color: var(--btn-inventario-texto);
        transition: background-color 0.3s ease;
    }
    #btn-inventario:hover {
        background-color: var(--btn-inventario-hover);
    }

    /* Iconos de contacto sin silueta */
    .icono-contacto {
        color: #DC2626;
        font-size: 1.5rem;
        width: 2rem;
        text-align: center;
        flex-shrink: 0;
    }
</style>

<!-- HERO SECTION CON CARRUSEL -->
<section class="relative h-[700px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0" id="slider-hero">
        <!-- Imagen 1 -->
        <div class="slide activo" style="background-image: url('{{ asset('img/carrusel/auto1.jpg') }}');"></div>
        <!-- Imagen 2 -->
        <div class="slide" style="background-image: url('{{ asset('img/carrusel/auto2.jpg') }}');"></div>
        <!-- Imagen 3 -->
        <div class="slide" style="background-image: url('{{ asset('img/carrusel/auto3.jpg') }}');"></div>

        <div class="overlay-hero absolute inset-0"></div>
    </div>
    <div class="relative text-center px-4 z-10">
        <h1 id="hero-title" class="text-5xl md:text-7xl font-bold mb-4 drop-shadow-lg font-montserrat tracking-wide" style="color: var(--texto-titulo);">
            TU PRÓXIMO AUTO ESTÁ AQUÍ
        </h1>
        <p id="hero-subtitle" class="text-xl md:text-2xl mb-8 font-light font-poppins" style="color: var(--texto-secundario);">
            Encuentra el vehículo perfecto entre más de 500 opciones
        </p>
        <a href="{{ url('/inventario') }}" id="btn-inventario" class="inline-block font-bold py-4 px-10 rounded-lg text-lg font-montserrat tracking-wide shadow-lg hover:scale-105 transition-transform">
            EXPLORAR INVENTARIO
        </a>
    </div>
</section>

<!-- ¿QUIÉNES SOMOS? -->
<section id="quienes-somos" class="py-20" style="background-color: var(--bg-principal);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-16 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
            ¿QUIÉNES SOMOS?
        </h2>

        <!-- Primera fila: 3 tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- Historia -->
            <div class="card-hover rounded-lg p-8 shadow-lg">
                <i class="fas fa-history text-4xl text-brand-red mb-4"></i>
                <h3 class="text-xl font-bold mb-4 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
                    NUESTRA HISTORIA
                </h3>
                <p style="color: var(--texto-secundario);" class="font-poppins leading-relaxed">
                    Fundada en 2010, MadeCars nació con la visión de revolucionar la compra de vehículos en Latinoamérica, ofreciendo transparencia y confianza.
                </p>
            </div>

            <!-- Compromiso -->
            <div class="card-hover rounded-lg p-8 shadow-lg">
                <i class="fas fa-handshake text-4xl text-brand-red mb-4"></i>
                <h3 class="text-xl font-bold mb-4 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
                    NUESTRO COMPROMISO
                </h3>
                <p style="color: var(--texto-secundario);" class="font-poppins leading-relaxed">
                    Calidad garantizada, precios justos y asesoría personalizada en cada paso de tu compra.
                </p>
            </div>

            <!-- Servicios -->
            <div class="card-hover rounded-lg p-8 shadow-lg">
                <i class="fas fa-tools text-4xl text-brand-red mb-4"></i>
                <h3 class="text-xl font-bold mb-4 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
                    NUESTROS SERVICIOS
                </h3>
                <p style="color: var(--texto-secundario);" class="font-poppins leading-relaxed">
                    Venta de autos nuevos y usados, financiamiento flexible, cotización personalizada y asesoría.
                </p>
            </div>
        </div>

        <!-- Segunda fila: 2 tarjetas centradas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Misión -->
            <div class="card-hover rounded-lg p-10 shadow-lg">
                <i class="fas fa-bullseye text-4xl text-brand-red mb-4"></i>
                <h3 class="text-2xl font-bold mb-3 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
                    MISIÓN
                </h3>
                <p style="color: var(--texto-secundario);" class="font-poppins leading-relaxed">
                    Democratizar el acceso a vehículos de calidad, ofreciendo soluciones financieras accesibles y un servicio excepcional que supere las expectativas de nuestros clientes.
                </p>
            </div>

            <!-- Visión -->
            <div class="card-hover rounded-lg p-10 shadow-lg">
                <i class="fas fa-eye text-4xl text-brand-red mb-4"></i>
                <h3 class="text-2xl font-bold mb-3 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
                    VISIÓN
                </h3>
                <p style="color: var(--texto-secundario);" class="font-poppins leading-relaxed">
                    Ser el concesionario líder en Latinoamérica, reconocido por nuestra innovación, transparencia y compromiso con la satisfacción del cliente.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CONTÁCTANOS -->
<section id="contacto" class="py-20" style="background-color: var(--bg-principal);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-16 font-montserrat tracking-wide" style="color: var(--texto-titulo);">
            CONTÁCTANOS
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <!-- Información de Contacto -->
            <div class="card-hover p-8 rounded-2xl">
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-phone icono-contacto"></i>
                        <span style="color: var(--texto-secundario);" class="font-poppins">+1 (809) 469-3746</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <i class="fas fa-envelope icono-contacto"></i>
                        <span style="color: var(--texto-secundario);" class="font-poppins">info@madecars.com - Ventas@madecars.com</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <i class="fas fa-map-marker-alt icono-contacto"></i>
                        <span style="color: var(--texto-secundario);" class="font-poppins">Agustin Guerrero, Higüey 23000, República Dominicana</span>
                    </div>
                </div>

                <div class="mt-16 pt-6 border-t" style="border-color: var(--borde-tarjeta);">
                    <h4 class="text-lg font-bold mb-4 text-center font-montserrat" style="color: var(--texto-titulo);">Síguenos en</h4>
                    <div class="flex justify-center space-x-6 text-3xl">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1Bn6YTNpck/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer" class="text-[#1877F2] hover:text-blue-400 transition transform hover:scale-110" aria-label="Facebook MadeCars">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/madecarsrd?igsi=bTF3NnB5bTJiYjFu" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 bg-clip-text text-transparent hover:scale-110 transition transform" aria-label="Instagram MadeCars">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mapa -->
            <div class="mapa-container">
                <iframe
                    id="mapa-tema"
                    src="https://www.google.com/maps?q=Madecarsrd,+Agustin+Guerrero,+Hig%C3%BCey+23000,+Rep%C3%BAblica+Dominicana&output=embed"
                    width="100%"
                    height="400"
                    style="border:0; filter: var(--filtro-mapa); transition: filter 0.3s ease;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        </div>
    </div>
</section>

<script>
// Carrusel con fade
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
</script>
@endsection
