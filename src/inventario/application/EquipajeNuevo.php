<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Crear un equipaje para las actividades elegidas de una casa (`equipajes_nuevo_guardar`).
 *
 * Sucesor de la lógica del controlador (fase 5, paso 5.2). Corrige: una fecha mal escrita se guardaba
 * como vacía sin avisar; ahora se devuelve el error y no se crea el equipaje.
 */
final class EquipajeNuevo
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
    ) {
    }

    /** @return string texto de error ('' si todo fue bien) */
    public function execute(int $idUbiActiv, string $nombre, string $idsActiv, string $fIni, string $fFin, string $lugar): string
    {
        $fechas = [];
        foreach (['ini' => $fIni, 'fin' => $fFin] as $clave => $texto) {
            $fecha = $texto === '' ? null : DateTimeLocal::createFromLocal($texto);
            if ($fecha !== null && !$fecha instanceof DateTimeLocal) {
                return sprintf(_("Fecha no válida: %s"), $texto);
            }
            $fechas[$clave] = $fecha;
        }
        $oEquipaje = new Equipaje();
        $oEquipaje->setId_equipaje($this->equipajeRepository->getNewId());
        $oEquipaje->setIds_activ($idsActiv);
        $oEquipaje->setLugarVo($lugar);
        $oEquipaje->setF_ini($fechas['ini']);
        $oEquipaje->setF_fin($fechas['fin']);
        $oEquipaje->setId_ubi_activ($idUbiActiv);
        $oEquipaje->setNom_equipaje($nombre);
        if ($this->equipajeRepository->Guardar($oEquipaje) === false) {
            return _("hay un error, no se ha guardado") . "\n" . $this->equipajeRepository->getErrorTxt();
        }

        return '';
    }
}
