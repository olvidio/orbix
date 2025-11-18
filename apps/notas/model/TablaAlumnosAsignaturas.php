<?php

namespace notas\model;

use notas\model\entity\GestorPersonaNotaDB;
use notas\model\entity\GestorPersonaNotaDlDB;
use personas\model\entity\GestorPersonaDl;
use src\asignaturas\application\repositories\AsignaturaRepository;
use src\notas\application\repositories\NotaRepository;
use web\Lista;

/**
 * Classe que
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class TablaAlumnosAsignaturas
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    private $a_delegacionesStgr = [];


    public function getTablaCr($a_dl)
    {

        // Asignaturas posibles:
        $AsignaturaRepository = new AsignaturaRepository();
        $aWhere = [];
        $aOperador = [];
        $aWhere['status'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        $a_cabeceras = [];
        $a_cabeceras[0] = _("n/a");
        $a_cabeceras[1] = _("stgr");
        $a_cabeceras[2] = _("centro");
        $a_cabeceras[3] = _("apellidos, nombre");
        $a = 3;
        foreach ($cAsignaturas as $oAsignatura) {
            $a++;
            $nom_asig = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos() ?? '';
            $a_cabeceras[$a] = "$nom_asig ($creditos)";
        }
        //todas
        $cAsignaturasTodas = $AsignaturaRepository->getAsignaturas(array('_ordre' => 'id_asignatura'));
        foreach ($cAsignaturasTodas as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
        }

        // array de id_situacion que corresponde a superada
        $NotaRepository = new NotaRepository();
        $a_notas_superada = $NotaRepository->getArrayNotasSuperadas();

        $aWhere = [];
        $aOperador = [];
        $aWhere['situacion'] = 'A';
        $aWhere['stgr'] = 'b|c1|c2';
        $aWhere['_ordre'] = 'dl,stgr,apellido1,nom';

        $aOperador['stgr'] = '~';

        $dl_txt = '';
        foreach ($a_dl as $id_dl) {
            $dl_txt .= empty($dl_txt) ? '' : ',';
            $dl_txt .= "'" . $this->a_delegacionesStgr[$id_dl] . "'";
        }
        $aWhere['dl'] = $dl_txt;
        $aOperador['dl'] = 'IN';


        $GesPersonas = new GestorPersonaDl();
        $cPersonas = $GesPersonas->getPersonasDl($aWhere, $aOperador);
        $GesPersonaNotas = new GestorPersonaNotaDB();
        $p = 0;
        $a_valores = [];
        foreach ($cPersonas as $oPersona) {
            $p++;
            $id_nom = $oPersona->getId_nom();
            $id_tabla = $oPersona->getId_tabla();
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $stgr = $oPersona->getStgr();
            $centro = $oPersona->getCentro_o_dl();
            $dl = $oPersona->getDl();

            $a_valores[$p][1] = $id_tabla;
            $a_valores[$p][2] = $stgr;
            $a_valores[$p][3] = $dl;
            $a_valores[$p][4] = $ap_nom;

            // Asignaturas cursadas:
            // Busco fin_bienio, cuadrienio
            $cFin = $GesPersonaNotas->getPersonaNotas(array('id_nom' => $id_nom, 'id_nivel' => 9990), array('id_nivel' => '>'));
            $fin_bienio = false;
            $fin_cuadrienio = false;
            foreach ($cFin as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                if ($id_asignatura == 9999) {
                    $fin_bienio = true;
                }
                if ($id_asignatura == 9998) {
                    $fin_cuadrienio = true;
                }
            }

            //$cPersonaNotas = $GesPersonaNotas->getPersonaNotasSuperadas($id_nom,'t');
            $cPersonaNotas = $GesPersonaNotas->getPersonaNotas(['id_nom' => $id_nom]);
            $aAprobadas = [];
            foreach ($cPersonaNotas as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                $id_nivel = $oPersonaNota->getId_nivel();
                $id_situacion = $oPersonaNota->getId_situacion();

                if ($id_asignatura > 3000) {
                    $id_nivel_asig = $id_nivel;
                } else {
                    $id_nivel_asig = $a_Asig_nivel[$id_asignatura];
                }

                // En el caso de las cursadas (id_situacion = 2) pongo 2.
                if ($id_situacion == Nota::CURSADA) {
                    $aAprobadas[$id_nivel_asig]['nota'] = 2;
                } elseif (in_array($id_situacion, $a_notas_superada)) {
                    $aAprobadas[$id_nivel_asig]['nota'] = '';
                }
            }


            $a = 4; // 1: id_tabla, 2: stgr, 3: centro, 4: ap_nom.
            foreach ($cAsignaturas as $oAsignatura) {
                $a++;
                $id_nivel = $oAsignatura->getId_nivel();
                if (!empty($aAprobadas[$id_nivel])) {
                    $a_valores[$p][$a] = $aAprobadas[$id_nivel]['nota'];
                } else {
                    $a_valores[$p][$a] = 1;
                }
                // borro las pendientes si ya está aprobado el bienio o cuadrienio
                if ($fin_bienio && $id_nivel < 2000) {
                    $a_valores[$p][$a] = '';
                }
                if ($fin_cuadrienio) {
                    $a_valores[$p][$a] = '';
                }
            }
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla("pendientes");
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);

        return $oTabla;
    }

    public function getTablaDl()
    {
        // Asignaturas posibles:
        $AsignaturaRepository = new AsignaturaRepository();
        $aWhere = [];
        $aOperador = [];
        $aWhere['status'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        $a_cabeceras = [];
        $a_cabeceras[0] = _("n/a");
        $a_cabeceras[1] = _("stgr");
        $a_cabeceras[2] = _("centro");
        $a_cabeceras[3] = _("apellidos, nombre");
        $a = 3;
        foreach ($cAsignaturas as $oAsignatura) {
            $a++;
            $nom_asig = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos() ?? '';
            $a_cabeceras[$a] = "$nom_asig ($creditos)";
        }
        //todas
        $cAsignaturasTodas = $AsignaturaRepository->getAsignaturas(array('_ordre' => 'id_asignatura'));
        foreach ($cAsignaturasTodas as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $a_Asig_status[$id_asignatura] = $oAsignatura->getStatus();
            $a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
        }

        // array de id_situacion que corresponde a superada
        $NotaRepository = new NotaRepository();
        $a_notas_superada = $NotaRepository->getArrayNotasSuperadas();

        $aWhere = [];
        $aOperador = [];
        $aWhere['situacion'] = 'A';
        $aWhere['stgr'] = 'b|c1|c2';
        $aWhere['_ordre'] = 'stgr,apellido1,nom';

        $aOperador['stgr'] = '~';

        $GesPersonas = new GestorPersonaDl();
        $cPersonas = $GesPersonas->getPersonasDl($aWhere, $aOperador);
        $GesPersonaNotas = new GestorPersonaNotaDlDB();
        $p = 0;
        $a_valores = [];
        foreach ($cPersonas as $oPersona) {
            $p++;
            $id_nom = $oPersona->getId_nom();
            $id_tabla = $oPersona->getId_tabla();
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $stgr = $oPersona->getStgr();
            $centro = $oPersona->getCentro_o_dl();

            $a_valores[$p][1] = $id_tabla;
            $a_valores[$p][2] = $stgr;
            $a_valores[$p][3] = $centro;
            $a_valores[$p][4] = $ap_nom;

            // Asignaturas cursadas:
            // Busco fin_bienio, cuadrienio
            $cFin = $GesPersonaNotas->getPersonaNotas(array('id_nom' => $id_nom, 'id_nivel' => 9990), array('id_nivel' => '>'));
            $fin_bienio = false;
            $fin_cuadrienio = false;
            foreach ($cFin as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                if ($id_asignatura == 9999) {
                    $fin_bienio = true;
                }
                if ($id_asignatura == 9998) {
                    $fin_cuadrienio = true;
                }
            }

            //$cPersonaNotas = $GesPersonaNotas->getPersonaNotasSuperadas($id_nom,'t');
            $cPersonaNotas = $GesPersonaNotas->getPersonaNotas(['id_nom' => $id_nom]);
            $aAprobadas = [];
            foreach ($cPersonaNotas as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                $id_nivel = $oPersonaNota->getId_nivel();
                $id_situacion = $oPersonaNota->getId_situacion();

                if ($id_asignatura > 3000) {
                    $id_nivel_asig = $id_nivel;
                } else {
                    $id_nivel_asig = $a_Asig_nivel[$id_asignatura];
                }

                // En el caso de las cursadas (id_situacion = 2) pongo 2.
                if ($id_situacion == Nota::CURSADA) {
                    $aAprobadas[$id_nivel_asig]['nota'] = 2;
                } elseif (in_array($id_situacion, $a_notas_superada)) {
                    $aAprobadas[$id_nivel_asig]['nota'] = '';
                }
            }


            $a = 4; // 1: id_tabla, 2: stgr, 3: centro, 4: ap_nom.
            foreach ($cAsignaturas as $oAsignatura) {
                $a++;
                $id_nivel = $oAsignatura->getId_nivel();
                if (!empty($aAprobadas[$id_nivel])) {
                    $a_valores[$p][$a] = $aAprobadas[$id_nivel]['nota'];
                } else {
                    $a_valores[$p][$a] = 1;
                }
                // borro las pendientes si ya está aprobado el bienio o cuadrienio
                if ($fin_bienio && $id_nivel < 2000) {
                    $a_valores[$p][$a] = '';
                }
                if ($fin_cuadrienio) {
                    $a_valores[$p][$a] = '';
                }
            }
        }

        $oTabla = new Lista();
        $oTabla->setId_tabla("pendientes");
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);

        return $oTabla;
    }

    public function getA_delegacionesStgr()
    {
        return $this->a_delegacionesStgr;
    }

    public function setA_delegacionesStgr($a_delegacionesStgr)
    {
        $this->a_delegacionesStgr = $a_delegacionesStgr;
    }

}