<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

// Cargar vehículos
$vehiculos = json_decode(file_get_contents('data/vehiculos.json'), true);

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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin | MadeCars</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="css/inventario.css">
</head>
<body class="bg-black text-white p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gold mb-6">Panel de Administración</h1>
        
        <!-- Formulario para agregar/editar vehículo -->
        <div class="bg-gray-900 p-6 rounded-2xl border border-gray-700 mb-8">
            <h2 class="text-xl font-bold mb-4"><?php echo $editando ? 'Editar Vehículo' : 'Agregar Nuevo Vehículo'; ?></h2>
            <form action="guardar_vehiculo.php" method="POST" enctype="multipart/form-data" id="form-vehiculo">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?php echo $editando['id']; ?>">
                    <!-- Campo para guardar las imágenes que se mantienen -->
                    <input type="hidden" name="imagenes_mantenidas" id="imagenes_mantenidas" value="<?php echo htmlspecialchars(json_encode($editando['imagenes'] ?? [])); ?>">
                    <!-- Campo para guardar la portada actual -->
                    <input type="hidden" name="portada_actual" id="portada_actual" value="<?php echo $editando['portada'] ?? ''; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="marca" placeholder="Marca" required value="<?php echo $editando['marca'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    <input type="text" name="modelo" placeholder="Modelo" required value="<?php echo $editando['modelo'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    <input type="number" name="anio" placeholder="Año" required value="<?php echo $editando['anio'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    <input type="text" name="transmision" placeholder="Transmisión" required value="<?php echo $editando['transmision'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    <input type="text" name="kilometraje" placeholder="Kilometraje" required value="<?php echo $editando['kilometraje'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    
                    <!-- NUEVO CAMPO: Características Adicionales -->
                    <div class="col-span-2">
                        <label class="text-gray-400 text-sm block mb-1">Características Adicionales</label>
                        <textarea name="caracteristicas_extra" rows="3" placeholder="Ej: 4x4, 7 plazas, full equipo, motor V8..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2"><?php echo $editando['caracteristicas_extra'] ?? ''; ?></textarea>
                    </div>
                    
                    <input type="text" name="precio" placeholder="Precio (ej: REF 50.000)" required value="<?php echo $editando['precio'] ?? ''; ?>" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
                    
                    <!-- Selector de imágenes -->
                    <div class="col-span-2">
                        <label class="text-gray-400 text-sm block mb-2">Imágenes del vehículo</label>
                        
                        <!-- Miniaturas existentes (solo en edición) -->
                        <?php if ($editando && !empty($editando['imagenes'])): ?>
                            <div class="mb-4">
                                <p class="text-gray-400 text-xs mb-2">Imágenes actuales (haz clic para seleccionar portada, X para eliminar):</p>
                                <div class="flex gap-2 flex-wrap" id="miniaturas-actuales">
                                    <?php foreach ($editando['imagenes'] as $index => $img): ?>
                                        <div class="relative grupo-imagen" data-url="<?php echo $img; ?>" onclick="seleccionarPortadaExistente('<?php echo $img; ?>', this)">
                                            <img src="<?php echo $img; ?>" class="w-24 h-20 object-cover rounded-lg cursor-pointer border-2 <?php echo ($editando['portada'] == $img) ? 'border-gold' : 'border-gray-700'; ?>">
                                            <?php if ($editando['portada'] == $img): ?>
                                                <span class="absolute top-0 right-0 bg-gold text-black text-xs px-2 py-1 rounded-bl-lg font-bold">Portada</span>
                                            <?php endif; ?>
                                            <!-- Botón para eliminar esta imagen -->
                                            <button type="button" onclick="event.stopPropagation(); eliminarImagenExistente(this)" class="absolute top-0 left-0 bg-red-600 text-white text-xs w-5 h-5 flex items-center justify-center rounded-br-lg hover:bg-red-500">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Subir nuevas imágenes -->
                        <input type="file" name="imagenes[]" accept="image/*" multiple id="input-imagenes" class="w-full text-gray-400">
                        
                        <!-- Miniaturas de nuevas imágenes -->
                        <div id="miniaturas-nuevas" class="flex gap-2 flex-wrap mt-4"></div>
                        
                        <!-- Campo oculto para el índice de portada de nuevas imágenes -->
                        <input type="hidden" name="portada_index" id="portada_index" value="0">
                    </div>
                </div>
                
                <button type="submit" class="mt-4 bg-gold hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg"><?php echo $editando ? 'Actualizar Vehículo' : 'Guardar Vehículo'; ?></button>
            </form>
        </div>

        <!-- Lista de vehículos existentes -->
        <h2 class="text-2xl font-bold mb-4">Vehículos Registrados (<?php echo count($vehiculos); ?>)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($vehiculos as $v): ?>
            <div class="bg-gray-900 p-4 rounded-xl border border-gray-700">
                <img src="<?php echo $v['portada'] ?? $v['imagenes'][0] ?? ''; ?>" class="w-full h-40 object-cover rounded-lg mb-3">
                <h3 class="font-bold"><?php echo $v['marca'] . " " . $v['modelo']; ?></h3>
                <p class="text-gray-400 text-sm"><?php echo $v['anio']; ?> | <?php echo $v['transmision']; ?></p>
                <p class="text-gold font-bold mt-2"><?php echo $v['precio']; ?></p>
                <div class="flex gap-2 mt-4">
                    <a href="admin.php?editar=<?php echo $v['id']; ?>" class="bg-blue-600 hover:bg-blue-500 text-white py-1 px-3 rounded-lg text-sm">Editar</a>
                    <a href="eliminar_vehiculo.php?id=<?php echo $v['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este vehículo?')" class="bg-red-600 hover:bg-red-500 text-white py-1 px-3 rounded-lg text-sm">Eliminar</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- JavaScript para manejar imágenes -->
    <script>
        // Variables globales para imágenes
        let imagenesExistentes = <?php echo json_encode($editando['imagenes'] ?? []); ?>;
        let portadaExistente = '<?php echo $editando['portada'] ?? ''; ?>';
        let nuevasImagenes = [];

        // Seleccionar portada entre imágenes existentes
        function seleccionarPortadaExistente(url, elemento) {
            document.querySelectorAll('#miniaturas-actuales .grupo-imagen').forEach(el => {
                el.querySelector('img').classList.remove('border-gold');
                el.querySelector('img').classList.add('border-gray-700');
                const span = el.querySelector('span');
                if (span && span.textContent === 'Portada') span.remove();
            });
            
            elemento.querySelector('img').classList.remove('border-gray-700');
            elemento.querySelector('img').classList.add('border-gold');
            
            const span = document.createElement('span');
            span.className = 'absolute top-0 right-0 bg-gold text-black text-xs px-2 py-1 rounded-bl-lg font-bold';
            span.textContent = 'Portada';
            elemento.appendChild(span);
            
            portadaExistente = url;
            document.getElementById('portada_actual').value = url;
        }

        // Eliminar imagen existente
        function eliminarImagenExistente(boton) {
            const div = boton.parentElement;
            const url = div.dataset.url;
            
            if (confirm('¿Eliminar esta imagen?')) {
                // Remover de la lista de imágenes existentes
                imagenesExistentes = imagenesExistentes.filter(img => img !== url);
                
                // Si la portada era esta imagen, limpiar portada
                if (portadaExistente === url) {
                    portadaExistente = '';
                    document.getElementById('portada_actual').value = '';
                }
                
                // Actualizar campo oculto
                document.getElementById('imagenes_mantenidas').value = JSON.stringify(imagenesExistentes);
                
                // Eliminar visualmente
                div.remove();
            }
        }

        // Seleccionar portada entre nuevas imágenes
        function seleccionarPortadaNueva(index, elemento) {
            document.querySelectorAll('#miniaturas-nuevas .grupo-imagen').forEach(el => {
                el.querySelector('img').classList.remove('border-gold');
                el.querySelector('img').classList.add('border-gray-700');
                const span = el.querySelector('span');
                if (span && span.textContent === 'Portada') span.remove();
            });
            
            elemento.querySelector('img').classList.remove('border-gray-700');
            elemento.querySelector('img').classList.add('border-gold');
            
            const span = document.createElement('span');
            span.className = 'absolute top-0 right-0 bg-gold text-black text-xs px-2 py-1 rounded-bl-lg font-bold';
            span.textContent = 'Portada';
            elemento.appendChild(span);
            
            document.getElementById('portada_index').value = index;
        }

        // Generar miniaturas al seleccionar archivos
        document.getElementById('input-imagenes').addEventListener('change', function(e) {
            const contenedor = document.getElementById('miniaturas-nuevas');
            contenedor.innerHTML = '';
            nuevasImagenes = [];
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
                    
                    // Si es la primera, seleccionarla automáticamente como portada
                    if (i === 0) {
                        div.click();
                    }
                    
                    contenedor.appendChild(div);
                };
                
                reader.readAsDataURL(archivo);
                nuevasImagenes.push(archivo);
            }
        });
    </script>
</body>
</html>