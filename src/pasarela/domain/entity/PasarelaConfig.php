<?php

namespace src\pasarela\domain\entity;

use src\pasarela\domain\value_objects\PasarelaParametroCode;
use src\shared\domain\traits\Hydratable;
use src\shared\infrastructure\persistence\ConverterJson;
use stdClass;

class PasarelaConfig
{

    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private PasarelaParametroCode $nom_parametro;

    private ?string $json_valor = null;


    public function getNom_parametro(): string
    {
        return $this->nom_parametro->value();
    }


    public function setNom_parametro(string $nom_parametro): void
    {
        $this->nom_parametro = PasarelaParametroCode::fromNullableString($nom_parametro);
    }

    // Value Object API for parametro
    public function getNomParametroVo(): PasarelaParametroCode
    {
        return $this->nom_parametro;
    }

    public function setNomParametroVo(PasarelaParametroCode|string $code): void
    {
        $this->nom_parametro = $code instanceof PasarelaParametroCode
            ? $code
            : PasarelaParametroCode::fromNullableString($code);
    }

    /**
     *
     * @param boolean $returnArray si hay que devolver un array en vez de un objeto.
     * @return array|stdClass|null
     */
    public function getJson_valor(bool $returnArray = FALSE): array|stdClass|null
    {
        return (new ConverterJson($this->json_valor, $returnArray))->fromPg();
    }

    /**
     * @param string|array|null $oJSON json_certificados
     * @param boolean $db =FALSE optional. Para determinar la variable que se le pasa es ya un objeto json,
     *  o es una variable de php hay que convertirlo. En la base de datos ya es json.
     */
    public function setJson_valor(string|array|null $oJSON, bool $db = FALSE): void
    {
        $a_json_certificados = (new ConverterJson($oJSON, FALSE))->toPg($db);
        if ($a_json_certificados === "[]" || empty($a_json_certificados)) {
            $this->json_valor = null;
        } else {
            $this->json_valor = (new ConverterJson($oJSON, FALSE))->toPg($db);
        }
    }
}
