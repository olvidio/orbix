<?php

namespace src\cartaspresentacion\application;

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;

/**
 * Data builder: listado de centros con el estado (si/no) de su carta de
 * presentacion, datos para pintar con `web\Lista` en el frontend.
 *
 * Sucesor de las ramas `get_dl` y `get_r` del dispatcher
 * `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.
 *
 * `a_cabeceras` se devuelve ya en el formato esperado por `web\Lista`.
 * `a_valores` contiene filas con columnas 1..4 donde las columnas
 * interactivas usan `['script' => ..., 'valor' => ...]` para que las
 * funciones JS (fnjs_modificar, fnjs_ver_ubi, fnjs_eliminar_cp) se
 * invoquen desde los formatters del `Lista`.
 */
final class CartasPresentacionUbisListaData
{
    /**
     * @param array{tipo_lista?: string, poblacion_sel?: string} $input
     * @return array{
     *   tipo_lista: string,
     *   explicacion: string,
     *   a_cabeceras: array<int,array<string,mixed>>,
     *   a_valores: array<int, array<int, string|array<string,string>>>,
     * }
     */
    public static function execute(array $input): array
    {
        $tipo_lista = (string)($input['tipo_lista'] ?? '');
        $poblacion_sel = (string)($input['poblacion_sel'] ?? '');

        return match ($tipo_lista) {
            'get_dl' => self::buildDl($poblacion_sel),
            'get_r' => self::buildEx(),
            default => [
                'tipo_lista' => $tipo_lista,
                'explicacion' => '',
                'a_cabeceras' => self::cabeceras(),
                'a_valores' => [],
            ],
        };
    }

    /**
     * @return array{
     *   tipo_lista: string,
     *   explicacion: string,
     *   a_cabeceras: array<int,array<string,mixed>>,
     *   a_valores: array<int, array<int, string|array<string,string>>>
     * }
     */
    private static function buildDl(string $poblacion_sel): array
    {
        $cDirCentros = [];

        if ($poblacion_sel !== '') {
            $repoDireccion = $GLOBALS['container']->get(DireccionCentroDlRepositoryInterface::class);
            $repoCentro = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $repoCtrxDir = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);

            $cDirecciones = $repoDireccion->getDirecciones(
                ['poblacion' => $poblacion_sel],
                ['poblacion' => 'sin_acentos']
            );
            $d = 0;
            foreach ($cDirecciones as $oDireccion) {
                $d++;
                $id_direccion = $oDireccion->getId_direccion();
                $cId_ubis = $repoCtrxDir->getUbisPorDireccion($id_direccion);
                $cCentros = [];
                foreach ($cId_ubis as $aId_ubi) {
                    $oCentro = $repoCentro->findById((int)$aId_ubi['id_ubi']);
                    if ($oCentro !== null) {
                        $cCentros[] = $oCentro;
                    }
                }
                $cDirCentros[$d] = [
                    'dir' => $oDireccion->getDireccionPostal(' - '),
                    'colCentros' => $cCentros,
                    'id_direccion' => $id_direccion,
                    'nom_sede' => $oDireccion->getNom_sede(),
                ];
            }
        }

        $repoCarta = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
        $a_valores = [];
        $orden_nom = [];
        $c = 0;
        foreach ($cDirCentros as $Cen) {
            $txt_direccion = $Cen['dir'];
            $id_direccion = $Cen['id_direccion'];
            $nom_sede = $Cen['nom_sede'];
            foreach ($Cen['colCentros'] as $oCentro) {
                $c++;
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                if (!$oCentro->isActive()) {
                    $nombre_ubi = _("ANULADO") . ' ' . $nombre_ubi;
                }
                $nombre_ubi .= $nom_sede === '' ? '' : " ($nom_sede)";

                $activo = $repoCarta->findById($id_ubi, (int)$id_direccion) !== null;
                $a_valores[$c] = self::buildFila($id_direccion, $id_ubi, $nombre_ubi, $activo, $txt_direccion);
                $orden_nom[$c] = strtolower($nombre_ubi);
            }
        }
        if (!empty($orden_nom)) {
            array_multisort($orden_nom, SORT_LOCALE_STRING, SORT_ASC, $a_valores);
        }

        $explicacion = '<br>' . _("para añadir un centro como carta de presentación, basta con poner el nombre del director.");

        return [
            'tipo_lista' => 'get_dl',
            'explicacion' => $explicacion,
            'a_cabeceras' => self::cabeceras(),
            'a_valores' => $a_valores,
        ];
    }

    /**
     * @return array{
     *   tipo_lista: string,
     *   explicacion: string,
     *   a_cabeceras: array<int,array<string,mixed>>,
     *   a_valores: array<int, array<int, string|array<string,string>>>
     * }
     */
    private static function buildEx(): array
    {
        $repoCentro = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        $repoCtrxDir = $GLOBALS['container']->get(RelacionCentroExDireccionRepositoryInterface::class);
        $repoDireccion = $GLOBALS['container']->get(DireccionCentroExRepositoryInterface::class);
        $repoCarta = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);

        $aWhere = ['tipo_ctr' => 'cr|dl', 'active' => 't', '_ordre' => 'nombre_ubi'];
        $aOperador = ['tipo_ctr' => '~'];
        $cCentros = $repoCentro->getCentros($aWhere, $aOperador);

        $c = 0;
        $a_valores = [];
        $orden_nom = [];
        foreach ($cCentros as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi_base = $oCentro->getNombre_ubi();

            $cCtrxDir = $repoCtrxDir->getDireccionesPorUbi($id_ubi);
            foreach ($cCtrxDir as $aCtrxDir) {
                $id_direccion = (int)$aCtrxDir['id_direccion'];
                $oDireccion = $repoDireccion->findById($id_direccion);
                if ($oDireccion === null) {
                    continue;
                }
                $txt_direccion = $oDireccion->getDireccionPostal(' - ');
                $nom_sede = (string)$oDireccion->getNom_sede();
                $nombre_ubi = $nombre_ubi_base . ($nom_sede === '' ? '' : " ($nom_sede)");
                $activo = $repoCarta->findById($id_ubi, $id_direccion) !== null;

                $a_valores[$c] = self::buildFila($id_direccion, $id_ubi, $nombre_ubi, $activo, $txt_direccion);
                $orden_nom[$c] = strtolower($nombre_ubi);
            }
        }
        if (!empty($orden_nom)) {
            array_multisort($orden_nom, SORT_LOCALE_STRING, SORT_ASC, $a_valores);
        }

        return [
            'tipo_lista' => 'get_r',
            'explicacion' => '',
            'a_cabeceras' => self::cabecerasEx(),
            'a_valores' => $a_valores,
        ];
    }

    /**
     * @return array<int, string|array<string,string>>
     */
    private static function buildFila(int|string $id_direccion, int|string $id_ubi, string $nombre_ubi, bool $activo, string $txt_direccion): array
    {
        $id_direccion = (int)$id_direccion;
        $id_ubi = (int)$id_ubi;
        $pres = $activo ? _("si") : _("no");

        $fila = [];
        $fila[1] = [
            'script' => "fnjs_modificar($id_direccion,$id_ubi)",
            'valor' => 'director',
        ];
        $fila[2] = [
            'script2' => "fnjs_ver_ubi($id_ubi)",
            'valor' => $nombre_ubi,
        ];
        if ($activo) {
            $fila[3] = [
                'script3' => "fnjs_eliminar_cp($id_direccion,$id_ubi)",
                'valor' => $pres . ', ' . _("quitar"),
            ];
        } else {
            $fila[3] = $pres;
        }
        $fila[4] = $txt_direccion;
        return $fila;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private static function cabeceras(): array
    {
        return [
            ['name' => ucfirst(_("nombre")), 'width' => 20, 'formatter' => 'clickFormatter'],
            ['name' => ucfirst(_("centro")), 'width' => 80, 'formatter' => 'clickFormatter2'],
            ['name' => ucfirst(_("carta de presentación")), 'width' => 20, 'formatter' => 'clickFormatter3'],
            ['name' => ucfirst(_("dirección")), 'width' => 100],
        ];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private static function cabecerasEx(): array
    {
        $cab = self::cabeceras();
        $cab[3]['name'] = ucfirst(_("direccion"));
        return $cab;
    }
}
