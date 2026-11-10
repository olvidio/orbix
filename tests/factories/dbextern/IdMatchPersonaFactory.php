<?php

namespace Tests\factories\dbextern;

use src\dbextern\domain\entity\IdMatchPersona;

/**
 * Factory de IdMatchPersona para tests.
 * `id_listas` es la clave (bigint, tabla conv_id_personas). Se usan ids altos
 * (990000001..990999999) para no chocar con datos reales: los id_listas reales
 * empiezan por el tipo de persona (1-4), nunca por 9. `id_orbix` es único en BD.
 */
class IdMatchPersonaFactory
{
    public const ID_BASE = 990000000;

    public function createSimple(?int $idListas = null, ?int $idOrbix = null, string $idTabla = 'n'): IdMatchPersona
    {
        $o = new IdMatchPersona();
        $o->setId_listas($idListas ?? (self::ID_BASE + random_int(1, 999999)));
        $o->setId_orbix($idOrbix);
        $o->setId_tabla($idTabla);

        return $o;
    }
}
