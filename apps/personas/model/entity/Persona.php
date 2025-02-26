<?php

namespace personas\model\entity;

use core\ConfigGlobal;
use PDO;

/**
 * Fitxer amb la Classe que accedeix a la taula pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class Persona
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */
    private $path_ini;


    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

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


    public function buscarEnTodasRegiones($id_nom)
    {
        $aWhere = [];
        $aWhere['situacion'] = 'A';
        $aWhere['id_nom'] = $id_nom;

        $a_lista = [];
        $oGesPersonasDl = new GestorPersonaDl();
        foreach ($this->posiblesEsquemas() as $esquema) {
            $oDB = $this->conexion($esquema);
            if ($esquema === 'restov') {
                $oGesPersonasEx = new GestorPersonaEx();
                $oGesPersonasEx->setoDbl($oDB);
                $cPersonasDl = $oGesPersonasEx->getPersonasEx($aWhere);
            } else {
                $oGesPersonasDl->setoDbl($oDB);
                $cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere);
            }
            foreach ($cPersonasDl as $oPersonaDl) {
                $oPersonaDl->setoDbl($oDB);
                $ape_nom = $oPersonaDl->getPrefApellidosNombre();
                $nombre = $oPersonaDl->getNom();
                $dl_persona = $oPersonaDl->getDl();
                $apellido1 = $oPersonaDl->getApellido1();
                $apellido2 = $oPersonaDl->getApellido2();
                $situacion = $oPersonaDl->getSituacion();
                $a_lista[] = [
                    'esquema' => $esquema,
                    'id_nom' => $id_nom,
                    'ape_nom' => $ape_nom,
                    'nombre' => $nombre,
                    'dl_persona' => $dl_persona,
                    'apellido1' => $apellido1,
                    'apellido2' => $apellido2,
                    'situacion' => $situacion,
                ];
            }
            $this->restaurarConexion($oDB);
        }
        return $a_lista;
    }

    private function posiblesEsquemas()
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

    private function conexion($esquema)
    {
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        if (ConfigGlobal::mi_region_dl() == $esquema) {
            //Utilizo la conexión oDB para cambiar momentáneamente el search_path.
            $oDB = $GLOBALS['oDB'];
        } else {
            // Sólo funciona con la conexión oDBR porque el usuario es orbixv que
            // tiene permiso de lectura para todos los esquemas
            $oDB = $GLOBALS['oDBR'];
        }
        $qRs = $oDB->query('SHOW search_path');
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        $this->path_ini = $aPath['search_path'];
        $oDB->exec('SET search_path TO public,"' . $esquema . '"');
        return $oDB;
    }

    private function restaurarConexion($oDB)
    {
        // Volver oDBR a su estado original:
        $oDB->exec("SET search_path TO $this->path_ini");
    }
}
