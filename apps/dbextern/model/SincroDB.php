<?php

namespace dbextern\model;

use core\ConfigGlobal;
use dbextern\model\entity\GestorDlListas;
use dbextern\model\entity\GestorIdMatchPersona;
use dbextern\model\entity\GestorPersonaBDU;
use dbextern\model\entity\IdMatchPersona;
use dbextern\model\entity\PersonaBDU;
use PDO;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorTelecoPersonaDl;
use personas\model\entity\PersonaDl;
use personas\model\entity\TelecoPersonaDl;
use personas\model\entity\TrasladoDl;
use web\DateTimeLocal;

/**
 * Description of SincroDB
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class SincroDB
{

    private $tipo_persona;    //'n', 'a', 's', 'sssc'.
    private $id_tipo;        //1  ,  2,   3,    4.

    private $cPersonasListas;

    private $region;
    private $dl_listas;
    private $aCentros;

    private $aDlListas2Orbix;
    private $aDlOrbix2listas;
    private $path_ini;
    private $tabla;

    public function __construct()
    {
        //$this->tabla = 'dbo.q_dl_Estudios_b ';
        $this->tabla = 'tmp_bdu';
    }

    public function getTipo_persona()
    {
        return $this->tipo_persona;
    }

    public function getId_tipo()
    {
        return $this->id_tipo;
    }

    public function setTipo_persona($tipo_persona)
    {
        $this->tipo_persona = $tipo_persona;
        $id_tipo = 0;
        switch ($tipo_persona) {
            case 'n':
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $id_tipo = 1;
                }
                break;
            case 'a':
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $id_tipo = 2;
                }
                break;
            case 's':
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $id_tipo = 3;
                }
                break;
            case 'sssc':
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $id_tipo = 4;
                }
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
        $this->id_tipo = $id_tipo;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getDlListas()
    {
        return $this->dl_listas;
    }

    public function setDlListas($dl)
    {
        $this->dl_listas = $dl;
    }

    public function getCentros()
    {
        return $this->aCentros;
    }

    public function setCentros($aCentros)
    {
        $this->aCentros = $aCentros;
    }

    private function cargarArrayDl()
    {
        /* formato de dl en listas, tipo: Acscr (para Acsecr)
         * formato de dl en orbix: crAcse
         */
        $gesDllistas = new GestorDlListas();
        $cDllistas = $gesDllistas->getDlListas();
        $this->aDlListas2Orbix = [];
        $this->aDlOrbix2listas = [];
        foreach ($cDllistas as $oDlListas) {
            $dl_listas = $oDlListas->getDl();
            $nombre_dl = $oDlListas->getNombre_dl();

            preg_match('/(cr) (\w*)$/', $nombre_dl, $matches);
            if (!empty($matches[1])) {
                $reg = $matches[2];
                $dl_orbix = 'cr' . $reg; // 'crAcse'
            } else {
                $dl_orbix = 'dl' . $dl_listas;
            }

            $this->aDlListas2Orbix[$dl_listas] = $dl_orbix;
            $this->aDlOrbix2listas[$dl_orbix] = $dl_listas;
        }
    }

    public function dlListas2Orbix($dl_listas)
    {
        if (empty($this->aDlListas2Orbix)) {
            $this->cargarArrayDl();
        }

        if (empty($this->aDlListas2Orbix[$dl_listas])) {
            $msg = sprintf(_("No se encuentra la dl %s en la tabla Aux de listas"), $dl_listas);
            echo $msg;
            return FALSE;
        } else {
            return $this->aDlListas2Orbix[$dl_listas];
        }
    }

    public function dlOrbix2Listas($dl_orbix)
    {
        if (empty($this->aDlOrbix2listas)) {
            $this->cargarArrayDl();
        }

        if (empty($this->aDlOrbix2listas[$dl_orbix])) {
            $msg = sprintf(_("No se encuentra la dl %s en la tabla Aux de listas"), $dl_orbix);
            echo $msg;
            return FALSE;
        } else {
            return $this->aDlOrbix2listas[$dl_orbix];
        }
    }

    public function getPersonasBDU()
    {
        if (empty($this->cPersonasListas)) {
            $Query = "SELECT * FROM $this->tabla 
                        WHERE identif::text LIKE '$this->id_tipo%' AND  Dl='$this->dl_listas' 
                            AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
            // todos los de listas
            $gestorPersonaBDU = new GestorPersonaBDU();
            $cPersonasBDU = $gestorPersonaBDU->getPersonaBDUQuery($Query);

            // Añadir las delegaciones dependientes de la región (que no tienen esquema propio)
            if (array_key_exists($this->region, ConfigGlobal::REGIONES_CON_DL)) {
                $cPersonasBDU_n = [];
                foreach (ConfigGlobal::REGIONES_CON_DL[$this->region] as $dl_n) {

                    $Query = "SELECT * FROM $this->tabla
                          WHERE identif::text LIKE '$this->id_tipo%' AND  Dl='$dl_n'
                               AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
                    // todos los de listas
                    $cPersonasBDU_n[] = $gestorPersonaBDU->getPersonaBDUQuery($Query);

                }
                $cPersonasBDU = array_merge($cPersonasBDU, ...array_values($cPersonasBDU_n));
            }
            $this->cPersonasListas = $cPersonasBDU;
        }
        return $this->cPersonasListas;
    }

    public function union_automatico($oPersonaListas)
    {
        $id_nom_listas = $oPersonaListas->getIdentif();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento = $oPersonaListas->getFecha_Naci();
        $nombre = $oPersonaListas->getNombre();

        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = $this->tipo_persona;
        $aWhere['apellido1'] = $apellido1_sinprep;
        // Para los extranjeros que no tienen segundo apellido.
        if (!empty($apellido2_sinprep)) {
            $aWhere['apellido2'] = $apellido2_sinprep;
        }
        $aWhere['f_nacimiento'] = "'$f_nacimiento'";
        $aWhere['nom'] = trim($nombre);

        $oGesPersonasDl = new GestorPersonaDl();
        $cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere, $aOperador);
        if ($cPersonasDl !== false && count($cPersonasDl) == 1) {
            $oPersonaDl = $cPersonasDl[0];
            $id_nom = $oPersonaDl->getId_nom();

            $oIdMatch = new IdMatchPersona($id_nom_listas);
            $oIdMatch->setId_orbix($id_nom);
            $oIdMatch->setId_tabla($this->tipo_persona);

            if ($oIdMatch->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oIdMatch->getErrorTxt();
                print_r($oIdMatch);
                echo '<br>';
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Posibles coincidencias en la BDU
     *
     * @param integer $id_nom_orbix
     * @return string[][]
     */
    public function posiblesBDU(int $id_nom_orbix)
    {
        $oPersonaDl = new PersonaDl($id_nom_orbix);
        $oPersonaDl->DBCarregar();

        $apellido1 = $oPersonaDl->getApellido1();
        $apellido1 = str_replace("'","''", $apellido1);

        $Query = "SELECT * FROM $this->tabla
                        WHERE identif::text LIKE '$this->id_tipo%' AND  ApeNom LIKE '%" . $apellido1 . "%'
                            AND (pertenece_r='$this->region' OR compartida_con_r='$this->region') ";
        // todos los de listas
        $gestorPersonaBDU = new GestorPersonaBDU();
        $cPersonasBDU = $gestorPersonaBDU->getPersonaBDUQuery($Query);

        $i = 0;
        $a_lista_bdu = [];
        foreach ($cPersonasBDU as $oPersonaBDU) {
            $id_nom_listas = $oPersonaBDU->getIdentif();
            $oGesMatch = new GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
            if (!empty($cIdMatch[0]) and count($cIdMatch) > 0) {
                continue;
            }
            $id_nom_listas = $oPersonaBDU->getIdentif();
            $ape_nom = $oPersonaBDU->getApeNom();
            $nombre = $oPersonaBDU->getNombre();
            $apellido1 = $oPersonaBDU->getApellido1();
            $nx1 = $oPersonaBDU->getNx1();
            $apellido1_sinprep = $oPersonaBDU->getApellido1_sinprep();
            $nx2 = $oPersonaBDU->getNx2();
            $apellido2 = $oPersonaBDU->getApellido2();
            $apellido2_sinprep = $oPersonaBDU->getApellido2_sinprep();
            $f_nacimiento = $oPersonaBDU->getFecha_Naci();
            $dl_persona = $oPersonaBDU->getDl();
            $lugar_nacimiento = $oPersonaBDU->getLugar_Naci();
            $f_nacimiento = empty($f_nacimiento) ? '??' : $f_nacimiento;
            $pertenece_r = $oPersonaBDU->getPertenece_r();
            $compartida_con_r = $oPersonaBDU->getCompartida_con_r();
            $a_lista_bdu[$i] = [
                'id_nom' => $id_nom_listas,
                'ape_nom' => $ape_nom,
                'nombre' => $nombre,
                'dl_persona' => $dl_persona,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'f_nacimiento' => $f_nacimiento,
                'pertenece_r' => $pertenece_r,
                'compartida_con_r' => $compartida_con_r,
            ];
            $i++;
        }
        return $a_lista_bdu;
    }

    public function posiblesOrbixOtrasDl($id_nom_listas)
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
        $e = 0;
        foreach ($aEsquemas as $esquemaName) {
            $esquema = $esquemaName['schemaname'];
            //elimino el de H-H
            if (strpos($esquema, '-')) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
                if ($reg == $dl) {
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
            if ($esquema === 'restov') {
                continue;
            }
//			$esquema_slash = '"'.$esquema.'"';
//			$oDBR->exec("SET search_path TO public,$esquema_slash");
            // buscar en cada esquema
            $a_lista_orbix = $this->posiblesOrbix($id_nom_listas, $esquema);
            if (!empty($a_lista_orbix)) {
                $e++;
                $a_posibles[$e] = $a_lista_orbix;
            }
        }
        return $a_posibles;
    }

    public function posiblesOrbix($id_nom_listas, $esquema = '')
    {
        $oPersonaBDU = new PersonaBDU($id_nom_listas);
        $oPersonaBDU->DBCarregar();

        $apellido1_sinprep = $oPersonaBDU->getApellido1_sinprep();
        // Si tiene más de una palabra cojo la primera
        $tokens = explode(' ', $apellido1_sinprep);
        $apellido1_sinprep_c = $tokens[0];
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_tabla'] = $this->tipo_persona;
        $aWhere['situacion'] = 'A';
        $aWhere['apellido1'] = $apellido1_sinprep_c;
        $aOperador['apellido1'] = 'sin_acentos';
        $aWhere['_ordre'] = 'apellido1, apellido2, nom';

        $oGesPersonasDl = new GestorPersonaDl();
        if (!empty($esquema)) {
            $oDB = $this->conexion($esquema);
            $oGesPersonasDl->setoDbl($oDB);
        }
        $cPersonasDl = $oGesPersonasDl->getPersonasDl($aWhere, $aOperador);
        $i = 0;
        $a_lista_orbix = [];
        foreach ($cPersonasDl as $oPersonaDl) {
            if (!empty($esquema)) {
                $oPersonaDl->setoDbl($oDB);
            }
            $id_nom = $oPersonaDl->getId_nom();
            $oGesMatch = new GestorIdMatchPersona();
            $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix' => $id_nom));
            if (!empty($cIdMatch[0]) and count($cIdMatch) > 0) {
                continue;
            }
            $ape_nom = $oPersonaDl->getPrefApellidosNombre();
            $nombre = $oPersonaDl->getNom();
            $dl_persona = $oPersonaDl->getDl();
            $apellido1 = $oPersonaDl->getApellido1();
            $apellido2 = $oPersonaDl->getApellido2();
            $f_nacimiento = empty($oPersonaDl->getF_nacimiento()->getFromLocal()) ? '??' : $oPersonaDl->getF_nacimiento()->getFromLocal();
            $a_lista_orbix[$i] = array('esquema' => $esquema,
                'id_nom' => $id_nom,
                'ape_nom' => $ape_nom,
                'nombre' => $nombre,
                'dl_persona' => $dl_persona,
                'apellido1' => $apellido1,
                'apellido2' => $apellido2,
                'f_nacimiento' => $f_nacimiento);
            $i++;
        }
        if (!empty($esquema)) {
            $this->restaurarConexion($oDB);
        }
        return $a_lista_orbix;
    }


    function syncro($oPersonaListas, $id_orbix)
    {
        $msg = '';
        $oHoy = new DateTimeLocal();
        $a_ctr = $GLOBALS['a_centros'];

        $id_nom_listas = $oPersonaListas->getIdentif();
        $ape_nom = $oPersonaListas->getApeNom();
        $nombre = $oPersonaListas->getNombre();
        $apellido1 = $oPersonaListas->getApellido1();
        $nx1 = $oPersonaListas->getNx1();
        $apellido1_sinprep = $oPersonaListas->getApellido1_sinprep();
        $nx2 = $oPersonaListas->getNx2();
        $apellido2 = $oPersonaListas->getApellido2();
        $apellido2_sinprep = $oPersonaListas->getApellido2_sinprep();
        $f_nacimiento = $oPersonaListas->getFecha_Naci();
        $lugar_nacimiento = $oPersonaListas->getLugar_Naci();

        $dl_listas = $oPersonaListas->getDl();
        $Ctr = $oPersonaListas->getCtr();
        //por alguna razón puede no existir el centro en la lista
        if (!empty($a_ctr[$Ctr])) {
            $id_ubi = $a_ctr[$Ctr];
        } else {
            $id_ubi = 0;
            if (empty($Ctr)) {
                $msg = sprintf(_("parece que %s no tiene puesto el ctr en la BDU"), $ape_nom);
            } else {
                $msg = sprintf(_("no se encuentra el ctr %s en la lista de ctr"), $Ctr);
            }
        }

        $Email = $oPersonaListas->getEmail();
        $Tfno_Movil = $oPersonaListas->getTfno_Movil();

        $ce_num = $oPersonaListas->getCe_num();
        $ce_lugar = $oPersonaListas->getCe_lugar();
        $ce_ini = $oPersonaListas->getCe_ini();
        $ce_fin = $oPersonaListas->getCe_fin();

        $inc = $oPersonaListas->getInc();
        $f_inc = $oPersonaListas->getF_inc();
        $encargos = $oPersonaListas->getEncargos();
        $profesion = $oPersonaListas->getProfesion_cargo();
        $estudios = $oPersonaListas->getTitulo_estudios();


        $id_tipo_persona = substr($id_nom_listas, 0, 1);
        switch ($id_tipo_persona) {
            case '4': // sssc
                $obj_pau = 'PersonaSSSC';
                break;
            case '3': // supernumerarios
                $obj_pau = 'PersonaS';
                break;
            case '1': // numerarios
                $obj_pau = 'PersonaN';
                break;
            case '2': // agregados
                $obj_pau = 'PersonaAgd';
                break;
            case "p_nax":
                $obj_pau = 'PersonaNax';
                break;
        }
        $obj = 'personas\\model\\entity\\' . $obj_pau;
        $oPersona = new $obj($id_orbix);

        $oPersona->DBCarregar();
        //Las personas en listas siempre están en situación 'A'
        if ($oPersona->getSituacion() !== 'A') {
            $oPersona->setSituacion('A');
            $oPersona->setF_situacion($oHoy);
        }
        $oPersona->setNom($nombre);
        $oPersona->setNx1($nx1);
        $oPersona->setApellido1($apellido1_sinprep);
        $oPersona->setNx2($nx2);
        $oPersona->setApellido2($apellido2_sinprep);
        $oPersona->setF_nacimiento($f_nacimiento);
        $oPersona->setLugar_nacimiento($lugar_nacimiento);
        if ($id_tipo_persona != 4) {
            $oPersona->setCe($ce_num);
            $oPersona->setCe_lugar($ce_lugar);
            $oPersona->setCe_ini($ce_ini);
            $oPersona->setCe_fin($ce_fin);
        }
        $oPersona->setInc($inc);
        $oPersona->setF_inc($f_inc, FALSE);
        $oPersona->setProfesion($profesion);
        $oPersona->setEap($encargos);

        $dl_orbix = $this->dlListas2Orbix($dl_listas);
        $oPersona->setDl($dl_orbix);

        $oPersona->setId_ctr($id_ubi);


        if ($oPersona->DBGuardar() === false) {
            exit(_("hay un error, no se ha guardado"));
        }

        //Dossiers
        $GesTeleco = new GestorTelecoPersonaDl();
        // Telf movil  --particular(5)
        if (!empty($Tfno_Movil)) {
            $cTelecos = $GesTeleco->getTelecos(array('id_nom' => $id_orbix, 'tipo_teleco' => 'móvil', 'desc_teleco' => 5));
            if (!empty($cTelecos) && count($cTelecos) > 0) {
                $oTeleco = $cTelecos[0];
                $oTeleco->DBCarregar();
                $oTeleco->setNum_teleco($Tfno_Movil);
                $oTeleco->setObserv('de listas');
            } else {
                $oTeleco = new TelecoPersonaDl();
                $oTeleco->setId_nom($id_orbix);
                $oTeleco->setTipo_teleco('móvil');
                $oTeleco->setDesc_teleco(5);
                $oTeleco->setNum_teleco($Tfno_Movil);
                $oTeleco->setObserv('de listas');
            }
            if ($oTeleco->DBGuardar() === false) {
                echo(_("hay un error, no se ha guardado"));
            }
        }
        // e-mail   --principal(13)
        if (!empty($Email)) {
            $cTelecos = $GesTeleco->getTelecos(array('id_nom' => $id_orbix, 'tipo_teleco' => 'e-mail', 'desc_teleco' => 13));
            if (!empty($cTelecos) && count($cTelecos) > 0) {
                $oTeleco = $cTelecos[0];
                $oTeleco->DBCarregar();
                $oTeleco->setNum_teleco($Email);
                $oTeleco->setObserv('de listas');
            } else {
                $oTeleco = new TelecoPersonaDl();
                $oTeleco->setId_nom($id_orbix);
                $oTeleco->setTipo_teleco('e-mail');
                $oTeleco->setDesc_teleco(13);
                $oTeleco->setNum_teleco($Email);
                $oTeleco->setObserv('de listas');
            }
            if ($oTeleco->DBGuardar() === false) {
                echo(_("hay un error, no se ha guardado"));
            }

        }
        return $msg;
    }

    public function buscarEnOrbix($id_orbix)
    {
        $oTrasladoDl = new TrasladoDl();
        $a_esquemas = $oTrasladoDl->getEsquemas($id_orbix, $this->tipo_persona);
        $esquema = '';
        foreach ($a_esquemas as $info_eschema) {
            // array(schemaName,id_schema,situacion,f_situacion)
            if ($info_eschema['situacion'] === 'A') {
                $esquema = $info_eschema['schemaname'];
            }
        }
        return $esquema;
    }

    public function conexion($esquema)
    {
        $sfsv_txt = (ConfigGlobal::mi_sfsv() == 1) ? 'v' : 'f';
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

    public function restaurarConexion($oDB)
    {
        // Volver oDBR a su estado original:
        $oDB->exec("SET search_path TO $this->path_ini");
    }
}
