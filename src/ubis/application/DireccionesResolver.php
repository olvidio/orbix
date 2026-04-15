<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;

final class DireccionesResolver
{
    public static function direccionRepo(string $obj_dir): object
    {
        return match ($obj_dir) {
            'DireccionCentroDl' => $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class),
            'DireccionCentroEx' => $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class),
            'DireccionCdcDl' => $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class),
            'DireccionCdcEx' => $GLOBALS['container']->get(DireccionCasaExRepositoryInterface::class),
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }

    public static function ubiRepo(string $obj_dir): object
    {
        return match ($obj_dir) {
            'DireccionCentroDl' => $GLOBALS['container']->get(CentroDlRepositoryInterface::class),
            'DireccionCentroEx' => $GLOBALS['container']->get(CentroExRepositoryInterface::class),
            'DireccionCdcDl' => $GLOBALS['container']->get(CasaDlRepositoryInterface::class),
            'DireccionCdcEx' => $GLOBALS['container']->get(CasaExRepositoryInterface::class),
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }
}
