<?php

namespace src\ubis\application\services;

use src\ubis\domain\criteria\TelecoUbiField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

final class UbiTelecoService
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
    }

    public function texto(
        string $obj_pau,
        int $id_ubi,
        string $tipo_teleco,
        string $desc_teleco = '*',
        string $separador = ' / '
    ): string {
        $tipoTelecoMap = [
            'telf' => 1,
            'fax' => 4,
            'e-mail' => 3,
        ];
        $id_tipo_teleco = $tipoTelecoMap[$tipo_teleco] ?? null;
        if ($id_tipo_teleco === null) {
            return '';
        }

        $TelecoRepository = $this->ubiRepositoryResolver->getTelecoRepository($obj_pau);
        $criteria = Criteria::create()
            ->where(Filter::eq(F::IdUbi, $id_ubi))
            ->andWhere(Filter::eq(F::IdTipoTeleco, $id_tipo_teleco));
        if ($desc_teleco !== '*' && $desc_teleco !== '') {
            $criteria = $criteria->andWhere(Filter::eq(F::IdDescTeleco, $desc_teleco));
        }

        $cTelecos = $TelecoRepository->matching($criteria);
        if (empty($cTelecos)) {
            return '';
        }

        $aTelefonos = [];
        foreach ($cTelecos as $oTelecoUbi) {
            $num_teleco = trim($oTelecoUbi->getNumTelecoVo()->value());
            if ($num_teleco === '') {
                continue;
            }
            if ($desc_teleco === '*') {
                $id_desc = $oTelecoUbi->getId_desc_teleco();
                if (!empty($id_desc)) {
                    $oDescTel = $this->descTelecoRepository->findById((int)$id_desc);
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
