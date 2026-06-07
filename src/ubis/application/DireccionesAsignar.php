<?php

namespace src\ubis\application;

final class DireccionesAsignar
{
    public function __construct(
        private DireccionesResolver $direccionesResolver,
    ) {
    }

    /**
     * @return array{ok: bool}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi, string $obj_dir, int $id_direccion): array
    {
        $UbiRepository = $this->direccionesResolver->ubiRepo($obj_dir);
        $oUbi = $UbiRepository->findById($id_ubi);
        if ($oUbi === null) {
            return ['ok' => false];
        }
        $oUbi->addDireccion($id_direccion, false, true);

        return ['ok' => true];
    }
}
