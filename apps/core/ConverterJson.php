<?php

namespace core;

use JsonException;
use stdClass;

class ConverterJson
{
    private string|array|stdClass|null $json;
    private bool $bArray;

    public function __construct($json, $bArray)
    {
        $this->json = $json;
        $this->bArray = $bArray;
    }

    /**
     * @throws JsonException
     */
    public function fromPg(): stdClass|array|null
    {
        $oJSON = null;
        if ($this->json !== null && $this->json !== '') {
            $oJSON = json_decode($this->json, $this->bArray, 512, JSON_THROW_ON_ERROR);
            if (is_string($oJSON)) {
                // Si es un string (se ha codificado 2 veces), intentamos decodificarlo
                $oJSON = json_decode($oJSON, $this->bArray, 512, JSON_THROW_ON_ERROR);
            }
        }

        // Si el resultado es vacío, nulo o el string de array vacío '[]'
        if (empty($oJSON) || $oJSON === '[]') {
            if ($this->bArray) {
                $oJSON = [];
            } else {
                //$oJSON = new stdClass;
                $oJSON = null;
            }
        }

        return $oJSON;
    }

    /**
     * @throws JsonException
     */
    public function toPg($db): bool|array|string|stdClass|null
    {
        if ($this->json === null) {
            return null;
        }

        if ($db === FALSE) {
            $json = json_encode($this->json, JSON_THROW_ON_ERROR);
        } else {
            $json = $this->json;
        }

        return $json;
    }

}