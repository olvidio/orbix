<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiPermisos;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;

final class TelecoEditarData
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private TipoTelecoRepositoryInterface $tipoTelecoRepository,
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(string $obj_pau, string $mod, int $id_ubi, int $pkey): array
    {
        $repoTeleco = $this->ubiRepositoryResolver->getTelecoRepository($obj_pau);
        $repoName = $this->ubiRepositoryResolver->getTelecoRepositoryClass($obj_pau);

        $desc_teleco = '';
        $id_tipo_teleco = '';
        $num_teleco = '';
        $observ = '';
        if ($mod !== 'nuevo' && !empty($pkey)) {
            $TelecoUbi = $repoTeleco->findById($pkey);
            if ($TelecoUbi !== null) {
                $desc_teleco = $TelecoUbi->getId_desc_teleco();
                $id_tipo_teleco = $TelecoUbi->getId_tipo_teleco();
                $num_teleco = $TelecoUbi->getNum_teleco();
                $observ = $TelecoUbi->getObserv();
            }
        }

        $oUbi = $this->ubiRepositoryResolver->findUbiForPermisos($obj_pau, $id_ubi);
        $dl = ($oUbi !== null && method_exists($oUbi, 'getDl')) ? (string)($oUbi->getDl() ?? '') : '';
        $botones = UbiPermisos::puedeModificarPorObjeto($obj_pau, $dl) ? '1,3' : '0';

        $a_tipos = $this->tipoTelecoRepository->getArrayTiposTelecoUbi();
        $a_desc = [];
        if (!empty($id_tipo_teleco)) {
            $a_desc = $this->descTelecoRepository->getArrayDescTelecoUbis((string) $id_tipo_teleco);
        }

        return [
            'obj' => $repoName,
            'dl' => $dl,
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
