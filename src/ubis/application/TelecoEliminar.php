<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiRepositoryResolver;

final class TelecoEliminar
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
    ) {
    }

    /**
     * @param list<int|string> $a_pkey
     * @return array{ok: true}
     */
    public function execute(string $obj_pau, array $a_pkey): array
    {
        $Repository = $this->ubiRepositoryResolver->getTelecoRepository($obj_pau);

        foreach ($a_pkey as $pkey) {
            $id = is_int($pkey) ? $pkey : (int) $pkey;
            $TelecoUbi = $Repository->findById($id);
            if ($TelecoUbi === null) {
                continue;
            }
            $Repository->Eliminar($TelecoUbi);
        }

        return ['ok' => true];
    }
}
