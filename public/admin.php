<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

// Cargar vehículos
$vehiculos = json_decode(file_get_contents('data/vehiculos.json'), true);
if (!$vehiculos) $vehiculos = [];

// Verificar si se está editando un vehículo
$editando = null;
if (isset($_GET['editar'])) {
    foreach ($vehiculos as $v) {
        if ($v['id'] == $_GET['editar']) {
            $editando = $v;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | MadeCars</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'brand-red': '#DC2626',
                        'brand-black': '#000000',
                    },
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-principal: #000000;
            --texto-principal: #ffffff;
            --bg-tarjeta: #000000;
            --borde-tarjeta: #ffffff;
        }
        html.light-mode {
            --bg-principal: #ffffff;
            --texto-principal: #1f2937;
            --bg-tarjeta: #ffffff;
            --borde-tarjeta: #e5e7eb;
        }
        body {
            background-color: var(--bg-principal);
            color: var(--texto-principal);
        }
        .border-gold { border-color: #DC2626 !important; }
        .bg-gold { background-color: #DC2626 !important; }
        .text-gold { color: #DC2626 !important; }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header -->
    <nav class="bg-brand-black border-b-2 border-brand-red sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white font-montserrat">
                <i class="fas fa-car mr-2 text-brand-red"></i>MADECARS ADMIN
            </h1>
            <div class="flex gap-4">
                <a href="/" class="text-gray-300 hover:text-brand-red transition">
                    <i class="fas fa-home mr-1"></i> Ver Sitio
                </a>
                <a href="admin_login.php?logout=1" onclick="sessionStorage.clear()" class="bg-brand-red hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-brand-black border border-white rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Total Vehículos</p>
                        <p class="text-3xl font-bold text-brand-red"><?php echo count($vehiculos); ?></p>
                    </div>
                    <i class="fas fa-car text-4xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <!-- Formulario para agregar/editar vehículo -->
        <div class="bg-brand-black border-2 border-brand-red rounded-2xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 font-montserrat">
                <i class="fas fa-plus-circle mr-2 text-brand-red"></i>
                <?php echo $editando ? 'Editar Vehículo' : 'Agregar Nuevo Vehículo'; ?>
            </h2>
            
            <form action="guardar_vehiculo.php" method="POST" enctype="multipart/form-data" id="form-vehiculo">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?php echo $editando['id']; ?>">
                    <input type="hidden" name="imagenes_mantenidas" id="imagenes_mantenidas" value="<?php echo htmlspecialchars(json_encode($editando['imagenes'] ?? [])); ?>">
                    <input type="hidden" name="portada_actual" id="portada_actual" value="<?php echo $editando['portada'] ?? ''; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Marca</label>
                        <input type="text" name="marca" placeholder="Ej: TOYOTA" required 
                            value="<?php echo $editando['marca'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Modelo</label>
                        <input type="text" name="modelo" placeholder="Ej: COROLLA SE" required 
                            value="<?php echo $editando['modelo'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Año</label>
                        <input type="number" name="anio" placeholder="2023" required 
                            value="<?php echo $editando['anio'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Transmisión</label>
                        <input type="text" name="transmision" placeholder="Automática" required 
                            value="<?php echo $editando['transmision'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Kilometraje</label>
                        <input type="text" name="kilometraje" placeholder="45,000" required 
                            value="<?php echo $editando['kilometraje'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Precio</label>
                        <input type="text" name="precio" placeholder="$ RD 1,150,000.00" required 
                            value="<?php echo $editando['precio'] ?? ''; ?>" 
                            class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red">
                    </div>
                </div>

                <!-- Características Adicionales -->
                <div class="mb-6">
                    <label class="block text-gray-400 text-sm mb-2">Características Adicionales</label>
                    <textarea name="caracteristicas_extra" rows="3" 
                        placeholder="Ej: 4x4, 7 plazas, full equipo, motor V8, Apple CarPlay..." 
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red"><?php echo $editando['caracteristicas_extra'] ?? ''; ?></textarea>
                </div>

                <!-- Selector de imágenes -->
                <div class="mb-6">
                    <label class="block text-gray-400 text-sm mb-2">Imágenes del vehículo</label>
                    
                    <?php if ($editando && !empty($editando['imagenes'])): ?>
                        <div class="mb-4">
                            <p class="text-gray-400 text-xs mb-2">Imágenes actuales (click para portada, X para eliminar):</p>
                            <div class="flex gap-2 flex-wrap" id="miniaturas-actuales">
                                <?php foreach ($editando['imagenes'] as $index => $img): ?>
                                    <div class="relative grupo-imagen" data-url="<?php echo $img; ?>" 
                                         onclick="seleccionarPortadaExistente('<?php echo $img; ?>', this)">
                                        <img src="<?php echo $img; ?>" 
                                            class="w-24 h-20 object-cover rounded-lg cursor-pointer border-2 <?php echo ($editando['portada'] == $img) ? 'border-brand-red' : 'border-gray-700'; ?>">
                                        <?php if ($editando['portada'] == $img): ?>
                                            <span class="absolute top-0 right-0 bg-brand-red text-white text-xs px-2 py-1 rounded-bl-lg font-bold">Portada</span>
                                        <?php endif; ?>
                                        <button type="button" onclick="event.stopPropagation(); eliminarImagenExistente(this)" 
                                            class="absolute top-0 left-0 bg-red-600 text-white text-xs w-5 h-5 flex items-center justify-center rounded-br-lg hover:bg-red-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" name="imagenes[]" accept="image/*" multiple id="input-imagenes" 
                        class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3">
                    <div id="miniaturas-nuevas" class="flex gap-2 flex-wrap mt-4"></div>
                    <input type="hidden" name="portada_index" id="portada_index" value="0">
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-brand-red hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition font-montserrat">
                        <i class="fas fa-save mr-2"></i><?php echo $editando ? 'Actualizar Vehículo' : 'Guardar Vehículo'; ?>
                    </button>
                    <?php if ($editando): ?>
                        <a href="admin.php" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-lg transition font-montserrat">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Lista de vehículos existentes -->
        <h2 class="text-2xl font-bold text-white mb-6 font-montserrat">
            <i class="fas fa-list mr-2 text-brand-red"></i>Vehículos Registrados
        </h2>
        
        <?php if (empty($vehiculos)): ?>
            <div class="bg-brand-black border border-gray-700 rounded-xl p-8 text-center">
                <i class="fas fa-car text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400">No hay vehículos registrados aún.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($vehiculos as $v): ?>
                    <div class="bg-brand-black border border-white rounded-xl overflow-hidden hover:border-brand-red transition">
                        <img src="<?php echo $v['portada'] ?? ($v['imagenes'][0] ?? ''); ?>" 
                            class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-white text-lg"><?php echo $v['marca'] . " " . $v['modelo']; ?></h3>
                            <p class="text-gray-400 text-sm"><?php echo $v['anio']; ?> | <?php echo $v['transmision']; ?> | <?php echo $v['kilometraje']; ?> km</p>
                            <p class="text-brand-red font-bold text-xl mt-2"><?php echo $v['precio']; ?></p>
                            <div class="flex gap-2 mt-4">
                                <a href="admin.php?editar=<?php echo $v['id']; ?>" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex-1 text-center transition">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                <a href="eliminar_vehiculo.php?id=<?php echo $v['id']; ?>" 
                                    onclick="return confirm('¿Seguro que deseas eliminar este vehículo?')" 
                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm flex-1 text-center transition">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Variables globales para imágenes
    let imagenesExistentes = <?php echo json_encode($editando['imagenes'] ?? []); ?>;
    let portadaExistente = '<?php echo $editando['portada'] ?? ''; ?>';

    function seleccionarPortadaExistente(url, elemento) {
        document.querySelectorAll('#miniaturas-actuales .grupo-imagen').forEach(el => {
            el.querySelector('img').classList.remove('border-brand-red');
            el.querySelector('img').classList.add('border-gray-700');
            const span = el.querySelector('span');
            if (span && span.textContent === 'Portada') span.remove();
        });
        elemento.querySelector('img').classList.remove('border-gray-700');
        elemento.querySelector('img').classList.add('border-brand-red');
        const span = document.createElement('span');
        span.className = 'absolute top-0 right-0 bg-brand-red text-white text-xs px-2 py-1 rounded-bl-lg font-bold';
        span.textContent = 'Portada';
        elemento.appendChild(span);
        portadaExistente = url;
        document.getElementById('portada_actual').value = url;
    }

    function eliminarImagenExistente(boton) {
        const div = boton.parentElement;
        const url = div.dataset.url;
        if (confirm('¿Eliminar esta imagen?')) {
            imagenesExistentes = imagenesExistentes.filter(img => img !== url);
            if (portadaExistente === url) {
                portadaExistente = '';
                document.getElementById('portada_actual').value = '';
            }
            document.getElementById('imagenes_mantenidas').value = JSON.stringify(imagenesExistentes);
            div.remove();
        }
    }

    function seleccionarPortadaNueva(index, elemento) {
        document.querySelectorAll('#miniaturas-nuevas .grupo-imagen').forEach(el => {
            el.querySelector('img').classList.remove('border-brand-red');
            el.querySelector('img').classList.add('border-gray-700');
            const span = el.querySelector('span');
            if (span && span.textContent === 'Portada') span.remove();
        });
        elemento.querySelector('img').classList.remove('border-gray-700');
        elemento.querySelector('img').classList.add('border-brand-red');
        const span = document.createElement('span');
        span.className = 'absolute top-0 right-0 bg-brand-red text-white text-xs px-2 py-1 rounded-bl-lg font-bold';
        span.textContent = 'Portada';
        elemento.appendChild(span);
        document.getElementById('portada_index').value = index;
    }

    document.getElementById('input-imagenes').addEventListener('change', function(e) {
        const contenedor = document.getElementById('miniaturas-nuevas');
        contenedor.innerHTML = '';
        const archivos = e.target.files;
        for (let i = 0; i < archivos.length; i++) {
            const archivo = archivos[i];
            const reader = new FileReader();
            reader.onload = function(event) {
                const div = document.createElement('div');
                div.className = 'relative grupo-imagen';
                div.onclick = function() { seleccionarPortadaNueva(i, div); };
                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'w-24 h-20 object-cover rounded-lg cursor-pointer border-2 border-gray-700';
                div.appendChild(img);
                if (i === 0) div.click();
                contenedor.appendChild(div);
            };
            reader.readAsDataURL(archivo);
        }
    });

    // Logout
    if (window.location.href.includes('logout=1')) {
        sessionStorage.clear();
        window.location.href = 'admin_login.php';
    }
    </script>
</body>
</html>