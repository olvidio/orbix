<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Datos de la pantalla `ctr_ficha`:
 * - calcula el `filtro_ctr` efectivo a partir del centro (cuando no viene del POST)
 * - devuelve las `opciones_seccion` para el desplegable de grupo de ctrs.
 *
 * Reemplaza la lectura directa de repos y el acceso a `EncargoAplicacionService`
 * que el frontend hacia en `ctr_ficha.php`.
 */
final class CtrFichaData
{
    /**
     * @return array{filtro_ctr: int, opciones_seccion: array<string, string>}
     */
    public static function execute(int $id_ubi, int $filtro_ctr): array
    {
        if ($id_ubi > 0) {
            $filtro_ctr = self::calcularFiltroDesdeCentro($id_ubi, $filtro_ctr);
        }

        $oAplicacion = new EncargoAplicacionService();

        return [
            'filtro_ctr' => $filtro_ctr,
            'opciones_seccion' => $oAplicacion->getArraySeccion(),
        ];
    }

    private static function calcularFiltroDesdeCentro(int $id_ubi, int $filtro_ctr): int
    {
        $id_ubi_txt = (string)$id_ubi;

        if ((int)$id_ubi_txt[0] === 2) {
            $repo = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        } else {
            $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        }

        $oCentro = $repo->findById($id_ubi);
        if ($oCentro === null) {
            return $filtro_ctr;
        }

        $tipo_ubi = $oCentro->getTipo_ubi();
        $tipo_ctr = $oCentro->getTipo_ctr();

        if ($tipo_ubi === 'ctrsf') {
            return EncargoGrupo::CENTRO_SF;
        }

        return match ($tipo_ctr) {
            'a', 'n', 's', 'aj', 'am', 'nj', 'nm', 'sj', 'sm' => EncargoGrupo::CENTRO_SV,
            'ss' => EncargoGrupo::CENTRO_SSSC,
            'igloc' => EncargoGrupo::IGL,
            'cgioc', 'oc' => EncargoGrupo::CGI,
            default => $filtro_ctr,
        };
    }
}
