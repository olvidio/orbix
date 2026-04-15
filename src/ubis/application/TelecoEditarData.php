<?php

namespace src\ubis\application;

use core\ConfigGlobal;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;

final class TelecoEditarData
{
    public static function execute(string $obj_pau, string $mod, int $id_ubi, array $pkey): array
    {
        $resolver = new TelecoResolver();
        $repoTeleco = $resolver->getTelecoRepo($obj_pau);
        $repoUbi = $resolver->getUbiRepo($obj_pau);
        $repoName = $resolver->getTelecoRepoClass($obj_pau);

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

        $botones = '0';
        if (str_contains($obj_pau, 'Dl')) {
            $oUbi = $repoUbi->findById($id_ubi);
            if ($oUbi->getDl() === ConfigGlobal::mi_delef() && $_SESSION['oPerm']->have_perm_oficina('scdl')) {
                $botones = '1,3';
            }
        } elseif (str_contains($obj_pau, 'Ex') && $_SESSION['oPerm']->have_perm_oficina('scdl')) {
            $botones = '1,3';
        }

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
