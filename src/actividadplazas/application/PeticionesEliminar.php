<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;

/**
 * Elimina todas las peticiones de plaza para un {id_nom, tipo}.
 *
 * Sucesor de la rama `borrar` del dispatcher legacy
 * `apps/actividadplazas/controller/peticiones_activ_ajax.php`.
 */
final class PeticionesEliminar
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
        $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        $sactividad = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad');
        if ($id_nom <= 0 || $sactividad === '') {
            return (string)_("faltan parametros id_nom / sactividad");
        }

        $cPlazasPeticion = $this->plazaPeticionRepository->getPlazasPeticion([
            'id_nom' => $id_nom,
            'tipo' => $sactividad,
        ]);
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            if ($this->plazaPeticionRepository->Eliminar($oPlazaPeticion) === false) {
                return (string)_("hay un error, no se ha podido eliminar");
            }
        }
        return '';
    }
}
