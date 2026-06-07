<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class TelecoDescLista
{
    public function __construct(
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
    }

    /**
     * @return array{a_desc: array<int|string, string>}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_tipo_teleco): array
    {
        $aOpciones = $this->descTelecoRepository->getArrayDescTelecoUbis((string) $id_tipo_teleco);

        return ['a_desc' => $aOpciones];
    }
}
