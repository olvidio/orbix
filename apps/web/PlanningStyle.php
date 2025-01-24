<?php

namespace web;

 use asistentes\model\entity\Asistente;

 class PlanningStyle
{
 /**
     *Es para no volver a escribir todo en la función select.
     *Sirve para seleccionar el color en función del tipo de actividad: sv, sf, resto
     */
    public static function clase($id_tipo_activ, $propio, $plaza, $status)
    {
        $svsf = (integer)substr($id_tipo_activ, 0, 1);
        switch ($svsf){
            case 1:
                $clase = "actsv";
                break;
            case 2:
                $clase = "actsf";
                break;
            default:
                $clase = "actotras";
        }
        // sobreescribo
        if ($propio === TRUE) {
            $clase = 'actpropio';
        }
        if ($propio === "p") {
            $clase = 'actpersonal';
        }
        if (!empty($plaza) && $plaza < Asistente::PLAZA_ASIGNADA) {
            $clase = 'provisional ' . $clase;
        }
        if (!empty($status) && $status === 1) {
            $clase = 'proyecto ' . $clase;
            if ($svsf === 2) {
                $clase = 'proyectof ' . $clase;
            }
        }
        return $clase;
    }
}