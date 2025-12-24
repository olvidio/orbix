<?php
namespace dossiers\model;

use core\ConfigGlobal;
use src\personas\domain\entity\Persona;
use web\TiposActividades;
use function core\is_true;

/**
 * Classe per gestionar permisos de dossiers
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/1/2014
 */
class PermDossier
{

    static function permiso($r, $rw, $depende, $pau, $id_pau)
    {
        /*	$r = número referente al bit de lectura en decimal
			$rw = número referente al bit de lectura y escritura en decimal
			Se compara con el permiso que tengo; devuelve:
				1. Si no hay permisos,
				2. Si el permiso es de lectura y
				3. Si el permiso es de lectura/escritura.
			Los bits de las oficinas son los mismos que para el phplib (definidos en local.inc):
				adl(1),agd(2),des(8),est(16),scl(32),sg(64),sm(128),soi(256),sr(512),ss(1024),dtor(2048),ocs(4096)

		28.10.02	añado:  (para que tenga en cuenta la oficina)
			$depende = t o f (true, false) si se debe comprobar la oficina
			$pau = p,a,u según estemos hablando de personas, actividades o ubis
			$id_pau = el id correspondiente: id_nom, id_activ, id_ubi

		*/

        $userbits = $_SESSION['iPermMenus'];

        $lect = (($userbits & $r)); //true si tiene permiso de lectura
        $escr = (($userbits & $rw)); //true si tiene permiso de escritura

        $rta = 1;
        if ($lect && $r) {
            $rta = 2;
        }
        if ($escr && $rw) {
            $rta = 3;
        }

        if (is_true($depende) && $rta == 3 && $pau === "p") {
            // busco el id_tabla para saber de quién se trata y ver si es de mi oficina.
            $oPersona = Persona::findPersonaEnGlobal($id_pau);
            if (!is_object($oPersona)) {
                $msg_err = "<br>No encuentro a nadie con id_nom: $id_pau en  " . __FILE__ . ": line " . __LINE__;
                exit($msg_err);
            }
            $id_tabla = $oPersona->getId_tabla();
            switch ($id_tabla) {
                case "n":
                    if (!$_SESSION['oPerm']->have_perm_oficina('sm')) {
                        return 2;
                    }
                    break;
                case "x":
                    if (!$_SESSION['oPerm']->have_perm_oficina('nax')) {
                        return 2;
                    }
                    break;
                case "a":
                    if (!$_SESSION['oPerm']->have_perm_oficina('agd')) {
                        return 2;
                    }
                    break;
                case "s":
                    if (!$_SESSION['oPerm']->have_perm_oficina('sg')) {
                        return 2;
                    }
                    break;
                case "sssc":
                    if (!$_SESSION['oPerm']->have_perm_oficina('des')) {
                        return 2;
                    }
                    break;
                case "pn":
                    if (!$_SESSION['oPerm']->have_perm_oficina('sm')) {
                        return 2;
                    }
                    break;
                case "px":
                    if (!$_SESSION['oPerm']->have_perm_oficina('nax')) {
                        return 2;
                    }
                    break;
                case "pa":
                    if (!$_SESSION['oPerm']->have_perm_oficina('agd')) {
                        return 2;
                    }
                    break;
                case "psssc":
                    if (!$_SESSION['oPerm']->have_perm_oficina('des')) {
                        return 2;
                    }
                    break;
                default;
            }
        }
        return $rta;
    }

    function perm_activ_pers($id_tabla)
    {
        // Esta función devuelve un array con los permisos (si o no) para asignar las
        // actividades (según el tipo: nº) según el tipo de persona de que se trate y
        // quién seamos nosotros.

        $oTiposActividades = new TiposActividades();
        $a_posibles_tipos = $oTiposActividades->getId_tipoPosibles('^...'); // Que sólo devuelva los tres primeros dígitos

        //para no repetir los permisos comunes a sr,sg
        $sf = 2;
        $sv = ConfigGlobal::mi_sfsv();
        $ref_perm_sg = array(
            $sv . "11" => array('nom' => "crt n", 'perm' => 0),
            $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
            $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
            $sv . "41" => array('nom' => "crt s", 'perm' => 1),
            $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
            $sv . "12" => array('nom' => "ca n", 'perm' => 0),
            $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
            $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
            $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
            $sv . "14" => array('nom' => "cve n", 'perm' => 0),
            $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
            $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
            $sv . "43" => array('nom' => "cve s", 'perm' => 1),
            $sv . "51" => array('nom' => "sg crt", 'perm' => 1),
            $sv . "53" => array('nom' => "sg cv", 'perm' => 1),
            $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
            $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
            $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
        );
        $ref_perm_sr = array(
            $sv . "11" => array('nom' => "crt n", 'perm' => 0),
            $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
            $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
            $sv . "41" => array('nom' => "crt s", 'perm' => 0),
            $sv . "71" => array('nom' => "crt sr", 'perm' => 1),
            $sv . "12" => array('nom' => "ca n", 'perm' => 0),
            $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
            $sv . "32" => array('nom' => "cv agd", 'perm' => 0),
            $sv . "73" => array('nom' => "cv sr", 'perm' => 1),
            $sv . "14" => array('nom' => "cve n", 'perm' => 0),
            $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
            $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
            $sv . "43" => array('nom' => "cve s", 'perm' => 0),
            $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
            $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
            $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
            $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
            $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
        );
        $ref_perm_ss = array(
            $sv . "11" => array('nom' => "crt n", 'perm' => 1),
            $sv . "21" => array('nom' => "crt nax", 'perm' => 1),
            $sv . "31" => array('nom' => "crt agd", 'perm' => 1),
            $sv . "41" => array('nom' => "crt s", 'perm' => 1),
            $sv . "71" => array('nom' => "crt sr", 'perm' => 1),
            $sv . "12" => array('nom' => "ca n", 'perm' => 1),
            $sv . "22" => array('nom' => "ca nax", 'perm' => 1),
            $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
            $sv . "73" => array('nom' => "cv sr", 'perm' => 1),
            $sv . "14" => array('nom' => "cve n", 'perm' => 1),
            $sv . "23" => array('nom' => "cv nax", 'perm' => 1),
            $sv . "34" => array('nom' => "cve agd", 'perm' => 1),
            $sv . "43" => array('nom' => "cve s", 'perm' => 1),
            $sv . "51" => array('nom' => "sg crt", 'perm' => 1),
            $sv . "53" => array('nom' => "sg cv", 'perm' => 1),
            $sv . "61" => array('nom' => "crt sss+", 'perm' => 1),
            $sv . "63" => array('nom' => "cv sss+", 'perm' => 1),
            $sv . "64" => array('nom' => "cve sss+", 'perm' => 1),
            $sf . ".." => array('nom' => "sf", 'perm' => 1)
        );

        $ref_perm = [];
        switch ($id_tabla) {
            case "n" : //------------------------- numerarios -------------------
            case "pn":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_n = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 1),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 1),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 1),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 1),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 1),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 1),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 1),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 1),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 1),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 1),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 1),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 1),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 1),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 1),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_n);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_agd = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 1),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 1),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_agd);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sg);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sr);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm_est = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 1),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 1),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_est);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_ss);
                }
                break;
            case "a" : //------------------------- agregados -------------------
            case "pa":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_n = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 1),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 1),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 0),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 1),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_n);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_agd = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 1),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 1),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 1),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 1),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 1),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 1),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 1),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 1),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 1),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_agd);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sg);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sr);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm_est = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 1),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 1),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "32" => array('nom' => "sem inv", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 1),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_est);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_ss);
                }
                break;
            case "x" : //------------------------- nax -------------------
            case "px":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_n = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_n);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_agd = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_agd);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_x = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 1),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 1),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 1),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 1),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 1),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_x);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sg);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sr);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm_est = array(
                        $sv . "11" => array('nom' => "crt n", 'perm' => 0),
                        $sv . "21" => array('nom' => "crt nax", 'perm' => 0),
                        $sv . "31" => array('nom' => "crt agd", 'perm' => 0),
                        $sv . "41" => array('nom' => "crt s", 'perm' => 0),
                        $sv . "71" => array('nom' => "crt sr", 'perm' => 0),
                        $sv . "12" => array('nom' => "ca n", 'perm' => 0),
                        $sv . "22" => array('nom' => "ca nax", 'perm' => 0),
                        $sv . "33" => array('nom' => "cv agd", 'perm' => 0),
                        $sv . "73" => array('nom' => "cv sr", 'perm' => 0),
                        $sv . "14" => array('nom' => "cve n", 'perm' => 0),
                        $sv . "23" => array('nom' => "cv nax", 'perm' => 0),
                        $sv . "34" => array('nom' => "cve agd", 'perm' => 0),
                        $sv . "43" => array('nom' => "cve s", 'perm' => 0),
                        $sv . "51" => array('nom' => "sg crt", 'perm' => 0),
                        $sv . "53" => array('nom' => "sg cv", 'perm' => 0),
                        $sv . "61" => array('nom' => "crt sss+", 'perm' => 0),
                        $sv . "63" => array('nom' => "cv sss+", 'perm' => 0),
                        $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
                    );
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_est);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_ss);
                }
                break;
            case "s": //------------------------- supernumerarios -------------------
                if ($_SESSION['oPerm']->have_perm_oficina('agd') || $_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sg);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sr);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_sr);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_ss);
                }
                break;
            case "psssc":
            case "sssc": //------------------------- sss+ -------------------
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm = $this->addPerm($ref_perm, $ref_perm_ss);
                }
            default;
        }

        //$ref_perm = $ref_perm_sg;
        // Quito los tipos que no existen
        $ref_perm2 = [];
        foreach ($ref_perm as $key => $value) {
            if (!isset($a_posibles_tipos[$key])) { continue; }
            $ref_perm2[$key] = $value;
        }
        return $ref_perm2;
    }

    function perm_pers_activ($id_tipo_activ)
    {
        // Esta función devuelve un array con los permisos (si o no) para añadir las
        // personas (agd, n...) según el tipo de actividad de que se trate y
        // quién seamos nosotros.

        //para inicializar la matriz:
        $ref_perm = array(
            "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
            "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
            "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
            "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
            "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
            "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
            "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0),
            "sssc" => array('nom' => "sss+", 'obj' => "PersonaSSSC", 'perm' => 0),
            "psssc" => array('nom' => "sss+ de paso", 'obj' => "PersonaEx&na=sssc", 'perm' => 0),
        );

        $oTipoActiv = new TiposActividades($id_tipo_activ);
        $asistentes = $oTipoActiv->getAsistentesText();

        switch ($asistentes) {
            case "sss+" :
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "psssc" => array('nom' => "sss+ de paso", 'obj' => "PersonaEx&na=sssc", 'perm' => 1),
                        "sssc" => array('nom' => "sss+", 'obj' => "PersonaSSSC", 'perm' => 1),
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "n" :
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                // cv de cl + sem inv
                if ($_SESSION['oPerm']->have_perm_oficina('agd')
                    and (substr($id_tipo_activ, 0, 4) == "1123"
                        || $id_tipo_activ == "114025"
                        || $id_tipo_activ == "114026")
                ) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "agd":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0),
                        "sssc" => array('nom' => "sss+", 'obj' => "PersonaSSSC", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('est')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "s":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 1),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "nax":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 1),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 1),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "sg":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 1),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0),
                        "sssc" => array('nom' => "sss+", 'obj' => "PersonaSSSC", 'perm' => 1),
                        "psssc" => array('nom' => "sss+ de paso", 'obj' => "PersonaEx&na=sssc", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
            case "sr":
                if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('nax')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 1),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 0),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 0),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 1),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 0),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 0),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('sr')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 1),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 1),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                if ($_SESSION['oPerm']->have_perm_oficina('des')) {
                    $ref_perm_of = array(
                        "n" => array('nom' => "n", 'obj' => "PersonaN", 'perm' => 1),
                        "a" => array('nom' => "agd", 'obj' => "PersonaAgd", 'perm' => 1),
                        "s" => array('nom' => "s", 'obj' => "PersonaS", 'perm' => 0),
                        "x" => array('nom' => "nax", 'obj' => "PersonaNax", 'perm' => 0),
                        "pn" => array('nom' => "n de paso", 'obj' => "PersonaEx&na=n", 'perm' => 1),
                        "pa" => array('nom' => "agd de paso", 'obj' => "PersonaEx&na=a", 'perm' => 1),
                        "px" => array('nom' => "nax de paso", 'obj' => "PersonaEx&na=x", 'perm' => 0),
                        "sssc" => array('nom' => "sss+", 'obj' => "PersonaSSSC", 'perm' => 1),
                        "psssc" => array('nom' => "sss+ de paso", 'obj' => "PersonaEx&na=sssc", 'perm' => 1)
                    );
                    $ref_perm = $this->daniBoleanOr($ref_perm, $ref_perm_of);
                }
                break;
        }
        return $ref_perm;
    }

    /**
     * Hago un or logico de los permisos por si un usuario tienen permiso para más de una oficina
     * que se quede con el máximo de permisos.
     *
     *
     */
    function daniBoleanOr($ref_perm, $ref_perm_of)
    {
        $ref_perm_or = [];
        foreach ($ref_perm as $asis => $a) {
            if (isset($ref_perm_of[$asis])) {
                $b = $ref_perm_of[$asis];
            } else {
                $b = array('nom' => $a['nom'], 'obj' => $a['obj'], 'perm' => 0);
            }
            //$a = array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
            // Para asegurar:
            $perm_or = 0;
            if (($a['nom'] == $b['nom']) && ($a['obj'] == $b['obj'])) {
                $perm_or = $a['perm'] || $b['perm'];
            }
            $ref_perm_or[$asis] = array('nom' => $a['nom'], 'obj' => $a['obj'], 'perm' => $perm_or);
        }
        return $ref_perm_or;
    }

    /**
     * Para no sobreescribir los permisos. Aplico un logical OR
     *
     * @param array $ref_perm
     * @param array $ref_perm1
     * @return array
     */
    private function addPerm(array $ref_perm, array $ref_perm1)
    {
        if (empty($ref_perm)) {
            return $ref_perm1;
        }
        // ejemplo: $sv . "64" => array('nom' => "cve sss+", 'perm' => 0)
        $array_suma = $ref_perm + $ref_perm1; //array_merge no mantiene las claves numéricas
        $comun_keys = array_intersect_key($ref_perm, $ref_perm1);
        foreach ($comun_keys as $key => $value) {
            $a_perm = $ref_perm[$key];
            $val_perm = $a_perm['perm'];
            $a_perm1 = $ref_perm1[$key];
            $val_perm1 = $a_perm1['perm'];
            $val_perm_combined = $val_perm || $val_perm1;
            // cambio el valor
            $array_suma[$key]['perm'] = $val_perm_combined;
        }
        return $array_suma;
    }
}
