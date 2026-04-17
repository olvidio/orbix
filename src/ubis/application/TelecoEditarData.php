<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\application\services\UbiPermisos;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;

final class TelecoEditarData
{
    use ProvidesRepositories;

    public static function execute(string $obj_pau, string $mod, int $id_ubi, int $pkey): array
    {
        return (new self())->run($obj_pau, $mod, $id_ubi, $pkey);
    }

    private function run(string $obj_pau, string $mod, int $id_ubi, int $pkey): array
    {
        $repoTeleco = $this->getTelecoRepository($obj_pau);
        $repoUbi = $this->getRepository($obj_pau);
        $repoName = $this->getTelecoRepositoryClass($obj_pau);

        $desc_teleco = '';
        $id_tipo_teleco = '';
        $num_teleco = '';
        $observ = '';
        if ($mod !== 'nuevo' && !empty($pkey)) {
            $TelecoUbi = $repoTeleco->findById($pkey);
            $desc_teleco = $TelecoUbi->getId_desc_teleco();
            $id_tipo_teleco = $TelecoUbi->getId_tipo_teleco();
            $num_teleco = $TelecoUbi->getNum_teleco();
            $observ = $TelecoUbi->getObserv();
        }

        $oUbi = str_contains($obj_pau, 'Dl') ? $repoUbi->findById($id_ubi) : null;
        $botones = UbiPermisos::puedeModificar($obj_pau, $oUbi) ? '1,3' : '0';

        $TipoTelecoRepository = $GLOBALS['container']->get(TipoTelecoRepositoryInterface::class);
        $a_tipos = $TipoTelecoRepository->getArrayTiposTelecoUbi();
        $a_desc = [];
        if (!empty($id_tipo_teleco)) {
            $a_desc = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class)->getArrayDescTelecoUbis((int)$id_tipo_teleco);
        }

        return [
            'obj' => $repoName,
            'botones' => $botones,
            'id_tipo_teleco' => $id_tipo_teleco,
            'id_desc_teleco' => $desc_teleco,
            'num_teleco' => $num_teleco,
            'observ' => $observ,
            'a_tipos' => $a_tipos,
            'a_desc' => $a_desc,
        ];
    }
}
