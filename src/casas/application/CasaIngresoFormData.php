<?php

namespace src\casas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\PermisosActividades;

/**
 * Data builder: datos para el formulario de ingreso de una actividad
 * (pantalla `casa_que`, ventana modal `que=form_ingreso`).
 */
final class CasaIngresoFormData
{
    public function __construct(
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private IngresoRepositoryInterface $ingresoRepository,
    ) {
    }

    /**
     * @param array{id_activ?: int|string} $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return ['ok' => false, 'error' => (string)_("Falta id_activ")];
        }

        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return ['ok' => false, 'error' => (string)_("Actividad no encontrada")];
        }
        $nom_activ = $oActividad->getNom_activ();
        $id_tipo_activ = (string) $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();
        $id_tarifa = $oActividad->getTarifa();
        $precio = $oActividad->getPrecio();

        $oPermSesion = $_SESSION['oPermActividades'] ?? null;
        $puede_modificar_tarifa = false;
        if ($oPermSesion instanceof PermisosActividades) {
            $oPermSesion->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPermTar = $oPermSesion->getPermisoActual('id_tarifa');
            $puede_modificar_tarifa = $oPermTar->have_perm_action('modificar');
        }

        $oTipoActiv = new TiposActividades($id_tipo_activ);
        $isfsv = $oTipoActiv->getSfsvId();

        $aOpcionesTarifa = [];
        $letra_tarifa = '';
        if ($puede_modificar_tarifa) {
            $aOpcionesTarifa = $this->tipoTarifaRepository->getArrayTipoTarifas($isfsv);
        } else {
            $letra_tarifa = '';
            if ($id_tarifa !== null) {
                $oTipoTarifa = $this->tipoTarifaRepository->findById($id_tarifa);
                $letra_tarifa = $oTipoTarifa?->getLetra() ?? '';
            }
        }

        $oIngreso = $this->ingresoRepository->findById($id_activ);
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
