<?php

namespace Tests\factories\pasarela;

use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Factory para crear instancias de PasarelaConfig para tests.
 * La clave es `nom_parametro` (texto, solo letras, números, `_` y `-`).
 */
class PasarelaConfigFactory
{
    public function createSimple(?string $nomParametro = null): PasarelaConfig
    {
        $oPasarelaConfig = new PasarelaConfig();
        $oPasarelaConfig->setNom_parametro($nomParametro ?? ('tst_param_' . random_int(1000, 9999)));
        $oPasarelaConfig->setJson_valor(['valor' => 'test']);

        return $oPasarelaConfig;
    }
}
