let vehiculos = [];
let vehiculosMostrados = 0;
const CANTIDAD_INICIAL = 6;
const CANTIDAD_POR_SCROLL = 6;
let cargando = false;

// Cargar datos JSON (CON ANTI-CACHÉ)
fetch('data/vehiculos.json?t=' + Date.now())
    .then(response => response.json())
    .then(data => {
        vehiculos = data;
        llenarFiltros();
        cargarMasVehiculos();
    })
    .catch(error => console.error('Error al cargar los vehículos:', error));

// Llenar filtros dinámicamente
function llenarFiltros() {
    const marcas = [...new Set(vehiculos.map(v => v.marca))].sort();
    const años = [...new Set(vehiculos.map(v => v.anio))].sort((a,b) => b - a);
    
    const selectMarca = document.getElementById('filtro-marca');
    const selectAño = document.getElementById('filtro-anio');

    marcas.forEach(marca => {
        const option = document.createElement('option');
        option.value = marca;
        option.textContent = marca;
        selectMarca.appendChild(option);
    });

    años.forEach(año => {
        const option = document.createElement('option');
        option.value = año;
        option.textContent = año;
        selectAño.appendChild(option);
    });

    // Eventos
    selectMarca.addEventListener('change', filtrar);
    document.getElementById('filtro-precio').addEventListener('change', filtrar);
    selectAño.addEventListener('change', filtrar);
}

// Cargar más vehículos
function cargarMasVehiculos() {
    if (cargando || vehiculosMostrados >= vehiculos.length) return;
    cargando = true;

    const grid = document.getElementById('grid-vehiculos');
    const indicador = document.getElementById('cargando-mas');
    indicador.classList.remove('hidden');

    setTimeout(() => {
        const siguientes = vehiculos.slice(vehiculosMostrados, vehiculosMostrados + CANTIDAD_POR_SCROLL);
        
        siguientes.forEach(v => {
            const card = document.createElement('div');
            card.className = 'card-hover flex flex-col';
            card.innerHTML = `
                <img src="${v.portada || v.imagenes[0]}" alt="${v.marca} ${v.modelo}" loading="lazy" class="w-full h-48 object-cover">
                <div class="p-4 flex-grow flex flex-col">
                    
                    <!-- Bloque superior: Nombre (con altura mínima para que no se rompa) -->
                    <div class="min-h-[60px]">
                        <h3 class="text-xl font-bold" style="color: var(--texto-titulo);">${v.marca} ${v.modelo}</h3>
                    </div>
                    
                    <!-- Bloque inferior fijo (siempre al fondo) -->
                    <div class="mt-auto">
                        <!-- Año, Transmisión y Kilometraje justo arriba del precio -->
                        <p class="text-sm" style="color: var(--texto-secundario);">${v.anio} | ${v.transmision} | ${v.kilometraje}</p>
                        
                        <!-- Precio -->
                        <div class="precio font-bold text-2xl mt-2">${v.precio}</div>
                        
                        <!-- Botón -->
                        <a href="detalle.html?id=${v.id}" class="btn-ver block text-center font-bold py-2 rounded-lg mt-4 transition">Ver Detalles</a>
                    </div>
                </div>
            `;
            grid.appendChild(card);
        });

        vehiculosMostrados += siguientes.length;
        indicador.classList.add('hidden');
        cargando = false;
    }, 300);
}

// Detectar scroll
const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
        cargarMasVehiculos();
    }
}, { rootMargin: '200px' });

const sentinel = document.createElement('div');
sentinel.id = 'sentinel';
sentinel.style.height = '10px';
document.body.appendChild(sentinel);
observer.observe(sentinel);

// Filtrar
function filtrar() {
    const marca = document.getElementById('filtro-marca').value;
    const precio = document.getElementById('filtro-precio').value;
    const año = document.getElementById('filtro-anio').value;

    const filtrados = vehiculos.filter(v => {
        const cumpleMarca = marca === '' || v.marca === marca;
        const cumpleAño = año === '' || v.anio == año;
        
        let cumplePrecio = true;
        if (precio !== '') {
            const [min, max] = precio.split('-').map(Number);
            const precioNum = parseInt(v.precio.replace(/[^0-9]/g, '')) || 0;
            cumplePrecio = precioNum >= min && precioNum <= max;
        }

        return cumpleMarca && cumpleAño && cumplePrecio;
    });

    vehiculosMostrados = 0;
    document.getElementById('grid-vehiculos').innerHTML = '';
    
    const primeros = filtrados.slice(0, CANTIDAD_INICIAL);
    primeros.forEach(v => {
        const card = document.createElement('div');
        card.className = 'card-hover flex flex-col';
        card.innerHTML = `
            <img src="${v.portada || v.imagenes[0]}" alt="${v.marca} ${v.modelo}" loading="lazy" class="w-full h-48 object-cover">
            <div class="p-4 flex-grow flex flex-col">
                
                <!-- Bloque superior: Nombre (con altura mínima) -->
                <div class="min-h-[60px]">
                    <h3 class="text-xl font-bold" style="color: var(--texto-titulo);">${v.marca} ${v.modelo}</h3>
                </div>
                
                <!-- Bloque inferior fijo (siempre al fondo) -->
                <div class="mt-auto">
                    <!-- Año, Transmisión y Kilometraje justo arriba del precio -->
                    <p class="text-sm" style="color: var(--texto-secundario);">${v.anio} | ${v.transmision} | ${v.kilometraje}</p>
                    
                    <!-- Precio -->
                    <div class="precio font-bold text-2xl mt-2">${v.precio}</div>
                    
                    <!-- Botón -->
                    <a href="detalle.html?id=${v.id}" class="btn-ver block text-center font-bold py-2 rounded-lg mt-4 transition">Ver Detalles</a>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });

    vehiculosMostrados = primeros.length;
}