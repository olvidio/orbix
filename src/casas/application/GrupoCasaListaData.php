<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Data builder: listado de `GrupoCasa` (relaciones casa padre ↔ casa hija).
 *
 * Sucesor de `apps/casas/controller/grupo_lista.php` (construcción de
 * los datos de la tabla; el renderizado HTML lo hace el frontend).
 */
final class GrupoCasaListaData
{
    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int|string,string|array{script:string,valor:string}>>,
     *   puede_anadir: bool
     * }
     */
    public static function execute(): array
    {
        $repoGrupo = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        $repoCasa = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);

        $cGrupoCasas = $repoGrupo->getGrupoCasas();

        $a_valores = [];
        $i = 0;
        if (is_array($cGrupoCasas)) {
            foreach ($cGrupoCasas as $oGrupoCasa) {
                $i++;
                $id_item = $oGrupoCasa->getId_item();

                $oCasaPadre = $repoCasa->findById($oGrupoCasa->getId_ubi_padre());
                $casa_padre = $oCasaPadre !== null ? $oCasaPadre->getNombre_ubi() : '';

                $oCasaHijo = $repoCasa->findById($oGrupoCasa->getId_ubi_hijo());
                $casa_hijo = $oCasaHijo !== null ? $oCasaHijo->getNombre_ubi() : '';

                $a_valores[$i][1] = $casa_padre;
                $a_valores[$i][2] = $casa_hijo;
                $a_valores[$i][3] = [
                    'script' => "fnjs_modificar($id_item)",
                    'valor' => _("editar"),
                ];
                $a_valores[$i][4] = [
                    'script' => "fnjs_eliminar($id_item)",
                    'valor' => _("eliminar"),
                ];
            }
        }

        $a_cabeceras = [
            _("casa padre"),
            _("casa hijo"),
            '',
            '',
        ];

        $puede_anadir = $_SESSION['oPerm']->have_perm_oficina('adl');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_anadir' => (bool)$puede_anadir,
        ];
    }
}
