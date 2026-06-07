<?php

namespace src\ubis\application\services;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Opciones para selects de delegaciones (mapa value => etiqueta).
 */
final class DelegacionDropdown
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * value = "dl|dl{sf}", label = "nombre_dl (dl{sf})"
     *
     * @return array<string, string>
     */
    public function dlURegionesFiltro(int $sfsv = 0): array
    {
        if ($sfsv === 0) {
            $sfsv = ConfigGlobal::mi_sfsv();
        }
        $sf = ($sfsv == 2) ? 'f' : '';

        $delegaciones = $this->delegacionRepository->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $nombreDlVo = $dl->getNombreDlVo();
            if ($nombreDlVo === null) {
                continue;
            }
            $dlCode = (string) ($dl->getDlVo()->value() ?? '');
            $value = 'dl|' . $dlCode . $sf;
            $label = $nombreDlVo->value() . ' (' . $dlCode . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public function activasOrdenNombre(): array
    {
        $delegaciones = $this->delegacionRepository->getDelegaciones(['active' => true, '_ordre' => 'nombre_dl']);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $nombreDlVo = $dl->getNombreDlVo();
            if ($nombreDlVo === null) {
                continue;
            }
            $dlCode = (string) ($dl->getDlVo()->value() ?? '');
            $opciones[$dlCode] = $nombreDlVo->value();
        }

        return $opciones;
    }

    /**
     * @param list<string> $regiones
     * @return array<string, string>
     */
    /**
     * @param list<string> $regiones
     */
    /**
     * @param list<string> $regiones
     * @return array<int|string, string>
     */
    public function byRegiones(array $regiones): array
    {
        $aWhere = ['active' => true, '_ordre' => 'nombre_dl'];
        $aOper = [];
        if (!empty($regiones)) {
            $aWhere['region'] = "'" . implode('","', $regiones) . "'";
            $aOper['region'] = 'IN';
        }
        $delegaciones = $this->delegacionRepository->getDelegaciones($aWhere, $aOper);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $nombreDlVo = $dl->getNombreDlVo();
            if ($nombreDlVo === null) {
                continue;
            }
            $dlCode = (string) ($dl->getDlVo()->value() ?? '');
            $opciones[$dlCode] = $nombreDlVo->value();
        }

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public function listaRegDele(bool $incluirPropia = true): array
    {
        $sf = (ConfigGlobal::mi_sfsv() == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $delegaciones = $this->delegacionRepository->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $nombreDlVo = $dl->getNombreDlVo();
            if ($nombreDlVo === null) {
                continue;
            }
            $dlCode = (string) ($dl->getDlVo()->value() ?? '');
            if (!$incluirPropia && $dlCode === $propia) {
                continue;
            }
            $regionCode = (string) $dl->getRegionVo()->value();
            $value = $regionCode . '-' . $dlCode . $sf;
            $label = $nombreDlVo->value() . ' (' . $dlCode . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public function delegacionesURegiones(int $sfsv = 0, bool $incluirPropia = true): array
    {
        if ($sfsv === 0) {
            $sfsv = ConfigGlobal::mi_sfsv();
        }
        $sf = ($sfsv == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $delegaciones = $this->delegacionRepository->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $nombreDlVo = $dl->getNombreDlVo();
            if ($nombreDlVo === null) {
                continue;
            }
            $dlCode = (string) ($dl->getDlVo()->value() ?? '');
            if (!$incluirPropia && $dlCode === $propia) {
                continue;
            }
            $value = $dlCode . $sf;
            $regionCode = (string) $dl->getRegionVo()->value();
            $label = $nombreDlVo->value() . ' (' . $regionCode . '-' . $dlCode . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }
}
