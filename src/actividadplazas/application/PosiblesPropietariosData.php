<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\personas\domain\entity\Persona;

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
    public static function execute(array $input): array
    {
        $id_nom = (int)($input['id_nom'] ?? 0);
        $id_activ = (int)($input['id_activ'] ?? 0);

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
            $dl_de_paso = $oPersona->getDl();
        }

        $propietario = ConfigGlobal::mi_delef() . '>' . ($dl_de_paso === false ? '' : $dl_de_paso);

        /** @var ResumenPlazasService $gesActividadPlazas */
        $gesActividadPlazas = $GLOBALS['container']->get(ResumenPlazasService::class);
        $gesActividadPlazas->setId_activ($id_activ);
        $opciones = $gesActividadPlazas->getPosiblesPropietariosOpciones($dl_de_paso);

        return [
            'id' => 'propietario',
            'opciones' => $opciones,
            'selected' => $propietario,
            'blanco' => true,
            'val_blanco' => '',
        ];
    }
}
