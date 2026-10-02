<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VehiculoController extends Controller
{
    private const MAX_RETRIES = 25;
    private const RETRY_INTERVAL = 2;
    private const CACHE_KEY_PREFIX = 'webrapi_inventario_page_';
    private const CACHE_DURATION = 600; // 10 minutos

    // =========================================================================
    // MÉTODOS NUEVOS: Comunicación con la API Local (WebRServer)
    // =========================================================================

        /**
     * Obtiene un token JWT de la API local y lo guarda en caché por 14 minutos.
     */
    private function obtenerTokenLocal(): ?string
    {
        $cacheKey = 'webrapi_local_token';

        // Si ya hay un token válido en caché, reutilizarlo
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // ==========================================
        // LOGS DE DEPURACIÓN AGREGADOS AQUÍ
        // ==========================================
        $apiUrl = config('webrapi.api_local_url');
        $apiKey = config('webrapi.api_local_key');

        Log::info('WebRAPI - DEBUG obtenerTokenLocal', [
            'api_url_leida' => $apiUrl,
            'api_key_leida' => $apiKey,
            'usar_api_local_config' => config('webrapi.usar_api_local', false)
        ]);
        // ==========================================

        // Si no, solicitar uno nuevo a la API de .NET
        try {
            $response = Http::post($apiUrl . '/api/auth/token', [
                'api_key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Guardar en caché por 14 minutos (840 segundos), 1 min menos que la expiración del servidor
                Cache::put($cacheKey, $data['token'], 840);
                return $data['token'];
            }

            Log::warning('WebRAPI - Token no exitoso', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('WebRAPI - Error obteniendo token API Local', ['error' => $e->getMessage()]);
            return null;
        }
    }

        /**
     * Realiza una petición GET segura a la API local usando el token JWT.
     */
    private function peticionLocalSegura(string $endpoint, array $params = []): ?array
    {
        if (!config('webrapi.usar_api_local', false)) {
            Log::info('WebRAPI - API Local deshabilitada en config');
            return null;
        }

        $token = $this->obtenerTokenLocal();
        if (!$token) {
            Log::warning('WebRAPI - No se pudo obtener token, abortando petición local');
            return null;
        }

        try {
            $baseUrl = config('webrapi.api_local_url');
            $url = $baseUrl . $endpoint;

            // ==========================================
            // LOGS DE DEPURACIÓN AGREGADOS AQUÍ
            // ==========================================
            Log::info('WebRAPI - DEBUG peticionLocalSegura', [
                'endpoint' => $endpoint,
                'params' => $params,
                'url_completa' => $url,
                'token_preview' => substr($token, 0, 15) . '...' // Solo mostramos los primeros 15 chars por seguridad
            ]);
            // ==========================================

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ])
                ->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            // Si el token expiró (401 Unauthorized), limpiar caché y reintentar una vez automáticamente
            if ($response->status() === 401) {
                Log::warning('WebRAPI - Token expirado (401), reintentando...');
                Cache::forget('webrapi_local_token');
                return $this->peticionLocalSegura($endpoint, $params);
            }

            Log::warning('WebRAPI - Petición local fallida', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('WebRAPI - Excepción en petición API Local', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // =========================================================================
    // MÉTODOS EXISTENTES: WebRAPI (Fallback)
    // =========================================================================

    private function construirODatos(int $pedido, string $argumentos = ''): string
    {
        $apiId = trim(config('webrapi.api_id'));
        $auth = trim(config('webrapi.auth_key'));
        $empId = config('webrapi.emp_id', 0);

        Log::info('WebRAPI - construirODatos INICIO', [
            'pedido' => $pedido,
            'argumentos_recibidos' => $argumentos,
            'apiId_length' => strlen($apiId)
        ]);

        if (strlen($apiId) === 32) {
            $argsLimpio = str_replace('|', '▌', $argumentos);
            $oDatosLimpio = $apiId . '|' . $pedido . '|' . $auth . '|' . $empId . '|' . $argsLimpio;
            Log::info('WebRAPI - construirODatos RESULTADO (LIMPIO, 32 chars)', [
                'oDatos_final' => $oDatosLimpio
            ]);
            return $oDatosLimpio;
        }

        $argsTerms = explode('|', config('webrapi.arguments_terms', ''));
        $htmlTerms = explode('|', config('webrapi.html_terms', ''));

        $getRandomTerm = function($array) {
            return $array[array_rand($array)];
        };

        $modifiedApiId = '';
        $apiIdStr = (string)$apiId;
        for ($i = 0; $i < strlen($apiIdStr) - 1; $i++) {
            $modifiedApiId .= $apiIdStr[$i] . $getRandomTerm($argsTerms);
        }
        $modifiedApiId .= $apiIdStr[strlen($apiIdStr) - 1];

        $modifiedArgs = '';
        if (!empty($argumentos)) {
            $argsArray = explode('|', $argumentos);
            for ($i = 0; $i < count($argsArray) - 1; $i++) {
                $modifiedArgs .= $argsArray[$i] . $getRandomTerm($argsTerms);
            }
            $modifiedArgs .= $argsArray[count($argsArray) - 1];
        }

        $oDatosFinal = $modifiedApiId . $getRandomTerm($htmlTerms) .
               $pedido . $getRandomTerm($htmlTerms) .
               $auth . $getRandomTerm($htmlTerms) .
               $empId . $getRandomTerm($htmlTerms) .
               $modifiedArgs;

        Log::info('WebRAPI - construirODatos RESULTADO (OFUSCADO)', [
            'oDatos_final' => $oDatosFinal,
            'modifiedArgs' => $modifiedArgs
        ]);

        return $oDatosFinal;
    }

    private function ejecutarConPolling(int $pedidoInicial, string $argumentos = ''): ?array
    {
        $pedidoActual = $pedidoInicial;
        $intentos = 0;

        Log::info('WebRAPI - ejecutarConPolling INICIO', [
            'pedidoInicial' => $pedidoInicial,
            'argumentos' => $argumentos
        ]);

        while ($intentos < self::MAX_RETRIES) {
            $intentos++;
            if ($intentos > 1) {
                sleep(self::RETRY_INTERVAL);
            }

            try {
                $oDatos = $this->construirODatos($pedidoActual, $argumentos);
                $url = "https://webrapi.finsoftek.com/?oDatos=" . urlencode($oDatos);

                Log::info('WebRAPI - Petición enviada', [
                    'intento' => $intentos,
                    'pedidoActual' => $pedidoActual,
                    'oDatos' => $oDatos
                ]);

                $response = Http::timeout(30)->get($url);
                $html = $response->body();

                if (preg_match('/<span id="lblRespuesta">(.*?)<\/span>/s', $html, $matches)) {
                    $respuesta = trim($matches[1]);
                } else {
                    $respuesta = trim($html);
                }

                Log::info('WebRAPI - Respuesta recibida', [
                    'intento' => $intentos,
                    'respuesta_preview' => substr($respuesta, 0, 200)
                ]);

                if (str_contains($respuesta, '§')) {
                    return $this->procesarResultado($respuesta);
                }

                $pedidoActual = (int) $respuesta;
            } catch (\Exception $e) {
                Log::error('WebRAPI - Excepción en polling', ['error' => $e->getMessage()]);
                return null;
            }
        }

        Log::error('WebRAPI - Timeout en polling', ['intentos' => $intentos]);
        return null;
    }

    private function procesarResultado(string $respuesta): ?array
    {
        $partes = explode('§', $respuesta, 2);
        $codigo = trim($partes[0]);
        $datos = isset($partes[1]) ? trim($partes[1]) : '';

        if (stripos($codigo, 'error') !== false || stripos($codigo, 'excepcion') !== false) {
            Log::error('WebRAPI - Excepción del servidor', ['mensaje' => $datos]);
            return null;
        }

        $datos = preg_replace('/(?<!\\\\)\\r?\\n/', '', $datos);

        $data = json_decode($datos, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('WebRAPI - Error al decodificar JSON', [
                'error' => json_last_error_msg(),
                'longitud_datos' => strlen($datos),
                'final_datos' => substr($datos, -150)
            ]);
            return null;
        }

        return $data;
    }

    // =========================================================================
    // CONTROLADORES DE VISTAS Y DATOS
    // =========================================================================

    public function inventario()
    {
        return view('inventario');
    }

    public function fetchInventarioData(Request $request)
    {
        $pagina = max(1, (int) $request->input('page', 1));
        $pageSize = config('webrapi.page_size_inventario', 6);
        $forzarRefresco = $request->has('refresh');
        $cacheKey = self::CACHE_KEY_PREFIX . $pagina;

        Log::info('WebRAPI - fetchInventarioData INICIO', [
            'pagina' => $pagina,
            'pageSize' => $pageSize,
            'forzarRefresco' => $forzarRefresco,
            'cacheKey' => $cacheKey
        ]);

        if (!$forzarRefresco && Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            Log::info('WebRAPI - Inventario servido desde CACHE', [
                'pagina' => $pagina,
                'cantidad' => isset($data['mercancias']) ? count($data['mercancias']) : 0
            ]);
        } else {
            // 1. Intentar primero con la API Local (mucho más rápido)
            $data = $this->peticionLocalSegura('/api/mercancia/inventario', [
                'page' => $pagina,
                'pageSize' => $pageSize
            ]);

            // 2. Fallback a WebRAPI si la API local falla o está desactivada
            if (!$data) {
                Log::info('WebRAPI - Fallback a WebRAPI para inventario (API Local no disponible)');
                $pedidoInicial = config('webrapi.pedido_inventario', 70);
                $argumentos = "page={$pagina}|pageSize={$pageSize}";
                $data = $this->ejecutarConPolling($pedidoInicial, $argumentos);
            }

            if ($data) {
                Cache::put($cacheKey, $data, self::CACHE_DURATION);
                Log::info('WebRAPI - Inventario guardado en CACHE', [
                    'pagina' => $pagina,
                    'cantidad' => isset($data['mercancias']) ? count($data['mercancias']) : 0
                ]);
            }
        }

        if (!$data || !isset($data['mercancias']) || !is_array($data['mercancias'])) {
            return response()->json([
                'vehiculos' => [],
                'total' => 0,
                'pagina' => $pagina,
                'paginas' => 0,
                'pageSize' => $pageSize
            ]);
        }

        $vehiculos = [];
        foreach ($data['mercancias'] as $v) {
            $simboloMoneda = 'RD$';
            $vehiculos[] = [
                'id' => $v['id'] ?? '',
                'marca' => trim($v['marca'] ?? ''),
                'modelo' => trim($v['modelo'] ?? ''),
                'anio' => trim($v['year'] ?? $v['anio'] ?? ''), // Soporta ambos formatos
                'transmision' => trim($v['transmision'] ?? $v['etiquetas']['etiqueta7'] ?? 'N/A'), // Soporta ambos formatos
                'kilometraje' => number_format((int)($v['kilometraje'] ?? 0), 0, ',', ','),
                'precio' =>$simboloMoneda . ' ' . number_format((float)($v['precio'] ?? 0), 0, ',', ','),
                // La API local manda 'portada' directo, WebRAPI manda 'imagenes[0]'
                'portada' => !empty($v['portada']) ? $v['portada'] : (!empty($v['imagenes'][0]) ? $v['imagenes'][0] : null),
            ];
        }

        Log::info('WebRAPI - fetchInventarioData RESULTADO', [
            'pagina' => $pagina,
            'cantidad_vehiculos' => count($vehiculos),
            'total' => $data['total'] ?? 0,
            'paginas' => $data['paginas'] ?? 0
        ]);

        return response()->json([
            'vehiculos' => $vehiculos,
            'total' => $data['total'] ?? 0,
            'pagina' => $data['pagina'] ?? $pagina,
            'paginas' => $data['paginas'] ?? 0,
            'pageSize' => $pageSize
        ]);
    }

    public function detalle($id)
    {
        return view('detalle', ['id' => $id]);
    }

    public function fetchDetalleData($id)
    {
        Log::info('WebRAPI - fetchDetalleData INICIO', ['id' => $id]);

        // 1. Intentar primero con la API Local
        $data = $this->peticionLocalSegura('/api/mercancia/detalle/' . $id);

        // 2. Fallback a WebRAPI si la API local falla
        if (!$data) {
            Log::info('WebRAPI - Fallback a WebRAPI para detalle ID: ' . $id);
            $pedidoDetalle = config('webrapi.pedido_detalle', 71);
            $argumentos = "mercanciaid={$id}";
            $data = $this->ejecutarConPolling($pedidoDetalle, $argumentos);
        }

        if (!$data) {
            Log::error('WebRAPI - No se recibió respuesta para detalle', ['id' => $id]);
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        if (!isset($data['encontrado']) || !$data['encontrado'] || !isset($data['mercancia'])) {
            Log::warning('WebRAPI - Vehículo no encontrado en respuesta', ['id' => $id]);
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $v = $data['mercancia'];
        $etiquetas = $v['etiquetas'] ?? [];
        $simboloMoneda = 'RD$';
        $vehiculo = [
            'id' => $v['id'] ?? '',
            'marca' => trim($v['marca'] ?? ''),
            'modelo' => trim($v['modelo'] ?? ''),
            'anio' => trim($v['year'] ?? $v['anio'] ?? ''),
            'color' => trim($v['color'] ?? ''),
            'condicion' => $v['condicion'] ?? 0,
            // Soporta 'transmision' directo (API Local) o 'etiquetas.etiqueta7' (WebRAPI)
            'transmision' => trim($v['transmision'] ?? $etiquetas['etiqueta7'] ?? 'N/A'),
            'kilometraje' => number_format((int)($v['kilometraje'] ?? 0), 0, ',', ','),
            'precio' =>$simboloMoneda . ' ' . number_format((float)($v['precio'] ?? 0), 0, ',', ','),
            'descripcion' => trim($v['descripcion'] ?? ''),
            'chasis' => trim($v['chasis'] ?? ''),
            'moneda' => trim($v['moneda'] ?? ''),
            'portada' => !empty($v['imagenes'][0]) ? $v['imagenes'][0] : null,
            'imagenes' => $v['imagenes'] ?? [],
            'caracteristicas_extra' => !empty($v['caracteristicas_extra'])
                ? $v['caracteristicas_extra']
                : (implode(', ', array_filter([
                    $etiquetas['etiqueta1'] ?? '',
                    $etiquetas['etiqueta6'] ?? '',
                    $etiquetas['etiqueta8'] ?? ''
                ])) ?: 'Sin información adicional')
        ];

        Log::info('WebRAPI - fetchDetalleData RESULTADO', [
            'id' => $id,
            'marca' => $vehiculo['marca'],
            'modelo' => $vehiculo['modelo'],
            'cantidad_imagenes' => count($vehiculo['imagenes'])
        ]);

        return response()->json(['vehiculo' => $vehiculo]);
    }
        public function solicitarCotizacion(Request $request)
{
    Log::info('WebRAPI - Solicitud Unificada INICIO', $request->all());

    try {
        // El orden es CRUCIAL. Los primeros 21 mantienen su índice original.
        // Los 2 nuevos se colocan al final (índices 21 y 22).
        $campos = [
            // --- 21 CAMPOS ORIGINALES (Índices 0 a 20) ---
            (int)$request->input('monto'),             // 0
            (int)$request->input('tipo_prestamo'),     // 1
            trim($request->input('nombres')),          // 2
            trim($request->input('apellidos')),        // 3
            trim($request->input('apodo')),            // 4
            trim($request->input('cedula')),           // 5
            (int)$request->input('genero'),            // 6
            (int)$request->input('estado_civil'),      // 7
            trim($request->input('fecha_nacimiento')), // 8
            trim($request->input('direccion')),        // 9
            trim($request->input('ciudad')),           // 10
            trim($request->input('provincia')),        // 11
            trim($request->input('celular')),          // 12
            trim($request->input('tel_hogar')),        // 13
            trim($request->input('tel_laboral')),      // 14
            trim($request->input('email')),            // 15
            trim($request->input('empresa_ingreso')),  // 16
            (int)$request->input('ciclo_ingreso'),     // 17
            (int)$request->input('monto_ingreso'),     // 18
            trim($request->input('cargo')),            // 19
            trim($request->input('supervisor')),       // 20

            // --- 2 CAMPOS NUEVOS AL FINAL (Índices 21 y 22) ---
            (int)$request->input('vehiculo_id'),       // 21: MercanciaID
            (int)$request->input('tipo_solicitud')     // 22: TipoSolicitud (1=Cotizar, 2=Financiar)
        ];

        $argumentos = implode('|', $campos);
        Log::info('WebRAPI - Argumentos generados (23 campos)', ['length' => count($campos)]);

        $pedidoInicial = 72;
        $data = $this->ejecutarConPolling($pedidoInicial, $argumentos);

        if ($data && isset($data['mensaje'])) {
            return response()->json(['success' => true, 'message' => $data['mensaje']]);
        }

        return response()->json(['success' => false, 'message' => 'Error en el servidor.'], 500);

    } catch (\Exception $e) {
        Log::error('WebRAPI - Excepción en Solicitud', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Error de conexión.'], 500);
    }
}
        public function solicitarFinanciamiento(Request $request)
{
    Log::info('WebRAPI - Solicitud de Financiamiento INICIO', $request->all());

    try {
        // Por ahora, solo registramos la solicitud en el log
        // En el futuro, podemos crear un nuevo pedido en WebRDB para financiamiento
        $data = [
            'vehiculo_id' => $request->input('vehiculo_id'),
            'vehiculo_nombre' => $request->input('vehiculo_nombre'),
            'nombre_completo' => trim($request->input('nombre_completo')),
            'cedula' => trim($request->input('cedula')),
            'telefono' => trim($request->input('telefono')),
            'email' => trim($request->input('email')),
            'mensaje' => trim($request->input('mensaje'))
        ];

        Log::info('WebRAPI - Datos de Financiamiento recibidos', $data);

        // TODO: En el futuro, crear un pedido en WebRDB para guardar en BD
        // Por ahora devolvemos éxito
        return response()->json([
            'success' => true,
            'message' => 'Su solicitud de financiamiento ha sido recibida. Un asesor se pondrá en contacto con usted pronto.'
        ]);

    } catch (\Exception $e) {
        Log::error('WebRAPI - Excepción en Solicitud de Financiamiento', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Ocurrió un error inesperado.'
        ], 500);
    }
}
}
