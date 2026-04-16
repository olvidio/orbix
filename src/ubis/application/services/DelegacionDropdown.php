<?php

namespace src\ubis\application\services;

use core\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Opciones para selects de delegaciones (mapa value => etiqueta).
 */
final class DelegacionDropdown
{
    /**
     * value = "dl|dl{sf}", label = "nombre_dl (dl{sf})"
     *
     * @return array<string, string>
     */
    public static function dlURegionesFiltro(int $sfsv = 0): array
    {
        if ($sfsv === 0) {
            $sfsv = ConfigGlobal::mi_sfsv();
        }
        $sf = ($sfsv == 2) ? 'f' : '';

        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $delegaciones = $repo->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $value = 'dl|' . ($dl->getDlVo()?->value() ?? '') . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getDlVo()?->value() ?? '') . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public static function activasOrdenNombre(): array
    {
        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $delegaciones = $repo->getDelegaciones(['active' => true, '_ordre' => 'nombre_dl']);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $opciones[$dl->getDlVo()?->value() ?? ''] = $dl->getNombreDlVo()?->value() ?? '';
        }

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public static function byRegiones(array $regiones): array
    {
        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $aWhere = ['active' => true, '_ordre' => 'nombre_dl'];
        $aOper = [];
        if (!empty($regiones)) {
            $aWhere['region'] = "'" . implode('","', $regiones) . "'";
            $aOper['region'] = 'IN';
        }
        $delegaciones = $repo->getDelegaciones($aWhere, $aOper);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $opciones[$dl->getDlVo()?->value() ?? ''] = $dl->getNombreDlVo()?->value() ?? '';
        }

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public static function listaRegDele(bool $incluirPropia = true): array
    {
        $sf = (ConfigGlobal::mi_sfsv() == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $delegaciones = $repo->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            if (!$incluirPropia && ($dl->getDlVo()?->value() ?? '') === $propia) {
                continue;
            }
            $value = ($dl->getRegionVo()?->value() ?? '') . '-' . ($dl->getDlVo()?->value() ?? '') . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getDlVo()?->value() ?? '') . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    /**
     * @return array<string, string>
     */
    public static function delegacionesURegiones(int $sfsv = 0, bool $incluirPropia = true): array
    {
        if ($sfsv === 0) {
            $sfsv = ConfigGlobal::mi_sfsv();
        }
        $sf = ($sfsv == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $repo = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $delegaciones = $repo->getDelegaciones(['active' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $dlCode = $dl->getDlVo()?->value() ?? '';
            if (!$incluirPropia && $dlCode === $propia) {
                continue;
            }
            $value = $dlCode . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getRegionVo()?->value() ?? '') . '-' . $dlCode . $sf . ')';
            $opciones[$value] = $label;
        }
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }
}
