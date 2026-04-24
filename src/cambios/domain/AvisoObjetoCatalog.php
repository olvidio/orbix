<?php

namespace src\cambios\domain;

use core\ConfigGlobal;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\asistentes\domain\entity\Asistente;
use src\procesos\domain\entity\ActividadProcesoTarea;

/**
 * Catalogo de objetos/tablas sobre los que se pueden anotar y avisar
 * cambios.
 *
 * Metodos estaticos puros (registro, sin estado ni efectos). Vive en
 * `domain/` porque lo consume la entidad `Cambio` ademas de los data
 * builders de `application/`; ponerlo mas arriba violaria la direccion
 * de dependencia (domain no depende de application).
 *
 * Extraido de la legacy `cambios\model\GestorAvisoCambios` en la pasada
 * de refactor; la parte de escritura (`addCanvi`) se movio a
 * `src\cambios\application\RegistrarCambio`.
 */
final class AvisoObjetoCatalog
{
    /**
     * Objetos avisables y su etiqueta localizada, listos para un
     * desplegable.
     *
     * `ActividadProcesoTarea` aparece solo si la dl tiene instalado el
     * modulo `procesos`.
     *
     * @return array<string, string>  clave = nombre del objeto; valor = etiqueta traducida.
     */
    public static function getArrayObjetosPosibles(): array
    {
        $aNomTablas_obj = [
            'Actividad' => _("actividad"),
            'ActividadCargoSacd' => _("sacd"),
            'CentroEncargado' => _("ctr"),
            'ActividadCargoNoSacd' => _("cl"),
            'Asistente' => _("asistencias"),
        ];
        if (ConfigGlobal::is_app_installed('procesos')) {
            $aNomTablas_obj['ActividadProcesoTarea'] = _("fases actividad");
        }
        return $aNomTablas_obj;
    }

    /**
     * Nombre completo (FQCN) de la clase de entidad asociada al objeto.
     *
     * Se usa para instanciar la entidad y leer sus `DatosCampos` al
     * pintar descripciones legibles del cambio. Las variantes de
     * actividad (`ActividadDl`, `ActividadEx`, `ActividadAll`) y las de
     * asistente (`AsistenteDl`, `AsistenteEx`, `AsistenteOut`,
     * `AsistentePub`) se colapsan a la clase unificada correspondiente.
     *
     * Devuelve cadena vacia si el objeto no es conocido.
     */
    public static function getFullPathObj(string $obj_txt): string
    {
        switch ($obj_txt) {
            case 'Actividad':
            case 'ActividadDl':
            case 'ActividadEx':
            case 'ActividadAll':
                return ActividadAll::class;
            case 'ActividadCargoSacd':
                return ActividadCargo::class;
            case 'CentroEncargado':
                return CentroEncargado::class;
            case 'ActividadCargoNoSacd':
                // sin entidad dedicada — se conserva el valor de la legacy.
                return 'ActividadCargoNoSacd';
            case 'Asistente':
            case 'AsistenteDl':
            case 'AsistenteOut':
            case 'AsistenteEx':
            case 'AsistentePub':
                return Asistente::class;
            case 'ActividadProcesoTarea':
                return ActividadProcesoTarea::class;
        }
        return '';
    }
}
