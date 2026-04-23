<?php

namespace src\casas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use web\TiposActividades;

/**
 * Data builder: datos para el formulario de ingreso de una actividad
 * (pantalla `casa_que`, ventana modal `que=form_ingreso`).
 */
final class CasaIngresoFormData
{
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return ['ok' => false, 'error' => (string)_("Falta id_activ")];
        }

        $TipoTarifa = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $ActividadAll = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $Ingreso = $GLOBALS['container']->get(IngresoRepositoryInterface::class);

        $oActividad = $ActividadAll->findById($id_activ);
        if ($oActividad === null) {
            return ['ok' => false, 'error' => (string)_("Actividad no encontrada")];
        }
        $nom_activ = $oActividad->getNom_activ();
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();
        $id_tarifa = $oActividad->getTarifa();
        $precio = $oActividad->getPrecio();

        $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
        $oPermTar = $_SESSION['oPermActividades']->getPermisoActual('id_tarifa');

        $oTipoActiv = new TiposActividades($id_tipo_activ);
        $isfsv = $oTipoActiv->getSfsvId();

        $puede_modificar_tarifa = $oPermTar->have_perm_action('modificar');
        $aOpcionesTarifa = [];
        $letra_tarifa = '';
        if ($puede_modificar_tarifa) {
            $aOpcionesTarifa = $TipoTarifa->getArrayTipoTarifas($isfsv);
        } else {
            $oTipoTarifa = $TipoTarifa->findById($id_tarifa);
            $letra_tarifa = $oTipoTarifa?->getLetra() ?? '';
        }

        $oIngreso = $Ingreso->findById($id_activ);
        $ingresos = $oIngreso?->getIngresosVo()?->value() ?? 0.0;
        $num_asistentes = $oIngreso?->getNumAsistentesVo()?->value() ?? 0;
        $observ = $oIngreso?->getObservVo()?->value() ?? '';

        return [
            'ok' => true,
            'id_activ' => $id_activ,
            'nom_activ' => (string)$nom_activ,
            'id_tarifa' => (string)$id_tarifa,
            'letra_tarifa' => (string)$letra_tarifa,
            'puede_modificar_tarifa' => $puede_modificar_tarifa,
            'a_opciones_tarifa' => $aOpcionesTarifa,
            'precio' => $precio,
            'ingresos' => $ingresos,
            'num_asistentes' => $num_asistentes,
            'observ' => (string)$observ,
        ];
    }
}
