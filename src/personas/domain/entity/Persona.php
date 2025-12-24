<?php

namespace src\personas\domain\entity;

use core\ConfigGlobal;
use PDO;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;

class Persona
{
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function findPersonaEnGlobal($id_nom): ?PersonaGlobal
    {
        // para sustituir aNewPersona
        // Mostrar el primero que encuentro. Empiezo por la propia dl, los de paso y el resto
        $aWhere = ['id_nom' => $id_nom, 'situacion' => 'A'];
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $cPersonas = $PersonaDlRepository->getPersonasDl($aWhere);
        if (count($cPersonas) > 0 && $cPersonas[0] !== null) {
            return $cPersonas[0];
        }
        $PersonaPubRepository = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
        $cPersonas = $PersonaPubRepository->getPersonas($aWhere);
        if (count($cPersonas) > 0 && $cPersonas[0] !== null) {
            return $cPersonas[0];
        }
        /*
        // Cuando busco en todas las regiones, queda mal la conexión y debo forzar la recuperación.
        $path_ini = self::conexion(ConfigGlobal::mi_region_dl(), $oDB);
        $cPersonas = self::buscarEnTodasRegiones($id_nom);
        if (count($cPersonas) > 1) {
            // más de una persona, devolver la que está en mi dl.
            foreach ($cPersonas as $oPersona) {
                if ($oPersona->getDl() === ConfigGlobal::mi_dele()) {
                    return $oPersona;
                }
            }
        }
        self::restaurarConexion($oDB, $path_ini);
        $PersonaDlRepository->setoDbl($oDB);
        */
        return $cPersonas[0] ?? null;
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

    public static function buscarEnTodasRegiones($id_nom)
    {
        $aWhere = [];
        $aWhere['situacion'] = 'A';
        $aWhere['id_nom'] = $id_nom;

        $aResultados = [];
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        foreach (self::posiblesEsquemas() as $esquema) {
            $path_ini = self::conexion($esquema, $oDB);
            if ($esquema === 'restov') {
                $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
                $resultado = $PersonaExRepository->getPersonas($aWhere);
            } else {
                $PersonaDlRepository->setoDbl($oDB);
                $resultado = $PersonaDlRepository->getPersonasDl($aWhere);
            }
            if (!empty($resultado)) {
                $aResultados[] = $resultado;
            }
            self::restaurarConexion($oDB, $path_ini);
        }
        return !empty($aResultados) ? array_merge(...$aResultados) : [];
    }

    private static function posiblesEsquemas()
    {
        // posibles esquemas
        /*
         * @todo: filtrar por regiones?
         */
        $oDBR = $GLOBALS['oDBR'];
        $qRs = $oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        $aResultSql = $qRs->fetchAll(\PDO::FETCH_ASSOC);
        $aEsquemas = $aResultSql;
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        $oDBR = $GLOBALS['oDBR'];
        $qRs = $oDBR->query('SHOW search_path');
        $aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
        $path_org = addslashes($aPath['search_path']);
        $a_posibles = [];
        foreach ($aEsquemas as $esquemaName) {
            $esquema = $esquemaName['schemaname'];
            //elimino el de H-H
            if (strpos($esquema, '-')) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
                if ($reg === $dl) {
                    continue;
                }
            }
            //elimino public, publicv, global
            if ($esquema === 'global') {
                continue;
            }
            if ($esquema === 'public') {
                continue;
            }
            if ($esquema === 'publicv') {
                continue;
            }
            $a_posibles[] = $esquema;
        }
        return $a_posibles;
    }

    private static function conexion($esquema, &$oDB)
    {
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        if (ConfigGlobal::mi_region_dl() === $esquema) {
            //Utilizo la conexión oDB para cambiar momentáneamente el search_path.
            $oDB = $GLOBALS['oDB'];
        } else {
            // Sólo funciona con la conexión oDBR porque el usuario es orbixv que
            // tiene permiso de lectura para todos los esquemas
            $oDB = $GLOBALS['oDBR'];
        }
        $qRs = $oDB->query('SHOW search_path');
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        $path_ini = $aPath['search_path'];
        $oDB->exec('SET search_path TO public,"' . $esquema . '"');
        return $path_ini;
    }

    private static function restaurarConexion($oDB, $path_ini)
    {
        // Volver oDBR a su estado original:
        $oDB->exec("SET search_path TO $path_ini");
    }
}
