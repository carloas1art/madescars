<?php $__env->startSection('content'); ?>
<style>
:root {
    --bg-principal: #000000; --texto-principal: #ffffff; --texto-secundario: #9ca3af;
    --bg-tarjeta: #000000; --borde-tarjeta: #ffffff; --hover-tarjeta: rgba(220, 38, 38, 0.3);
    --texto-titulo: #ffffff; --btn-ver-bg: #DC2626; --btn-ver-hover: #B91C1C;
    --btn-ver-texto: #ffffff; --precio-color: #DC2626;
}
html.light-mode, body.light-mode {
    --bg-principal: #ffffff; --texto-principal: #1f2937; --texto-secundario: #4b5563;
    --bg-tarjeta: #ffffff; --borde-tarjeta: #e5e7eb; --hover-tarjeta: rgba(220, 38, 38, 0.2);
    --texto-titulo: #1f2937; --btn-ver-bg: #000000; --btn-ver-hover: #1f2937;
    --btn-ver-texto: #ffffff; --precio-color: #DC2626;
}
body { background-color: var(--bg-principal); color: var(--texto-principal); transition: background-color 0.3s ease, color 0.3s ease; }
h1, h2, h3 { font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 2px; }
.card-hover { background-color: var(--bg-tarjeta); border: 1px solid var(--borde-tarjeta); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
.card-hover:hover { transform: translateY(-10px); box-shadow: 0 15px 30px var(--hover-tarjeta); border-color: #DC2626 !important; }
.btn-ver { background-color: var(--btn-ver-bg); color: var(--btn-ver-texto); transition: all 0.3s ease; }
.btn-ver:hover { background-color: var(--btn-ver-hover); }
.precio { color: var(--precio-color); }
.select-tema { background-color: var(--bg-tarjeta); color: var(--texto-principal); border: 1px solid var(--borde-tarjeta); transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
.select-tema:focus { border-color: #DC2626; outline: none; }

/* Loader de scroll infinito */
.scroll-loader {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    grid-column: 1 / -1;
}
.scroll-loader .spinner {
    border: 3px solid var(--borde-tarjeta);
    border-top: 3px solid #DC2626;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-right: 1rem;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.end-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 2rem;
    color: var(--texto-secundario);
    font-style: italic;
}
</style>

<section class="py-10" style="background-color: var(--bg-principal);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-center mb-8" style="color: var(--texto-titulo);">NUESTRO INVENTARIO</h1>

        <div class="flex flex-wrap justify-center gap-4 mb-10">
            <select id="filtro-marca" class="select-tema rounded-lg px-4 py-3 font-poppins min-w-[200px]">
                <option value="">Todas las Marcas</option>
            </select>
            <select id="filtro-precio" class="select-tema rounded-lg px-4 py-3 font-poppins min-w-[200px]">
                <option value="">Todos los Precios</option>
                <option value="0-50000">Menos de $50,000</option>
                <option value="50000-100000">$50,000 - $100,000</option>
                <option value="100000-200000">$100,000 - $200,000</option>
                <option value="200000-999999">Más de $200,000</option>
            </select>
            <select id="filtro-anio" class="select-tema rounded-lg px-4 py-3 font-poppins min-w-[200px]">
                <option value="">Todos los Años</option>
            </select>
            <button onclick="aplicarFiltros()" class="bg-brand-red hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 font-montserrat flex items-center">
                <i class="fas fa-search mr-2"></i> Filtrar
            </button>
            <button onclick="refrescarInventario()" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 font-montserrat flex items-center" title="Refrescar datos">
                <i class="fas fa-sync-alt mr-2"></i> Refrescar
            </button>
        </div>

        <!-- Contador de vehículos -->
        <div class="text-center mb-6">
            <p id="contador-vehiculos" style="color: var(--texto-secundario);">Cargando vehículos...</p>
        </div>

        <div id="grid-vehiculos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 min-h-[350px]">
            <!-- Spinner inicial visible -->
            <div class="col-span-full text-center py-20">
                <i class="fas fa-spinner fa-spin text-4xl text-brand-red mb-4"></i>
                <p style="color: var(--texto-secundario);" class="font-poppins text-lg">Obteniendo información de los vehículos...</p>
            </div>
        </div>
    </div>
</section>

<script>
// Estado global del scroll infinito
let currentPage = 1;
let totalPages = 1;
let isLoading = false;
let allLoaded = false;
const detalleBaseUrl = '<?php echo e(url("/detalle")); ?>';
const apiUrl = '<?php echo e(route("api.inventario")); ?>';

document.addEventListener('DOMContentLoaded', function() {
    cargarPagina(1, true);

    // 🔑 SCROLL INFINITO: Detectar cuando el usuario llega al final
    window.addEventListener('scroll', () => {
        if (isLoading || allLoaded) return;

        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.body.offsetHeight - 500;

        if (scrollPosition >= threshold) {
            cargarMasVehiculos();
        }
    });
});

async function cargarPagina(page, esPrimeraCarga = false) {
    if (isLoading) return;
    isLoading = true;

    const grid = document.getElementById('grid-vehiculos');

    if (!esPrimeraCarga) {
        // Agregar loader de scroll
        const loaderDiv = document.createElement('div');
        loaderDiv.className = 'scroll-loader';
        loaderDiv.id = 'scroll-loader';
        loaderDiv.innerHTML = '<div class="spinner"></div><p style="color: var(--texto-secundario);">Cargando más vehículos...</p>';
        grid.appendChild(loaderDiv);
    }

    try {
        const url = `${apiUrl}?page=${page}`;
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });

        if (!response.ok) throw new Error('Error del servidor');

        const data = await response.json();

        // Remover loader
        const loader = document.getElementById('scroll-loader');
        if (loader) loader.remove();

        if (esPrimeraCarga) {
            grid.innerHTML = '';
        }

        if (!data.vehiculos || data.vehiculos.length === 0) {
            if (esPrimeraCarga) {
                grid.innerHTML = '<div class="col-span-full text-center py-20"><i class="fas fa-car text-6xl text-gray-600 mb-4"></i><p style="color: var(--texto-secundario);">No hay vehículos disponibles en este momento.</p></div>';
            }
            allLoaded = true;
            return;
        }

        // Agregar nuevas tarjetas
        data.vehiculos.forEach(v => agregarTarjeta(v));

        // Actualizar estado
        currentPage = data.pagina || page;
        totalPages = data.paginas || 1;

        if (currentPage >= totalPages) {
            allLoaded = true;
            mostrarMensajeFinal(grid);
        }

        // Actualizar contador
        const totalMostrados = grid.querySelectorAll('.vehiculo-item').length;
        document.getElementById('contador-vehiculos').textContent =
            `Mostrando ${totalMostrados} de ${data.total} vehículos`;

        // Poblar filtros solo en la primera carga
        if (esPrimeraCarga) {
            poblarFiltros();
        }

    } catch (error) {
        console.error(error);
        const loader = document.getElementById('scroll-loader');
        if (loader) loader.remove();

        if (esPrimeraCarga) {
            grid.innerHTML = '<div class="col-span-full text-center py-20"><i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i><p style="color: var(--texto-secundario);">Error al cargar el inventario. Por favor, recarga la página.</p></div>';
        }
    } finally {
        isLoading = false;
    }
}

function cargarMasVehiculos() {
    if (allLoaded || isLoading) return;
    cargarPagina(currentPage + 1, false);
}

function agregarTarjeta(v) {
    const grid = document.getElementById('grid-vehiculos');
    const imgHtml = v.portada
        ? '<img src="' + v.portada + '" alt="' + v.marca + ' ' + v.modelo + '" class="w-full h-full object-cover">'
        : '<i class="fas fa-car text-5xl text-gray-500"></i>';

    const precioNumerico = v.precio.replace(/[^0-9]/g, '');

    const card = document.createElement('div');
    card.className = 'card-hover rounded-lg overflow-hidden shadow-lg vehiculo-item flex flex-col';
    card.dataset.marca = v.marca.toLowerCase();
    card.dataset.anio = v.anio;
    card.dataset.precio = precioNumerico;

    card.innerHTML = `
        <div class="h-48 bg-gray-700 flex items-center justify-center relative overflow-hidden flex-shrink-0">
            ${imgHtml}
            <span class="absolute top-2 right-2 bg-brand-red text-white text-xs font-bold px-2 py-1 rounded">Disponible</span>
        </div>
        <div class="p-6 flex flex-col flex-grow">
            <h3 class="text-lg font-bold mb-2" style="color: var(--texto-titulo);">${v.marca} ${v.modelo}</h3>
            <div class="flex-grow"></div>
            <div class="mt-auto">
                <p class="text-sm mb-4" style="color: var(--texto-secundario);">${v.anio} | ${v.transmision} | ${v.kilometraje} km</p>
                <p class="precio text-2xl font-bold mb-4 font-montserrat">${v.precio}</p>
                <a href="${detalleBaseUrl}/${v.id}" class="btn-ver block w-full text-center font-bold py-3 rounded-lg font-montserrat">Ver Detalles</a>
            </div>
        </div>
    `;

    grid.appendChild(card);
}

function mostrarMensajeFinal(grid) {
    const msg = document.createElement('div');
    msg.className = 'end-message';
    msg.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Has visto todos los vehículos disponibles';
    grid.appendChild(msg);
}

function poblarFiltros() {
    const vehiculos = document.querySelectorAll('.vehiculo-item');
    const filtroMarca = document.getElementById('filtro-marca');
    const filtroAnio = document.getElementById('filtro-anio');
    const marcas = new Set();
    const anios = new Set();

    vehiculos.forEach(v => {
        marcas.add(v.dataset.marca);
        anios.add(v.dataset.anio);
    });

    marcas.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m;
        opt.textContent = m.charAt(0).toUpperCase() + m.slice(1);
        filtroMarca.appendChild(opt);
    });

    Array.from(anios).sort().reverse().forEach(a => {
        const opt = document.createElement('option');
        opt.value = a;
        opt.textContent = a;
        filtroAnio.appendChild(opt);
    });
}

function aplicarFiltros() {
    const marca = document.getElementById('filtro-marca').value.toLowerCase();
    const anio = document.getElementById('filtro-anio').value;
    document.querySelectorAll('.vehiculo-item').forEach(v => {
        const ok = (!marca || v.dataset.marca === marca) && (!anio || v.dataset.anio === anio);
        v.style.display = ok ? 'flex' : 'none';
    });
}

function refrescarInventario() {
    // Resetear estado del scroll infinito
    currentPage = 1;
    allLoaded = false;
    isLoading = false;
    cargarPagina(1, true);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Madecars\resources\views/inventario.blade.php ENDPATH**/ ?>