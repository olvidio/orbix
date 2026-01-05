<?php

namespace src\personas\domain\entity;

use src\personas\application\services\PersonaFinderService;

/**
 * @deprecated Esta clase está deprecada. Use PersonaFinderService en su lugar.
 *
 * Esta clase se mantiene temporalmente por compatibilidad con código legacy,
 * pero todos los métodos ahora delegan al servicio PersonaFinderService que
 * cumple con los principios de DDD (capa de aplicación).
 */
class Persona
{
    /**
     * @deprecated Use PersonaFinderService::findPersonaEnGlobal() en su lugar
     *
     * Busca una persona por id_nom en el esquema global (local).
     * Este método es un wrapper temporal para mantener compatibilidad.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return PersonaGlobal|null
     */
    public static function findPersonaEnGlobal($id_nom): ?PersonaGlobal
    {
        $service = $GLOBALS['container']->get(PersonaFinderService::class);
        return $service->findPersonaEnGlobal($id_nom);
    }

    /*
    public static function NewPersona($id_nom)
    {

        // para poder buscar sacd desde la sf
        if (ConfigGlobal::mi_sfsv() == 2) {
            if (substr($id_nom, 0, 1) == 1) {
                $gesPersonaDl = new GestorPersonaSacd();
            }
        } else {
            $gesPersonaDl = new GestorPersonaDl();
        }
        $cPersonasDl = $gesPersonaDl->getPersonas(array('id_nom' => $id_nom, 'situacion' => 'A'));
        if (count($cPersonasDl) > 0) {
            $oPersona = $cPersonasDl[0];
        } else {
            $gesPersonaEx = new GestorPersonaEx();
            $cPersonasEx = $gesPersonaEx->getPersonasEx(array('id_nom' => $id_nom, 'situacion' => 'A'));
            if (count($cPersonasEx) > 0) {
                $oPersona = $cPersonasEx[0];
            } else {
                $gesPersonaIn = new GestorPersonaIn();
                $cPersonasIn = $gesPersonaIn->getPersonasIn(array('id_nom' => $id_nom, 'situacion' => 'A'));
                if (count($cPersonasIn) > 0) {
                    $oPersona = $cPersonasIn[0];
                } else {
                    //Puede ser que este buscando una personaDl con situacion != 'A'
                    $cPersonasDl = $gesPersonaDl->getPersonas(array('id_nom' => $id_nom));
                    if (count($cPersonasDl) > 0) {
                        $oPersona = $cPersonasDl[0];
                    } else {
                        // o de otra dl.
                        if ($id_nom > 0) {
                            $gesPersonaAll = new GestorPersonaAll();
                            $oPersona = $gesPersonaAll->getPersonaByIdNom($id_nom);
                            if (is_object($oPersona)) {
                                return $oPersona;
                            }
                        }

                        $gesPersonaIn = new GestorPersonaIn();
                        $cPersonasIn = $gesPersonaIn->getPersonasIn(array('id_nom' => $id_nom));
                        if (count($cPersonasIn) > 0) {
                            $oPersona = $cPersonasIn[0];
                        } else {
                            return sprintf(_("no encuentro a nadie con id: %s"), $id_nom);
                        }
                    }
                }
            }
        }
        return $oPersona;
    }
*/

    /**
     * @deprecated Use PersonaFinderService::buscarEnTodasRegiones() en su lugar
     *
     * Busca una persona en todas las regiones/esquemas disponibles.
     * Este método es un wrapper temporal para mantener compatibilidad.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return array Array de PersonaGlobal encontradas
     */
    public static function buscarEnTodasRegiones($id_nom): array
    {
        $service = $GLOBALS['container']->get(PersonaFinderService::class);
        return $service->buscarEnTodasRegiones($id_nom);
    }
}
