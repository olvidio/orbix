<?php

namespace web;

use Illuminate\Http\JsonResponse;

class ContestarJson
{

    /**
     * Envía JSON. Si `data` no es string, se serializa con `json_encode` (sin
     * JSON_FORCE_OBJECT) para que arrays sigan siendo `[]` en el payload.
     * Así el envelope sigue siendo `{ "data": "<string JSON>" }` y el cliente
     * puede seguir usando `JSON.parse(rta.data)` donde ya lo hace.
     *
     * @param array $jsondata
     * @return void
     */
    static public function send(array $jsondata): void
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
     *
     * @param string $error_txt
     * @param string|array $data
     * @return void
     */
    static public function enviar(string $error_txt = '', string|array $data = 'ok'): void
    {
        $jsondata = self::respuestaPhp($error_txt, $data);
        if (!is_string($jsondata['data'])) {
            $jsondata['data'] = json_encode($jsondata['data']);
        }
        (new JsonResponse($jsondata))->send();
    }

    /**
     * forma un array con las claves 'success', 'mensaje' y 'data' sin codificar!
     * @param string $error_txt
     * @param string|array $data
     * @return array
     */
    static public function respuestaPhp(string $error_txt = '', string|array $data = 'ok'): array
    {
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            $jsondata['data'] = $data?? 'none';
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['data'] = $data;
        }

        return $jsondata;
    }
}