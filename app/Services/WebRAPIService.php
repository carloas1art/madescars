<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebRAPIService
{
    private string $url;
    private string $apiId;
    private string $auth;
    private int $empId;

    public function __construct()
    {
        // Leemos directamente de config/webrapi.php con valores por defecto a prueba de fallos
        $this->url = config('webrapi.url', 'https://webrapi.finsoftek.com');
        $this->apiId = config('webrapi.api_id', '');
        $this->auth = config('webrapi.auth_key', ''); // Coincide con webrapi.php
        $this->empId = (int) config('webrapi.emp_id', 0);
    }

     public function ejecutarPedido(int $pedido, string $argumentos = '', int $maxIntentos = 20, int $tiempoEspera = 2): array|string
    {
        $oDatos = "{$this->apiId}|{$pedido}|{$this->auth}|{$this->empId}|{$argumentos}";

        Log::info('WebRAPI - Enviando petición. oDatos: ' . $oDatos);

        for ($intento = 1; $intento <= $maxIntentos; $intento++) {
            try {
                $response = Http::timeout(30)->asForm()->post($this->url, [
                    'oDatos' => $oDatos,
                ]);

                $body = $response->body();

                Log::info("WebRAPI - Respuesta del intento {$intento}: " . substr($body, 0, 500));

                if (preg_match('/<span id="lblRespuesta">(.*?)<\/span>/s', $body, $matches)) {
                    return trim($matches[1]);
                }

                if ($response->successful()) {
                    return $body;
                }
            } catch (\Exception $e) {
                Log::error("WebRAPI - Excepción en ejecutarPedido intento {$intento}: " . $e->getMessage());
            }

            if ($intento < $maxIntentos) {
                sleep($tiempoEspera);
            }
        }

        return 'Error al ejecutar el pedido';
    }

    public function obtenerInventario(array $filtros = [], int $pagina = 1, int $pageSize = 20)
    {
        // Unimos los argumentos con el separador correcto
        $argumentos = "page={$pagina}|pageSize={$pageSize}";

        $oDatos = "{$this->apiId}|67|{$this->auth}|{$this->empId}|{$argumentos}";

        Log::info("WebRAPI - Enviando petición. oDatos: " . $oDatos);

        try {
            $response = Http::timeout(30)->get($this->url, [
                'oDatos' => $oDatos
            ]);

            $rawBody = $response->body();
            Log::info("WebRAPI - Respuesta cruda (primeros 500 chars): " . substr($rawBody, 0, 500));

            if (preg_match('/<span id="lblRespuesta">(.*?)<\/span>/s', $rawBody, $matches)) {
                $lblContent = trim($matches[1]);
                Log::info("WebRAPI - Contenido de lblRespuesta: " . $lblContent);

                if (strpos($lblContent, '§') !== false) {
                    $partes = explode('§', $lblContent, 2);
                    $valor = trim($partes[0]);
                    $datos = isset($partes[1]) ? trim($partes[1]) : '';

                    Log::info("WebRAPI - Valor: '{$valor}', Longitud de datos: " . strlen($datos));

                    if (is_numeric($valor)) {
                        $decoded = json_decode($datos, true);
                        if ($decoded && isset($decoded['mercancias'])) {
                            Log::info("WebRAPI - Éxito: Se encontraron " . count($decoded['mercancias']) . " vehículos.");
                            return $this->formatearVehiculos($decoded['mercancias']);
                        } else {
                            Log::error("WebRAPI - Error al decodificar JSON o falta la clave 'mercancias'. Datos: " . $datos);
                        }
                    } else {
                        Log::warning("WebRAPI - La API devolvió un valor no numérico o de espera: '{$valor}'");
                    }
                } else {
                    Log::warning("WebRAPI - La respuesta no contiene el separador '§'. Contenido: " . $lblContent);
                }
            } else {
                Log::error("WebRAPI - No se encontró el span <span id=\"lblRespuesta\"> en la respuesta.");
            }

        } catch (\Exception $e) {
            Log::error("WebRAPI - Excepción en la petición: " . $e->getMessage());
        }

        return [];
    }

    private function formatearVehiculos(array $mercancias)
    {
        $vehiculos = [];
        foreach ($mercancias as $v) {
            $vehiculos[] = [
                'id' => $v['id'] ?? '',
                'marca' => trim($v['marca'] ?? ''),
                'modelo' => trim($v['modelo'] ?? ''),
                'anio' => trim($v['year'] ?? $v['anio'] ?? ''),
                'transmision' => trim($v['etiquetas']['etiqueta7'] ?? 'N/A'),
                'kilometraje' => trim($v['kilometraje'] ?? '0'),
                'precio' => '$' . number_format((float)($v['precio'] ?? 0), 0, ',', ','),
                'portada' => !empty($v['imagenes'][0]) ? $v['imagenes'][0] : null,
            ];
        }
        return $vehiculos;
    }
}
