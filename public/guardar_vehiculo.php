<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

$vehiculos = json_decode(file_get_contents('data/vehiculos.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagenesFinales = [];
    $portada = '';
    
    // 1. Procesar imágenes existentes que se mantienen (al editar)
    if (isset($_POST['imagenes_mantenidas'])) {
        $imagenesMantenidas = json_decode($_POST['imagenes_mantenidas'], true);
        if (is_array($imagenesMantenidas)) {
            $imagenesFinales = $imagenesMantenidas;
        }
    }
    
    // 2. Obtener portada existente (al editar)
    if (isset($_POST['portada_actual']) && !empty($_POST['portada_actual'])) {
        $portada = $_POST['portada_actual'];
    }
    
    // 3. Procesar nuevas imágenes subidas
    if (isset($_FILES['imagenes'])) {
        $total = count($_FILES['imagenes']['name']);
        $indicePortadaNueva = isset($_POST['portada_index']) ? (int)$_POST['portada_index'] : 0;
        
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                $directorio = 'img/vehiculos/';
                $extension = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
                $nombreArchivo = 'vehiculo_' . time() . '_' . $i . '.' . $extension;
                move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $directorio . $nombreArchivo);
                $imagenesFinales[] = 'img/vehiculos/' . $nombreArchivo;
                
                // Si este es el índice marcado como portada de nuevas imágenes
                if ($i === $indicePortadaNueva) {
                    $portada = 'img/vehiculos/' . $nombreArchivo;
                }
            }
        }
    }
    
    // 4. Si no hay portada definida y hay imágenes, usar la primera
    if (empty($portada) && !empty($imagenesFinales)) {
        $portada = $imagenesFinales[0];
    }
    
    // 5. Generar ID (si es nuevo)
    if (isset($_POST['id'])) {
        $nuevoId = $_POST['id'];
    } else {
        $maxId = 0;
        foreach ($vehiculos as $v) {
            if ($v['id'] > $maxId) {
                $maxId = $v['id'];
            }
        }
        $nuevoId = $maxId + 1;
    }

    // 6. Crear o actualizar el vehículo
    $nuevoVehiculo = [
        'id' => $nuevoId,
        'marca' => $_POST['marca'],
        'modelo' => $_POST['modelo'],
        'anio' => $_POST['anio'],
        'transmision' => $_POST['transmision'],
        'kilometraje' => $_POST['kilometraje'],
        'precio' => $_POST['precio'],
        'portada' => $portada,
        'imagenes' => $imagenesFinales,
        // NUEVO CAMPO
        'caracteristicas_extra' => $_POST['caracteristicas_extra'] ?? ''
    ];

    // Si es edición, reemplazar el vehículo existente
    if (isset($_POST['id'])) {
        foreach ($vehiculos as $index => $v) {
            if ($v['id'] == $_POST['id']) {
                $vehiculos[$index] = $nuevoVehiculo;
                break;
            }
        }
    } else {
        $vehiculos[] = $nuevoVehiculo;
    }

    file_put_contents('data/vehiculos.json', json_encode($vehiculos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    header('Location: admin.php');
    exit;
}
?>