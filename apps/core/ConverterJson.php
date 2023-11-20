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
        if ($this->json !== NULL) {
            $oJSON = json_decode($this->json, $this->bArray, 512, JSON_THROW_ON_ERROR);
        } else {
            $oJSON = '';
        }

        if (empty($oJSON) || $oJSON === '[]') {
            if ($this->bArray) {
                $oJSON = [];
            } else {
                $oJSON = new stdClass;
            }
        }

        return $oJSON;
    }

    /**
     * @throws JsonException
     */
    public function toPg($db): bool|array|string|stdClass|null
    {
        if ($db === FALSE) {
            $json = json_encode($this->json, JSON_THROW_ON_ERROR);
        } else {
            $json = $this->json;
        }

        return $json;
    }

}