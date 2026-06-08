<?php

namespace src\shared\infrastructure\persistence;

use JsonException;
use stdClass;

class ConverterJson
{
    /** @var array<int|string, mixed>|stdClass|string|null */
    private array|stdClass|string|null $json;
    private bool $bArray;

    /**
     * @param array<int|string, mixed>|stdClass|string|null $json
     */
    public function __construct(array|stdClass|string|null $json, bool $bArray)
    {
        $this->json = $json;
        $this->bArray = $bArray;
    }

    /**
     * @throws JsonException
     * @return array<int|string, mixed>|stdClass|null
     */
    public function fromPg(): stdClass|array|null
    {
        $oJSON = null;
        if ($this->json !== null && $this->json !== '') {
            $jsonString = is_string($this->json) ? $this->json : json_encode($this->json, JSON_THROW_ON_ERROR);
            $oJSON = json_decode($jsonString, $this->bArray, 512, JSON_THROW_ON_ERROR);
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

        if ($oJSON === null || is_array($oJSON) || $oJSON instanceof stdClass) {
            return $oJSON;
        }

        return null;
    }

    /**
     * @throws JsonException
     * @return array<int|string, mixed>|bool|string|stdClass|null
     */
    public function toPg(bool $db): bool|array|string|stdClass|null
    {
        if ($this->json === null) {
            return null;
        }

        if ($db === false) {
            $json = json_encode($this->json, JSON_THROW_ON_ERROR);
        } else {
            $json = $this->json;
        }

        return $json;
    }

}
