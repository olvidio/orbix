<?php

namespace web;

use Illuminate\Http\JsonResponse;

class ContestarJson
{

    /**
     * codifica la clave 'data' del array con json_encode
     * @param array $jsondata
     * @return void
     */
    static public function send(array $jsondata): void
    {
        $jsondata['data'] = json_encode($jsondata['data'],  JSON_FORCE_OBJECT);
        (new JsonResponse($jsondata))->send();
    }

    /**
     * codifica la clave 'data' del array con json_encode
     * @param string $error_txt
     * @param string|array $data
     * @return void
     */
    static public function enviar(string $error_txt = '', string|array $data = 'ok'): void
    {
        $jsondata = self::respuestaPhp($error_txt, $data);

        $jsondata['data'] = json_encode($jsondata['data'],  JSON_FORCE_OBJECT);

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