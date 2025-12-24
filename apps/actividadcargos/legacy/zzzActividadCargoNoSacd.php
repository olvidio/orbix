<?php

namespace actividadcargos\legacy;

/**
 * Clase que implementa la entidad d_cargos_activ amb restricció a només els sacd.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/04/2012
 */
class ActividadCargoNoSacd extends ActividadCargoAbstract
{

    /* de fet no té cap métode adicional (de moment), pero a l'hora de fer els avisos són dues classes diferents,
       i es poden donar permisos diferents...
    */
    public function printItem($string)
    {

    }


}