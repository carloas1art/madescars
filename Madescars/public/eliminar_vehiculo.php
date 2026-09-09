<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: admin_login.php');
    exit;
}

if (isset($_GET['id'])) {
    $vehiculos = json_decode(file_get_contents('data/vehiculos.json'), true);
    
    foreach ($vehiculos as $index => $v) {
        if ($v['id'] == $_GET['id']) {
            // Eliminar todas las imágenes (portada + secundarias)
            $imagenes = array_merge(
                isset($v['imagenes']) ? $v['imagenes'] : [],
                isset($v['portada']) ? [$v['portada']] : []
            );
            
            foreach ($imagenes as $img) {
                if (file_exists($img)) {
                    unlink($img);
                }
            }
            
            unset($vehiculos[$index]);
            break;
        }
    }
    
    // Reindexar array
    $vehiculos = array_values($vehiculos);
    
    file_put_contents('data/vehiculos.json', json_encode($vehiculos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

header('Location: admin.php');
exit;
?>