// Obtener parámetros de la URL (vehículo seleccionado)
const params = new URLSearchParams(window.location.search);
const vehiculoSeleccionado = params.get('id');

// Cargar vehículos
fetch('data/vehiculos.json?t=' + Date.now())
    .then(response => response.json())
    .then(data => {
        const selectVehiculo = document.getElementById('vehiculo_id');
        
        // Llenar select con vehículos
        data.forEach(v => {
            const option = document.createElement('option');
            option.value = v.id;
            option.textContent = `${v.marca} ${v.modelo} (${v.anio}) - ${v.precio}`;
            selectVehiculo.appendChild(option);
        });
        
        // Si se seleccionó un vehículo desde la página de detalles, seleccionarlo automáticamente
        if (vehiculoSeleccionado) {
            selectVehiculo.value = vehiculoSeleccionado;
            mostrarInfoVehiculo(vehiculoSeleccionado);
        }
    });

// Mostrar información del vehículo seleccionado
document.getElementById('vehiculo_id').addEventListener('change', function() {
    mostrarInfoVehiculo(this.value);
});

function mostrarInfoVehiculo(id) {
    fetch('data/vehiculos.json?t=' + Date.now())
        .then(response => response.json())
        .then(data => {
            const v = data.find(v => v.id == id);
            if (v) {
                document.getElementById('info-vehiculo').classList.remove('hidden');
                document.getElementById('img-vehiculo').src = v.portada || v.imagenes[0];
                document.getElementById('nombre-vehiculo').textContent = `${v.marca} ${v.modelo} (${v.anio})`;
                document.getElementById('precio-vehiculo').textContent = v.precio;
                
                // Prellenar monto inicial sugerido (ej: 20% del precio)
                const precioNum = parseInt(v.precio.replace(/[^0-9]/g, '')) || 0;
                if (precioNum > 0) {
                    document.getElementById('monto_inicial').value = Math.round(precioNum * 0.2);
                }
            }
        });
}

// Calcular cuota mensual
function calcularCuota() {
    const precio = parseInt(document.getElementById('vehiculo_id').selectedOptions[0].textContent.match(/\d+/)?.[0] || 0);
    const montoInicial = parseFloat(document.getElementById('monto_inicial').value) || 0;
    const plazo = parseInt(document.getElementById('plazo').value) || 12;
    const tasa = parseFloat(document.getElementById('tasa').value) || 12;
    
    // Monto a financiar = precio - monto inicial
    const montoFinanciar = precio - montoInicial;
    
    if (montoFinanciar > 0) {
        // Fórmula de cuota fija (amortización)
        const tasaMensual = (tasa / 100) / 12;
        const cuota = (montoFinanciar * tasaMensual) / (1 - Math.pow(1 + tasaMensual, -plazo));
        
        document.getElementById('cuota-estimada').textContent = `$${cuota.toFixed(2)}`;
    } else {
        document.getElementById('cuota-estimada').textContent = '$0.00';
    }
}

// Eventos para calcular cuota
document.getElementById('monto_inicial').addEventListener('input', calcularCuota);
document.getElementById('plazo').addEventListener('change', calcularCuota);
document.getElementById('tasa').addEventListener('input', calcularCuota);

// Enviar formulario (por ahora simulado)
document.getElementById('form-cotizacion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Guardar datos en localStorage (para pruebas)
    const formData = new FormData(this);
    const datos = Object.fromEntries(formData.entries());
    localStorage.setItem('solicitud_financiamiento', JSON.stringify(datos));
    
    alert('✅ Solicitud enviada correctamente.\n\n(En producción, esta solicitud se enviará a la API de Finsoftek)');
    
    // Limpiar formulario
    this.reset();
    document.getElementById('cuota-estimada').textContent = '$0.00';
    document.getElementById('info-vehiculo').classList.add('hidden');
});