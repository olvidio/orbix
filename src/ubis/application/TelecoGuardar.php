<?php

namespace src\ubis\application;

use src\ubis\domain\entity\TelecoUbi;

final class TelecoGuardar
{
    public static function execute(string $obj_pau, int $id_ubi, array $pkey, int $id_tipo_teleco, int $id_desc_teleco, string $num_teleco, string $observ): array
    {
        $resolver = new TelecoResolver();
        $Repository = $resolver->getTelecoRepo($obj_pau);

        if (empty($pkey)) {
            $TelecoUbi = new TelecoUbi();
            $TelecoUbi->setId_item($Repository->getNewId());
            $TelecoUbi->setId_ubi($id_ubi);
        } else {
            $TelecoUbi = $Repository->findById($pkey);
        }

        $TelecoUbi->setId_tipo_teleco($id_tipo_teleco);
        $TelecoUbi->setId_desc_teleco($id_desc_teleco);
        $TelecoUbi->setNum_teleco($num_teleco);
        $TelecoUbi->setObserv($observ);
        $Repository->Guardar($TelecoUbi);

        return ['ok' => true];
    }
}
