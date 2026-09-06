<?php

declare(strict_types=1);

namespace Tests\factories\zonassacd;

use src\zonassacd\domain\entity\ZonaCtr;

class ZonaCtrFactory
{
    public function createSimple(?int $id_ubi = null, int $id_zona = 1): ZonaCtr
    {
        $fila = new ZonaCtr();
        $fila->setId_ubi($id_ubi ?? (9900000 + random_int(1000, 9999)));
        $fila->setId_zona($id_zona);

        return $fila;
    }
}
