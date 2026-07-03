<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Asigna un `CentroEncargado` nuevo a una actividad.
 *
 * Calcula `num_orden = max(num_orden) + 1` para que el nuevo centro quede
 * al final del listado. El campo `encargo` queda a 'organizador' por defecto.
 *
 * Sucesor de la rama `asignar` del dispatcher legacy `activ_ctr_ajax.php`.
 */
final class CentroEncargadoAsignar
{
    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $id_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        if ($id_activ <= 0 || $id_ubi <= 0) {
            return _("faltan parametros id_activ / id_ubi");
        }

        $cCentros = $this->centroEncargadoRepository->getCentrosEncargados([
            'id_activ' => $id_activ,
            '_ordre' => 'num_orden DESC',
        ]);
        $num_orden = count($cCentros) >= 1
            ? ((int) ($cCentros[0]->getNum_orden() ?? 0) + 1)
            : 1;

        $oCentroEncargado = new CentroEncargado();
        $oCentroEncargado->setId_activ($id_activ);
        $oCentroEncargado->setId_ubi($id_ubi);
        $oCentroEncargado->setNum_orden($num_orden);
        $oCentroEncargado->setEncargo('organizador');

        if ($this->centroEncargadoRepository->Guardar($oCentroEncargado) === false) {
            return _("hay un error, no se ha guardado el centro encargado");
        }
        return '';
    }
}
