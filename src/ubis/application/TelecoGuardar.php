<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\entity\TelecoUbi;

final class TelecoGuardar
{
    use ProvidesRepositories;

    public static function execute(string $obj_pau, int $id_ubi, array $a_pkey, int $id_tipo_teleco, int $id_desc_teleco, string $num_teleco, string $observ): array
    {
        return (new self())->run($obj_pau, $id_ubi, $a_pkey, $id_tipo_teleco, $id_desc_teleco, $num_teleco, $observ);
    }

    private function run(string $obj_pau, int $id_ubi, array $a_pkey, int $id_tipo_teleco, int $id_desc_teleco, string $num_teleco, string $observ): array
    {
        $Repository = $this->getTelecoRepository($obj_pau);

        if (empty($a_pkey)) {
            $TelecoUbi = new TelecoUbi();
            $TelecoUbi->setId_item($Repository->getNewId());
            $TelecoUbi->setId_ubi($id_ubi);
        } else {
            // Aqui no tiene sentido que haya más de uno
            $pkey = $a_pkey[0];
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
