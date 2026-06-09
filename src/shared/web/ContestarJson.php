<?php

namespace src\shared\web;

use Illuminate\Http\JsonResponse;

class ContestarJson
{

    /**
     * Envía JSON. Si `data` no es string, se serializa con `json_encode` (sin
     * JSON_FORCE_OBJECT) para que arrays sigan siendo `[]` en el payload.
     * Así el envelope sigue siendo `{ "data": "<string JSON>" }` y el cliente
     * puede seguir usando `JSON.parse(rta.data)` donde ya lo hace.
     *
     * @param array<string, mixed> $jsondata
     */
    public static function send(array $jsondata): void
    {
        if (array_key_exists('data', $jsondata) && !is_string($jsondata['data'])) {
            $jsondata['data'] = json_encode($jsondata['data']);
        }
        (new JsonResponse($jsondata))->send();
    }

    /**
     * Respuesta estándar `{success, mensaje?, data}`.
     * `data` estructurado (array) se expone como string JSON escapado en el
     * cuerpo (compatibilidad); `data` ya string (p. ej. `'ok'`) no se vuelve a codificar.
     * {@see \frontend\shared\PostRequest::getDataFromUrl} decodifica siempre a `array`
     * (p. ej. ack `'ok'` sin JSON interno → `[]`; ver `frontend/shared/PostRequest.php`).
     *
     * @param string|array<int|string, mixed> $data
     * @param int $httpStatusOnError Código HTTP si hay error (`$error_txt` no vacío). Por defecto 200 por compatibilidad.
     */
    public static function enviar(string $error_txt = '', string|array $data = 'ok', int $httpStatusOnError = 200): void
    {
        $jsondata = self::respuestaPhp($error_txt, $data);
        if (!is_string($jsondata['data'])) {
            $jsondata['data'] = json_encode($jsondata['data']);
        }
        $httpCode = $error_txt === '' ? 200 : $httpStatusOnError;
        (new JsonResponse($jsondata, $httpCode))->send();
    }

    /**
     * Igual que {@see enviar} pero `data` queda como objeto/array JSON anidado
     * (una sola codificación por byte en el mensaje HTTP). Para clientes nativos
     * (OkHttp+Moshi/Kotlin) que esperan `data.a_valores`, no una cadena que haya
     * que volver a parsear.
     *
     * No usar con {@see \frontend\shared\PostRequest::getData}: después del
     * `json_decode` del cuerpo hace otro sobre `data`, que debe ser string.
     *
     * @param string|array<string, mixed> $data
     */
    public static function enviarDataAnidado(string $error_txt = '', string|array $data = 'ok'): void
    {
        $jsondata = self::respuestaPhp($error_txt, $data);
        (new JsonResponse($jsondata))->send();
    }

    /**
     * forma un array con las claves 'success', 'mensaje' y 'data' sin codificar!
     *
     * @param string|array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function respuestaPhp(string $error_txt = '', string|array $data = 'ok'): array
    {
        if (!empty($error_txt)) {
            $jsondata['success'] = false;
            $jsondata['mensaje'] = $error_txt;
            $jsondata['data'] = $data;
        } else {
            $jsondata['success'] = true;
            $jsondata['data'] = $data;
        }

        return $jsondata;
    }
}
