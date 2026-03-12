<?php

namespace src\ubis\application\services;

use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class UbiTelecoService
{
    use ProvidesRepositories;

    public static function texto(
        string $obj_pau,
        int $id_ubi,
        string $tipo_teleco,
        string $desc_teleco = '*',
        string $separador = ' / '
    ): string {
        $self = new self();

        $tipoTelecoMap = [
            'telf' => 1,
            'fax' => 4,
            'e-mail' => 3,
        ];
        $id_tipo_teleco = $tipoTelecoMap[$tipo_teleco] ?? null;
        if ($id_tipo_teleco === null) {
            return '';
        }

        $TelecoRepository = $self->getTelecoRepository($obj_pau);
        $aWhere = [
            'id_ubi' => $id_ubi,
            'id_tipo_teleco' => $id_tipo_teleco,
        ];
        if ($desc_teleco !== '*' && $desc_teleco !== '') {
            $aWhere['id_desc_teleco'] = $desc_teleco;
        }

        $cTelecos = $TelecoRepository->getTelecos($aWhere) ?: [];
        if (empty($cTelecos)) {
            return '';
        }

        $DescTelecoRepository = $GLOBALS['container']->get(DescTelecoRepositoryInterface::class);
        $aTelefonos = [];
        foreach ($cTelecos as $oTelecoUbi) {
            $num_teleco = trim($oTelecoUbi->getNumTelecoVo()->value());
            if ($num_teleco === '') {
                continue;
            }
            if ($desc_teleco === '*') {
                $id_desc = $oTelecoUbi->getId_desc_teleco();
                if (!empty($id_desc)) {
                    $oDescTel = $DescTelecoRepository->findById((int)$id_desc);
                    $desc = $oDescTel?->getDescTelecoVo()?->value() ?? '';
                    if ($desc !== '') {
                        $num_teleco .= "($desc)";
                    }
                }
            }
            $aTelefonos[] = $num_teleco;
        }

        return implode($separador, $aTelefonos);
    }
}
