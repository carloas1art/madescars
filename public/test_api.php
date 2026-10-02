<?php
// public/test_api.php

$apiId = 'B7E4J7B5U6DIHTTR3K5D6UIODFTR3FEY';       // <-- REEMPLAZAR
$pedido = 67;               // Pedido de inventario
$auth = 'Ufkh21Sfkrc534Q5';      // <-- REEMPLAZAR
$empId = 0;
$argumentos = "page=1▌pageSize=20";

$oDatos = "{$apiId}|{$pedido}|{$auth}|{$empId}|{$argumentos}";
$url = "https://webrapi.finsoftek.com/?oDatos=" . urlencode($oDatos);

echo "<h2>1. URL Generada</h2>";
echo "<p style='word-break:break-all;'>" . htmlspecialchars($url) . "</p>";

echo "<h2>2. Respuesta de la API</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo para pruebas locales

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p style='color:red;'>Error de CURL: " . htmlspecialchars($error) . "</p>";
} else {
    echo "<pre style='background:#f4f4f4; padding:10px; max-height:300px; overflow:auto;'>" . htmlspecialchars($response) . "</pre>";

    echo "<h2>3. Análisis de lblRespuesta</h2>";
    if (preg_match('/<span id="lblRespuesta">(.*?)<\/span>/s', $response, $matches)) {
        $lblContent = trim($matches[1]);
        echo "<p><strong>Contenido encontrado:</strong> " . htmlspecialchars($lblContent) . "</p>";

        if (strpos($lblContent, '§') !== false) {
            $partes = explode('§', $lblContent, 2);
            echo "<p><strong>Valor (Código):</strong> " . htmlspecialchars(trim($partes[0])) . "</p>";
            echo "<p><strong>Datos (JSON):</strong></p>";
            echo "<pre style='background:#f4f4f4; padding:10px; max-height:400px; overflow:auto;'>" . htmlspecialchars(trim($partes[1])) . "</pre>";

            // Intentar decodificar para verificar que sea JSON válido
            $json = json_decode(trim($partes[1]), true);
            if ($json) {
                echo "<p style='color:green;'><strong>✅ El JSON es válido y contiene " . count($json['mercancias'] ?? []) . " vehículos.</strong></p>";
            } else {
                echo "<p style='color:red;'><strong>❌ El JSON no es válido.</strong></p>";
            }
        } else {
            echo "<p style='color:orange;'><strong>⚠️ No se encontró el separador '§'. La API podría estar devolviendo un código de espera (ej. 10140) o un error.</strong></p>";
        }
    } else {
        echo "<p style='color:red;'><strong>❌ No se encontró la etiqueta &lt;span id=\"lblRespuesta\"&gt; en la respuesta.</strong></p>";
    }
}
?>
