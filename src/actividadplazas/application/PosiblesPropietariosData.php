<?php

namespace src\actividadplazas\application;

use src\shared\config\ConfigGlobal;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\entity\Persona;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\domain\helpers\FuncTablasSupport;

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
     *     opciones: list<array{0: string, 1: string}>,
     *     selected: string,
     *     blanco: bool,
     *     val_blanco: string
     * }|array{error: string}
     */
    public function execute(array $input): array
    {
        $id_nom = FuncTablasSupport::inputInt($input, 'id_nom');
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');

        if ($id_nom === 0 || $id_activ === 0) {
            return ['error' => (string)_("faltan parametros id_nom / id_activ")];
        }

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if (!is_object($oPersona)) {
            return ['error' => sprintf((string)_("No se encuentra persona con id_nom %d"), $id_nom)];
        }

        $obj_pau = PersonaRepositoryResolver::objPauFromInstance($oPersona);
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
            'opciones' => OpcionesDesplegable::enOrden($opciones),
            'selected' => $propietario,
            'blanco' => true,
            'val_blanco' => '',
        ];
    }
}
