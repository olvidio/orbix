<?php

namespace src\actividadplazas\application;

use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

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
        $id_nom = input_int($input, 'id_nom');
        $sactividad = input_string($input, 'sactividad');
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
