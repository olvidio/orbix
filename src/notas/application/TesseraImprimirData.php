<?php

declare(strict_types=1);

namespace src\notas\application;

use src\notas\application\Tesera;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\RegionStgrAviso;

/**
 * Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).
 */
final class TesseraImprimirData
{
    /**
     * @return array<string, mixed>
     */
    public static function execute(int $id_nom): array
    {
        if ($id_nom <= 0) {
            return ['aviso' => RegionStgrAviso::mensajePersonaNoValida()];
        }

        $problemasRegionStgr = [];
        $oPersona = Persona::findPersonaEnGlobal($id_nom, $problemasRegionStgr);
        if ($oPersona === null) {
            return ['aviso' => sprintf(_('No encuentro persona con id_nom: %s'), (string)$id_nom)];
        }
        if ($oPersona->getId_schema() === 0) {
            RegionStgrAviso::registrarPersonaSinSchema(
                $problemasRegionStgr,
                $id_nom,
                (string)$oPersona->getPrefApellidosNombre(),
                (string)($oPersona->getDl() ?? ''),
            );

            return ['aviso' => RegionStgrAviso::formatear($problemasRegionStgr)];
        }

        $tesera = new Tesera();
        $plan = $tesera->getPlan($id_nom);
        $cAsignaturas = $tesera->getAsignaturasPosibles($plan);
        $aAprobadas = $tesera->getAsignaturasAprobadas($id_nom, $plan);

        $outAsigs = [];
        foreach ($cAsignaturas as $oAsig) {
            $outAsigs[] = [
                'id_nivel' => (int)$oAsig->getId_nivel(),
                'nombre_asignatura' => $oAsig->getNombre_asignatura(),
                'id_asignatura' => (int)$oAsig->getId_asignatura(),
            ];
        }

        $outApr = [];
        foreach ($aAprobadas as $k => $row) {
            $fecha = $row['fecha'];
            unset($row['fecha']);
            $fechaLocal = '';
            if ($fecha instanceof DateTimeLocal) {
                $fechaLocal = $fecha->getFromLocal();
            }
            $row['fecha_local'] = $fechaLocal;
            $outApr[$k] = $row;
        }

        return [
            'nom' => $oPersona->getNombreApellidos(),
            'plan' => $plan,
            'c_asignaturas' => $outAsigs,
            'a_aprobadas' => $outApr,
        ];
    }
}
