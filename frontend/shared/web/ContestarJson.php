<?php

namespace frontend\shared\web;

use Illuminate\Http\JsonResponse;

class ContestarJson
{

    /**
     * Envía JSON. Si `data` no es string, se serializa con `json_encode` (sin
     * JSON_FORCE_OBJECT) para que arrays sigan siendo `[]` en el payload.
     * Así el envelope sigue siendo `{ "data": "<string JSON>" }` y el cliente
     * puede seguir usando `JSON.parse(rta.data)` donde ya lo hace.
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
     */
    public static function enviar(string $error_txt = '', string|array $data = 'ok'): void
    {
        $jsondata = self::respuestaPhp($error_txt, $data);
        if (!is_string($jsondata['data'])) {
            $jsondata['data'] = json_encode($jsondata['data']);
        }
        (new JsonResponse($jsondata))->send();
    }

    /**
     * forma un array con las claves 'success', 'mensaje' y 'data' sin codificar!
     */
    public static function respuestaPhp(string $error_txt = '', string|array $data = 'ok'): array
    {
        if (!empty($error_txt)) {
            $jsondata['success'] = false;
            $jsondata['mensaje'] = $error_txt;
            $jsondata['data'] = $data ?? 'none';
        } else {
            $jsondata['success'] = true;
            $jsondata['data'] = $data;
        }

        return $jsondata;
    }
}
