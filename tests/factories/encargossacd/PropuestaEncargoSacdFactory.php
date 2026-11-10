<?php

namespace Tests\factories\encargossacd;

use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Factory de PropuestaEncargoSacd (tabla staging `propuesta_encargos_sacd`).
 * Ids de test 9900000+: fuera del rango de las secuencias reales.
 */
class PropuestaEncargoSacdFactory
{
    public function createSimple(?int $id = null, ?int $idNomNew = null): PropuestaEncargoSacd
    {
        $o = new PropuestaEncargoSacd();
        $o->setId_item($id ?? (9900000 + random_int(1000, 9999)));
        $o->setId_enc(3);
        $o->setId_nom(10011);
        $o->setModoVo(new EncargoModoId(3));
        $o->setF_ini(new DateTimeLocal('2023-04-05'));
        $o->setId_nom_new($idNomNew);

        return $o;
    }
}
