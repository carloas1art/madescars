// Obtener el ID de la URL
const params = new URLSearchParams(window.location.search);
const idVehiculo = params.get('id');

// Cargar datos con anti-caché
fetch('data/vehiculos.json?t=' + Date.now())
    .then(response => response.json())
    .then(data => {
        const vehiculo = data.find(v => parseInt(v.id) === parseInt(idVehiculo));
        
        if (vehiculo) {
            renderizarDetalle(vehiculo);
        } else {
            document.getElementById('contenedor-detalle').innerHTML = `
                <div class="text-center text-red-500">
                    <h2 class="text-3xl font-bold">Vehículo no encontrado</h2>
                    <a href="inventory.html" class="mt-4 inline-block text-rojo px-6 py-3 rounded-lg font-bold">Volver al Inventario</a>
                </div>
            `;
        }
    })
    .catch(error => console.error('Error al cargar el vehículo:', error));

// Función para renderizar los detalles
function renderizarDetalle(v) {
    const contenedor = document.getElementById('contenedor-detalle');
    
    const portada = v.portada || (v.imagenes && v.imagenes[0]) || v.imagen;
    const imagenes = v.imagenes && v.imagenes.length > 0 ? v.imagenes : [portada];
    const imagenPrincipal = portada;
    const miniaturas = imagenes.filter(img => img !== portada);

    contenedor.innerHTML = `
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Galería de imágenes -->
            <div class="md:w-1/2">
                <img id="imagen-principal" src="${imagenPrincipal}" alt="${v.marca} ${v.modelo}" class="w-full h-auto rounded-xl border border-gray-700 shadow-lg">
                
                ${miniaturas.length > 0 ? `
                    <div class="flex gap-2 mt-4 overflow-x-auto">
                        ${[portada, ...miniaturas].map((img, i) => `
                            <img src="${img}" alt="Miniatura ${i+1}" class="w-20 h-16 object-cover rounded-lg cursor-pointer border border-gray-700 hover:border-rojo transition" onclick="document.getElementById('imagen-principal').src='${img}'">
                        `).join('')}
                    </div>
                ` : ''}
            </div>
            
            <!-- Información del Vehículo -->
            <div class="md:w-1/2">
                <a href="inventory.html" class="text-rojo hover:text-red-400 transition">&larr; Volver al Inventario</a>
                
                <h1 class="text-4xl font-bold mt-4" style="color: var(--texto-titulo);">${v.marca} ${v.modelo}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="bg-gray-800 text-gray-300 px-3 py-1 rounded-full text-sm">${v.anio}</span>
                    <span class="bg-gray-800 text-gray-300 px-3 py-1 rounded-full text-sm">${v.transmision}</span>
                    <span class="bg-gray-800 text-gray-300 px-3 py-1 rounded-full text-sm">${v.kilometraje}</span>
                </div>
                
                <div class="mt-6">
                    <p class="text-gray-400 text-sm">Precio:</p>
                    <h2 class="precio text-4xl font-bold mt-2">${v.precio}</h2>
                </div>
                
                <div class="mt-8 space-y-4">
                    <h3 class="text-xl font-bold" style="color: var(--texto-titulo);">Características</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Tarjetas con letras blancas en ambos temas -->
                        <div class="tarjeta-caracteristica">
                            <p class="label">Marca</p>
                            <p>${v.marca}</p>
                        </div>
                        <div class="tarjeta-caracteristica">
                            <p class="label">Modelo</p>
                            <p>${v.modelo}</p>
                        </div>
                        <div class="tarjeta-caracteristica">
                            <p class="label">Año</p>
                            <p>${v.anio}</p>
                        </div>
                        <div class="tarjeta-caracteristica">
                            <p class="label">Transmisión</p>
                            <p>${v.transmision}</p>
                        </div>
                        <div class="tarjeta-caracteristica">
                            <p class="label">Kilometraje</p>
                            <p>${v.kilometraje}</p>
                        </div>
                        
                        <!-- Tarjeta de Características Adicionales (SIEMPRE visible) -->
                        <div class="tarjeta-caracteristica">
                            <p class="label">Características Adicionales</p>
                            <p>${v.caracteristicas_extra || 'Sin información adicional'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- DOS BOTONES -->
                <div class="mt-8 space-y-3">
                    <button onclick="abrirModal('modal-financiamiento', ${v.id})" class="btn-solicitar w-full font-bold py-3 rounded-lg transition">
                        Solicitar Financiamiento
                    </button>
                    <button onclick="abrirModal('modal-cotizacion', ${v.id})" class="btn-cotizar w-full font-bold py-3 rounded-lg transition">
                        Cotizar Vehículo
                    </button>
                    <a href="https://wa.me/18294649902?text=Hola%20MadeCars,%20Me%20interesa%20el%20veh%C3%ADculo%20${v.marca}%20${v.modelo}%20${v.anio}" target="_blank" class="block text-center bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-lg transition">
                        <i class="fab fa-whatsapp mr-2"></i>Cotizar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    `;
    
    // Prellenar los formularios con el vehículo
    document.getElementById('fin_vehiculo_id').value = v.id;
    document.getElementById('cot_vehiculo_id').value = v.id;
}

// Función para abrir un modal
function abrirModal(modalId, vehiculoId) {
    document.getElementById(modalId).classList.remove('hidden');
    if (modalId === 'modal-cotizacion') {
        const vehiculo = vehiculos.find(v => v.id == vehiculoId);
        if (vehiculo) {
            const precioNum = parseInt(vehiculo.precio.replace(/[^0-9]/g, '')) || 0;
            if (precioNum > 0) {
                document.querySelector('#modal-cotizacion input[name="monto_inicial"]').value = Math.round(precioNum * 0.2);
            }
        }
    }
}

// Función para cerrar un modal
function cerrarModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}