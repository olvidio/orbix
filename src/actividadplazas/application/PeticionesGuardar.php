<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Guarda las peticiones de una persona+tipo. Borra todas las
 * anteriores y crea las nuevas en el orden recibido.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php`.
 */
final class PeticionesGuardar
{
    public function __construct(
        private PlazaPeticionRepositoryInterface $plazaPeticionRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_nom = FuncTablasSupport::inputInt($input, 'id_nom');
        $sactividad = FuncTablasSupport::inputString($input, 'sactividad');
        if ($id_nom <= 0 || $sactividad === '') {
            return (string)_("faltan parametros id_nom / sactividad");
        }

        $cPlazasPeticion = $this->plazaPeticionRepository->getPlazasPeticion([
            'id_nom' => $id_nom,
            'tipo' => $sactividad,
        ]);
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            $this->plazaPeticionRepository->Eliminar($oPlazaPeticion);
        }

        $a_actividades = FuncTablasSupport::inputStringList($input, 'actividades');
        $i = 0;
        foreach ($a_actividades as $id_activ_raw) {
            $id_activ = (int)$id_activ_raw;
            if ($id_activ === 0) {
                continue;
            }
            $i++;
            $oPlazaPeticion = $this->plazaPeticionRepository->findById($id_nom, $id_activ);
            if ($oPlazaPeticion === null) {
                $oPlazaPeticion = new PlazaPeticion();
                $oPlazaPeticion->setId_nom($id_nom);
                $oPlazaPeticion->setId_activ($id_activ);
            }
            $oPlazaPeticion->setOrden($i);
            $oPlazaPeticion->setTipo($sactividad);
            if ($this->plazaPeticionRepository->Guardar($oPlazaPeticion) === false) {
                return (string)_("hay un error, no se han guardado todas las peticiones");
            }
        }
        return '';
    }
}
