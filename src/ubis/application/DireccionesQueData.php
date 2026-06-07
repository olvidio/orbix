<?php

namespace src\ubis\application;

final class DireccionesQueData
{
    public function __construct(
        private UbiFactory $ubiFactory,
    ) {
    }

    /**
     * @return array{tipo_ubi: string|null, titulo: string}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi): array
    {
        $oUbi = $this->ubiFactory->newUbi($id_ubi);

        return [
            'tipo_ubi' => $oUbi?->getTipo_ubi(),
            'titulo' => ucfirst(_("introduzca un valor para buscar una dirección existente")),
        ];
    }
}
