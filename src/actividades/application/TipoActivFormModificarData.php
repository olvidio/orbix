<?php

namespace src\actividades\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\security\HashB;

/**
 * Datos del formulario para cambiar el nombre de un tipo de actividad o eliminarlo: los textos del tipo y las
 * cápsulas de `tipo_activ_update` y `tipo_activ_eliminar`. El formulario lo monta
 * `frontend/actividades/controller/tipo_activ_form_modificar.php`.
 */
final class TipoActivFormModificarData
{
    /**
     * @param array<string, mixed> $input
     * @return array{id_tipo_activ: int, nom_actividad: string, nom_tipo: string, ctx_guardar: string, ctx_eliminar: string}|array{error: string}
     */
    public function execute(array $input = []): array
    {
        $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tipo_activ');
        if ($id_tipo_activ <= 0) {
            return ['error' => _("Tipo no válido")];
        }
        $oTiposActividades = new TiposActividades($id_tipo_activ);

        return [
            'id_tipo_activ' => $id_tipo_activ,
            'nom_actividad' => $oTiposActividades->getSfsvText()
                . ' ' . $oTiposActividades->getAsistentesText()
                . ' ' . $oTiposActividades->getActividadText(),
            'nom_tipo' => $oTiposActividades->getNom_tipoText(),
            'ctx_guardar' => HashB::sign('tipo_activ_update', ['id_tipo_activ' => $id_tipo_activ]),
            'ctx_eliminar' => HashB::sign('tipo_activ_eliminar', ['id_tipo_activ' => $id_tipo_activ]),
        ];
    }
}
