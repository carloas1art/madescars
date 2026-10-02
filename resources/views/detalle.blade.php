@extends('layouts.app')

@section('content')
<style>
    :root {
        --bg-principal: #000000; --texto-principal: #ffffff; --texto-secundario: #9ca3af;
        --bg-tarjeta: #000000; --borde-tarjeta: #ffffff;
        --texto-titulo: #ffffff;
        --btn-solicitar-bg: #DC2626; --btn-solicitar-hover: #B91C1C; --btn-solicitar-texto: #ffffff;
        --btn-cotizar-bg: #ffffff; --btn-cotizar-hover: #f3f4f6; --btn-cotizar-texto: #000000;
        --precio-color: #DC2626; --texto-tarjeta-detalle: #ffffff;
        --form-bg: #000000; --form-titulo: #ffffff; --form-input-bg: #1a1a1a;
        --form-input-texto: #ffffff; --form-label: #9ca3af; --form-borde: #ffffff;
    }
    html.light-mode, body.light-mode {
        --bg-principal: #ffffff; --texto-principal: #1f2937; --texto-secundario: #4b5563;
        --bg-tarjeta: #ffffff; --borde-tarjeta: #e5e7eb;
        --texto-titulo: #1f2937;
        --btn-solicitar-bg: #000000; --btn-solicitar-hover: #1f2937; --btn-solicitar-texto: #ffffff;
        --btn-cotizar-bg: #DC2626; --btn-cotizar-hover: #B91C1C; --btn-cotizar-texto: #ffffff;
        --precio-color: #DC2626; --texto-tarjeta-detalle: #1f2937;
        --form-bg: #f3f4f6; --form-titulo: #000000; --form-input-bg: #ffffff;
        --form-input-texto: #000000; --form-label: #4b5563; --form-borde: #e5e7eb;
    }
    body { background-color: var(--bg-principal); color: var(--texto-principal); }
    h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; text-transform: uppercase; letter-spacing: 1px; }
    .btn-solicitar { background-color: var(--btn-solicitar-bg); color: var(--btn-solicitar-texto); transition: all 0.3s ease; }
    .btn-solicitar:hover { background-color: var(--btn-solicitar-hover); }
    .btn-cotizar { background-color: var(--btn-cotizar-bg); color: var(--btn-cotizar-texto); transition: all 0.3s ease; }
    .btn-cotizar:hover { background-color: var(--btn-cotizar-hover); }
    .precio { color: var(--precio-color); }
    .tarjeta-caracteristica { background-color: var(--bg-tarjeta); border: 1px solid var(--borde-tarjeta); padding: 1rem; border-radius: 0.5rem; }
    .tarjeta-caracteristica p { color: var(--texto-tarjeta-detalle); }
    .tarjeta-caracteristica .label { color: var(--texto-secundario); font-size: 0.75rem; }

    .form-contenedor {
        background-color: var(--form-bg);
        border: 1px solid var(--form-borde);
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .form-label { color: var(--form-label); font-size: 0.875rem; margin-bottom: 0.25rem; display: block; }
    .form-input { background-color: var(--form-input-bg); color: var(--form-input-texto); border: 1px solid var(--form-borde); border-radius: 0.5rem; padding: 0.75rem 1rem; width: 100%; }
    .form-input:focus { outline: none; border-color: #DC2626; }
    .miniatura { cursor: pointer; border: 2px solid transparent; transition: all 0.3s ease; }
    .miniatura:hover, .miniatura.activa { border-color: #DC2626; }

    .modal-scroll::-webkit-scrollbar { width: 8px; }
    .modal-scroll::-webkit-scrollbar-track { background: transparent; }
    .modal-scroll::-webkit-scrollbar-thumb { background-color: var(--form-borde); border-radius: 4px; }
    .modal-scroll::-webkit-scrollbar-thumb:hover { background-color: #DC2626; }
</style>

<div class="container mx-auto px-4 py-10">
    <nav class="text-sm mb-8 font-poppins" style="color: var(--texto-secundario);">
        <a href="{{ url('/') }}" class="hover:text-brand-red transition">Inicio</a>
        <span class="mx-2">/</span>
        <a href="{{ route('inventario') }}" class="hover:text-brand-red transition">Inventario</a>
        <span class="mx-2">/</span>
        <span id="breadcrumb-titulo" style="color: var(--texto-principal);">Cargando...</span>
    </nav>

    <div id="detalle-contenido" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="col-span-full text-center py-20">
            <i class="fas fa-spinner fa-spin text-4xl text-brand-red mb-4"></i>
            <p style="color: var(--texto-secundario);" class="font-poppins text-lg">Cargando detalles del vehículo...</p>
        </div>
    </div>
</div>

<div id="modal-solicitud" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.8);">
    <div class="form-contenedor w-full max-w-4xl max-h-[90vh] flex flex-col relative">

        <div class="flex-shrink-0 flex justify-between items-center px-6 py-4 border-b z-20" style="background-color: var(--form-bg); border-color: var(--form-borde);">
            <h3 id="modal-titulo" class="text-2xl font-bold text-brand-red font-poppins">SOLICITUD</h3>
            <button onclick="cerrarModal('modal-solicitud')" class="text-3xl hover:text-brand-red transition-colors" style="color: var(--form-label);">&times;</button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 modal-scroll">
            <form id="form-solicitud" class="space-y-6">
                <input type="hidden" id="solicitud-tipo" value=""> <!-- 1=Cotizar, 2=Financiar -->
                <input type="hidden" id="solicitud-vehiculo-id" value="">
                <input type="hidden" id="solicitud-monto" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-lg" style="background-color: var(--form-input-bg);">
                    <div>
                        <label class="form-label">Vehículo de Interés</label>
                        <input type="text" id="solicitud-vehiculo-nombre" readonly class="form-input opacity-70 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="form-label">Monto de Referencia (RD$)</label>
                        <input type="text" id="solicitud-monto-display" readonly class="form-input opacity-70 cursor-not-allowed">
                    </div>
                </div>

                <h4 class="text-lg font-bold font-poppins border-b pb-2" style="color: var(--form-titulo); border-color: var(--form-borde);">DATOS PERSONALES</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="form-label">Nombres *</label><input type="text" name="nombres" required class="form-input"></div>
                    <div><label class="form-label">Apellidos *</label><input type="text" name="apellidos" required class="form-input"></div>
                    <div><label class="form-label">Apodo (Opcional)</label><input type="text" name="apodo" class="form-input"></div>
                    <div><label class="form-label">Cédula *</label><input type="text" name="cedula" required class="form-input"></div>
                    <div>
                        <label class="form-label">Género *</label>
                        <select name="genero" required class="form-input">
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Estado Civil *</label>
                        <select name="estado_civil" required class="form-input">
                            <option value="1">Soltero/a</option>
                            <option value="2">Casado/a</option>
                            <option value="3">Unión Libre</option>
                            <option value="4">Divorciado/a</option>
                            <option value="5">Viudo/a</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Fecha de Nacimiento *</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required class="form-input">
                    </div>
                    <div><label class="form-label">Correo Electrónico *</label><input type="email" name="email" required class="form-input"></div>
                </div>

                <h4 class="text-lg font-bold font-poppins border-b pb-2" style="color: var(--form-titulo); border-color: var(--form-borde);">UBICACIÓN Y CONTACTO</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="form-label">Dirección Completa *</label><input type="text" name="direccion" required class="form-input"></div>
                    <div><label class="form-label">Ciudad *</label><input type="text" name="ciudad" required class="form-input"></div>
                    <div><label class="form-label">Provincia *</label><input type="text" name="provincia" required class="form-input"></div>
                    <div><label class="form-label">Celular *</label><input type="tel" name="celular" required class="form-input"></div>
                    <div><label class="form-label">Teléfono Hogar</label><input type="tel" name="tel_hogar" class="form-input"></div>
                    <div><label class="form-label">Teléfono Laboral</label><input type="tel" name="tel_laboral" class="form-input"></div>
                </div>

                <h4 class="text-lg font-bold font-poppins border-b pb-2" style="color: var(--form-titulo); border-color: var(--form-borde);">DATOS LABORALES</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="form-label">Nombre de la Empresa *</label><input type="text" name="empresa_ingreso" required class="form-input"></div>
                    <div>
                        <label class="form-label">Ciclo de Ingreso *</label>
                        <select name="ciclo_ingreso" required class="form-input">
                            <option value="1">Mensual</option>
                            <option value="2">Quincenal</option>
                            <option value="3">Semanal</option>
                        </select>
                    </div>
                    <div><label class="form-label">Monto de Ingreso Mensual (RD$) *</label><input type="number" name="monto_ingreso" required class="form-input"></div>
                    <div><label class="form-label">Cargo que Ocupa *</label><input type="text" name="cargo" required class="form-input"></div>
                    <div class="md:col-span-2"><label class="form-label">Nombre del Supervisor *</label><input type="text" name="supervisor" required class="form-input"></div>
                </div>

                <button type="submit" id="btn-enviar-solicitud" class="btn-cotizar w-full font-bold py-4 rounded-lg mt-6 flex justify-center items-center gap-2 transition-all">
                    <span>Enviar Solicitud</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehiculoId = {{ $id ?? 'null' }};
    if (!vehiculoId) return;

    const contentDiv = document.getElementById('detalle-contenido');

    fetch('{{ url("/api/detalle-data") }}/' + vehiculoId, { headers: { 'Accept': 'application/json' } })
        .then(response => {
            if (!response.ok) throw new Error('Vehículo no encontrado');
            return response.json();
        })
        .then(data => {
            const v = data.vehiculo;
            document.getElementById('breadcrumb-titulo').textContent = v.marca + ' ' + v.modelo;

            document.getElementById('solicitud-vehiculo-id').value = v.id;
            const precioLimpio = v.precio ? v.precio.toString().replace(/[^0-9]/g, '') : '0';
            document.getElementById('solicitud-monto').value = precioLimpio;
            document.getElementById('solicitud-vehiculo-nombre').value = v.marca + ' ' + v.modelo + ' ' + v.anio;
            document.getElementById('solicitud-monto-display').value = v.precio;

            let imagenesHtml = '';
            if (v.imagenes && v.imagenes.length > 0) {
                v.imagenes.forEach(function(img, index) {
                    const activa = index === 0 ? 'activa' : '';
                    imagenesHtml += '<img src="' + img + '" alt="Vista ' + (index + 1) + '" class="miniatura w-full h-20 object-cover rounded ' + activa + '" onclick="cambiarImagen(\'' + img.replace(/'/g, "\\'") + '\', this)">';
                });
            }

            const portadaSrc = v.portada ? v.portada : '';
            contentDiv.innerHTML =
                '<div>' +
                    '<div class="w-full h-[400px] bg-gray-800 rounded-lg overflow-hidden mb-4">' +
                        '<img id="imagen-principal" src="' + portadaSrc + '" alt="' + v.marca + ' ' + v.modelo + '" class="w-full h-full object-cover">' +
                    '</div>' +
                    '<div class="grid grid-cols-4 gap-2 mb-6">' + imagenesHtml + '</div>' +
                    (v.descripcion ? '<div class="mt-20 pt-6 border-t border-gray-800 mb-6"><h2 class="text-2xl font-bold mb-4" style="color: var(--texto-titulo);">DESCRIPCIÓN</h2><p style="color: var(--texto-secundario);" class="leading-relaxed text-sm">' + v.descripcion + '</p></div>' : '') +
                '</div>' +
                '<div>' +
                    '<h1 class="text-4xl font-bold mb-4" style="color: var(--texto-titulo);">' + v.marca + ' ' + v.modelo + '</h1>' +
                    '<div class="flex flex-wrap gap-2 mb-6">' +
                        '<span class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-poppins">' + v.anio + '</span>' +
                        '<span class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-poppins">' + v.transmision + '</span>' +
                        '<span class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-poppins">' + v.kilometraje + ' km</span>' +
                        (v.color ? '<span class="bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-poppins">' + v.color + '</span>' : '') +
                    '</div>' +
                    '<div class="mb-8">' +
                        '<p class="text-sm mb-2 font-poppins" style="color: var(--texto-secundario);">Precio:</p>' +
                        '<p class="precio text-4xl font-bold font-montserrat">' + v.precio + '</p>' +
                    '</div>' +
                    '<h2 class="text-2xl font-bold mb-6" style="color: var(--texto-titulo);">CARACTERÍSTICAS</h2>' +
                    '<div class="grid grid-cols-2 gap-4 mb-6">' +
                        '<div class="tarjeta-caracteristica"><p class="label">Marca</p><p class="font-semibold">' + v.marca + '</p></div>' +
                        '<div class="tarjeta-caracteristica"><p class="label">Modelo</p><p class="font-semibold">' + v.modelo + '</p></div>' +
                        '<div class="tarjeta-caracteristica"><p class="label">Año</p><p class="font-semibold">' + v.anio + '</p></div>' +
                        '<div class="tarjeta-caracteristica"><p class="label">Transmisión</p><p class="font-semibold">' + v.transmision + '</p></div>' +
                        '<div class="tarjeta-caracteristica"><p class="label">Kilometraje</p><p class="font-semibold">' + v.kilometraje + '</p></div>' +
                        (v.color ? '<div class="tarjeta-caracteristica"><p class="label">Color</p><p class="font-semibold">' + v.color + '</p></div>' : '') +
                        '<div class="tarjeta-caracteristica col-span-2"><p class="label">Características Adicionales</p><p class="font-semibold">' + (v.caracteristicas_extra || 'Sin información adicional') + '</p></div>' +
                    '</div>' +
                    '<div class="space-y-4">' +
                        '<button onclick="abrirModalSolicitud(2)" class="btn-solicitar w-full font-bold py-4 rounded-lg font-montserrat">Solicitar Financiamiento</button>' +
                        '<button onclick="abrirModalSolicitud(1)" class="btn-cotizar w-full font-bold py-4 rounded-lg font-montserrat">Cotizar Vehículo</button>' +
                        '<a href="https://wa.me/18294649902?text=Hola,%20me%20interesa%20el%20' + encodeURIComponent(v.marca + ' ' + v.modelo + ' ' + v.anio) + '" target="_blank" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg font-montserrat text-center transition-all duration-300"><i class="fab fa-whatsapp mr-2"></i> Cotizar por WhatsApp</a>' +
                    '</div>' +
                '</div>';
        })
        .catch(error => {
            console.error(error);
            contentDiv.innerHTML = '<div class="col-span-full text-center py-20">' +
                '<i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>' +
                '<p style="color: var(--texto-secundario);">No se pudo cargar la información del vehículo.</p>' +
                '<a href="{{ route("inventario") }}" class="text-brand-red hover:underline mt-4 inline-block">Volver al inventario</a>' +
                '</div>';
        });
});

function cambiarImagen(src, elemento) {
    document.getElementById('imagen-principal').src = src;
    document.querySelectorAll('.miniatura').forEach(function(m) { m.classList.remove('activa'); });
    elemento.classList.add('activa');
}

function abrirModalSolicitud(tipo) {
    document.getElementById('solicitud-tipo').value = tipo;
    document.getElementById('modal-titulo').textContent = tipo === 1 ? 'SOLICITUD DE COTIZACIÓN' : 'SOLICITUD DE FINANCIAMIENTO';
    document.getElementById('btn-enviar-solicitud').innerHTML = '<span>Enviar ' + (tipo === 1 ? 'Cotización' : 'Financiamiento') + '</span>';

    document.getElementById('modal-solicitud').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = 'auto';
}

window.onclick = function(event) {
    if (event.target.id === 'modal-solicitud') cerrarModal('modal-solicitud');
};

document.getElementById('form-solicitud').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('btn-enviar-solicitud');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
    btn.disabled = true;

    const fechaRaw = document.getElementById('fecha_nacimiento').value;
    const fechaFmt = fechaRaw ? fechaRaw.replace(/-/g, '') : '';
    const tipoSolicitud = document.getElementById('solicitud-tipo').value;

    const formData = new FormData(this);
    const data = {
        tipo_solicitud: tipoSolicitud,
        vehiculo_id: document.getElementById('solicitud-vehiculo-id').value,
        monto: document.getElementById('solicitud-monto').value,
        tipo_prestamo: 1,
        nombres: formData.get('nombres'),
        apellidos: formData.get('apellidos'),
        apodo: formData.get('apodo') || '',
        cedula: formData.get('cedula'),
        genero: formData.get('genero'),
        estado_civil: formData.get('estado_civil'),
        fecha_nacimiento: fechaFmt,
        direccion: formData.get('direccion'),
        ciudad: formData.get('ciudad'),
        provincia: formData.get('provincia'),
        celular: formData.get('celular'),
        tel_hogar: formData.get('tel_hogar') || '',
        tel_laboral: formData.get('tel_laboral') || '',
        email: formData.get('email'),
        empresa_ingreso: formData.get('empresa_ingreso'),
        ciclo_ingreso: formData.get('ciclo_ingreso'),
        monto_ingreso: formData.get('monto_ingreso'),
        cargo: formData.get('cargo'),
        supervisor: formData.get('supervisor')
    };

    fetch('{{ route("solicitar.cotizacion") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ ' + result.message);
            cerrarModal('modal-solicitud');
            document.getElementById('form-solicitud').reset();
        } else {
            alert('❌ Error: ' + (result.message || 'No se pudo procesar la solicitud.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error de conexión con el servidor.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
@endsection
