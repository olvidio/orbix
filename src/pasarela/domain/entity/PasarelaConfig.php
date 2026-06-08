<?php

namespace src\pasarela\domain\entity;

use src\pasarela\domain\value_objects\PasarelaParametroCode;
use src\shared\domain\traits\Hydratable;
use src\shared\infrastructure\persistence\ConverterJson;
use stdClass;

class PasarelaConfig
{
    use Hydratable;

    private ?PasarelaParametroCode $nom_parametro = null;

    private ?string $json_valor = null;

    public function getNom_parametro(): string
    {
        return $this->nom_parametro?->value() ?? '';
    }

    public function setNom_parametro(string $nom_parametro): void
    {
        $this->nom_parametro = PasarelaParametroCode::fromNullableString($nom_parametro);
    }

    public function getNomParametroVo(): PasarelaParametroCode
    {
        if ($this->nom_parametro === null) {
            throw new \RuntimeException('nom_parametro not set');
        }

        return $this->nom_parametro;
    }

    public function setNomParametroVo(PasarelaParametroCode|string $code): void
    {
        $this->nom_parametro = $code instanceof PasarelaParametroCode
            ? $code
            : PasarelaParametroCode::fromNullableString($code);
    }

    /**
     * @return array<string, mixed>|stdClass|null
     */
    public function getJson_valor(bool $returnArray = false): array|stdClass|null
    {
        $value = (new ConverterJson($this->json_valor, $returnArray))->fromPg();
        if (!is_array($value)) {
            return $value;
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            $normalized[(string) $key] = $item;
        }

        return $normalized;
    }

    /**
     * @param string|array<string, mixed>|stdClass|null $oJSON
     */
    public function setJson_valor(string|array|stdClass|null $oJSON, bool $db = false): void
    {
        $encoded = (new ConverterJson($oJSON, false))->toPg($db);
        if (!is_string($encoded) || $encoded === '[]' || $encoded === '') {
            $this->json_valor = null;
        } else {
            $this->json_valor = $encoded;
        }
    }
}
