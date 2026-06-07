<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\entity\TelecoUbi;

final class TelecoGuardar
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
    ) {
    }

    /**
     * @param list<int|string> $a_pkey
     * @return array{ok: true}
     */
    public function execute(
        string $obj_pau,
        int $id_ubi,
        array $a_pkey,
        int $id_tipo_teleco,
        int $id_desc_teleco,
        string $num_teleco,
        string $observ
    ): array {
        $Repository = $this->ubiRepositoryResolver->getTelecoRepository($obj_pau);

        if ($a_pkey === []) {
            $TelecoUbi = new TelecoUbi();
            $TelecoUbi->setId_item($Repository->getNewId());
            $TelecoUbi->setId_ubi($id_ubi);
        } else {
            $firstKey = reset($a_pkey);
            $pkey = is_int($firstKey) ? $firstKey : (int) $firstKey;
            $TelecoUbi = $Repository->findById($pkey);
            if ($TelecoUbi === null) {
                throw new \RuntimeException(sprintf(_('No se encuentra teleco id %s'), (string) $pkey));
            }
        }

        $TelecoUbi->setId_tipo_teleco($id_tipo_teleco);
        $TelecoUbi->setId_desc_teleco($id_desc_teleco);
        $TelecoUbi->setNum_teleco($num_teleco);
        $TelecoUbi->setObserv($observ);
        $Repository->Guardar($TelecoUbi);

        return ['ok' => true];
    }
}
