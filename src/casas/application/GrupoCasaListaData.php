<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\permisos\domain\XPermisos;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Data builder: listado de `GrupoCasa` (relaciones casa padre ↔ casa hija).
 *
 * Sucesor de `apps/casas/controller/grupo_lista.php` (construcción de
 * los datos de la tabla; el renderizado HTML lo hace el frontend).
 */
final class GrupoCasaListaData
{
    public function __construct(
        private GrupoCasaRepositoryInterface $grupoCasaRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
    ) {
    }

    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int|string,string|array{script:string,valor:string}>>,
     *   puede_anadir: bool
     * }
     */
    public function execute(): array
    {
        $cGrupoCasas = $this->grupoCasaRepository->getGrupoCasas();

        $a_valores = [];
        $i = 0;
        foreach ($cGrupoCasas as $oGrupoCasa) {
            $i++;
            $id_item = $oGrupoCasa->getId_item();

            $oCasaPadre = $this->casaDlRepository->findById($oGrupoCasa->getId_ubi_padre());
            $casa_padre = $oCasaPadre !== null ? $oCasaPadre->getNombre_ubi() : '';

            $oCasaHijo = $this->casaDlRepository->findById($oGrupoCasa->getId_ubi_hijo());
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

        $a_cabeceras = [
            _("casa padre"),
            _("casa hijo"),
            '',
            '',
        ];

        $oPerm = $_SESSION['oPerm'] ?? null;
        $puede_anadir = $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('adl');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'puede_anadir' => $puede_anadir,
        ];
    }
}
