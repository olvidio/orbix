<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\input_int;

/**
 * Devuelve el payload del desplegable "posibles propietarios de
 * plaza" usado por `apps/asistentes` al asignar plaza a una
 * asistencia.
 *
 * Formato de retorno (contrato estandar de desplegable en
 * `refactor.md`): `{id, opciones, selected, blanco, val_blanco}`.
 * El frontend monta el `<select>` con `fnjs_construir_desplegable`.
 *
 * Sucesor de la rama `lst_propietarios` del dispatcher legacy
 * `apps/actividadplazas/controller/gestion_plazas_ajax.php`.
 */
final class PosiblesPropietariosData
{
    public function __construct(
        private ResumenPlazasService $resumenPlazasService,
    ) {
    }

    /**
     * @param array{id_nom?: int|string, id_activ?: int|string} $input
     * @return array{
     *     id: string,
     *     opciones: array<string,string>,
     *     selected: string,
     *     blanco: bool,
     *     val_blanco: string
     * }|array{error: string}
     */
    public function execute(array $input): array
    {
        $id_nom = input_int($input, 'id_nom');
        $id_activ = input_int($input, 'id_activ');

        if ($id_nom === 0 || $id_activ === 0) {
            return ['error' => (string)_("faltan parametros id_nom / id_activ")];
        }

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if (!is_object($oPersona)) {
            return ['error' => sprintf((string)_("No se encuentra persona con id_nom %d"), $id_nom)];
        }

        $obj_pau = str_replace("personas\\model\\entity\\", '', get_class($oPersona));
        $dl_de_paso = false;
        if ($obj_pau === 'PersonaEx') {
            $dl = $oPersona->getDl();
            if (is_string($dl) && $dl !== '') {
                $dl_de_paso = $dl;
            }
        }

        $propietario = ConfigGlobal::mi_delef() . '>' . ($dl_de_paso === false ? '' : $dl_de_paso);

        $this->resumenPlazasService->setId_activ($id_activ);
        $opciones = $this->resumenPlazasService->getPosiblesPropietariosOpciones($dl_de_paso);

        return [
            'id' => 'propietario',
            'opciones' => $opciones,
            'selected' => $propietario,
            'blanco' => true,
            'val_blanco' => '',
        ];
    }
}
