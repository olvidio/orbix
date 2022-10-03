<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace notas\model;

use asignaturas\model\entity as asignaturas;
use function core\is_true;
use core;
use web;
use personas\model\entity as personas;

/**
 * Description of tessera
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class Tesera
{

    private $id_nom;

    private function getCurso()
    {
        $ini_d = $_SESSION['oConfig']->getDiaIniStgr();
        $ini_m = $_SESSION['oConfig']->getMesIniStgr();
        $fin_d = $_SESSION['oConfig']->getDiaFinStgr();
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();

        $any = date('Y');
        $mes = date('m');

        if ($mes > $fin_m) {
            $any2 = $any - 1;
            $inicio = "$any2-$ini_m-$ini_d";
            $fin = "$any-$fin_m-$fin_d";
            $this->curso_txt = "$any2-$any";
        } else {
            $any2 = $any - 2;
            $any--;
            $inicio = "$any2-$ini_m-$ini_d";
            $fin = "$any-$fin_m-$fin_d";
            $this->curso_txt = "$any2-$any";
        }
        $this->oInicio = new web\DateTimeLocal($inicio);
        $this->oFin = new web\DateTimeLocal($fin);
    }

    private function getTitulo($id_nivel)
    {
        $html = "";
        switch ($id_nivel) {
            case 1101:
                //$html = '<tr><td colspan="3" align="CENTER"><h3>'
                $html = '<tr><td colspan="3" align="CENTER"><h3>'
                    . ucfirst(_("filosofía")) .
                    '</h3></td></tr> <tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' I</b></td></tr>
					';
                break;
            case 1201:
                $html = '<tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' II</b></td></tr>
					';
                break;
            case 2101:
                $html = '<tr><td><br></td></tr> <tr><td colspan="4" align="CENTER"><h3>'
                    . ucfirst(_("teología")) .
                    '</h3></td></tr> <tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' I</b></td></tr>
					';
                break;
            case 2201:
                //pruebo de cerrar la tabla anidada -->
                $html = '</table></td>
				<td valign="TOP" width="50%">
				<table class="semi">';

                $html .= '<tr><td colspan="3" align="CENTER"><h3>'
                    . ucfirst(_("teología")) .
                    '</h3></td></tr> <tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' II</b></td></tr>
					';
                break;
            case 2301:
                $html = '<tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' III</b></td></tr>
					';
                break;
            case 2401:
                $html = '<tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' IV</b></td></tr>
					';
                break;
        }
        return $html;
    }

    public function getVariasTesera()
    {


    }

    public function getAsignaturasPosibles()
    {
        $this->getCurso();
        // Asignaturas posibles:
        $GesAsignaturas = new asignaturas\GestorAsignatura();
        $aWhere = array();
        $aOperador = array();
        $aWhere['status'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere, $aOperador);

        return $cAsignaturas;
    }

    public function getAsignaturasAprobadas($id_nom)
    {
        // Asignaturas cursadas:
        $GesNotas = new entity\GestorPersonaNota();
        $aWhere = array();
        $aOperador = array();
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $cNotas = $GesNotas->getPersonaNotas($aWhere, $aOperador);
        $aAprobadas = array();
        foreach ($cNotas as $oPersonaNota) {
            $id_asignatura = $oPersonaNota->getId_asignatura();
            $id_nivel = $oPersonaNota->getId_nivel();
            $oF_acta = $oPersonaNota->getF_acta();
            $id_situacion = $oPersonaNota->getId_situacion();
            $bAprobada = $oPersonaNota->isAprobada();
            $oAsig = new asignaturas\Asignatura($id_asignatura);
            if ($id_asignatura > 3000) {
                $id_nivel_asig = $id_nivel;
            } else {
                if ($oAsig->getStatus() != 't') continue;
                $id_nivel_asig = $oAsig->getId_nivel();
            }
            $n = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel'] = $id_nivel;
            $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
            $aAprobadas[$n]['nombre_corto'] = $oAsig->getNombre_corto();
            $aAprobadas[$n]['fecha'] = $oF_acta;
            $aAprobadas[$n]['id_situacion'] = $id_situacion;
            $aAprobadas[$n]['bAprobada'] = $bAprobada;
            //$aAprobadas[$n]['nota']= $oNota->getDescripcion();
            $nota = $oPersonaNota->getNota_txt();
            $aAprobadas[$n]['nota'] = $nota;
        }
        ksort($aAprobadas);

        return $aAprobadas;
    }

    public function verTesera($id_nom)
    {

        $oPersona = personas\Persona::NewPersona($id_nom);
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $centro = $oPersona->getCentro_o_dl();

        $cAsignaturas = $this->getAsignaturasPosibles();
        // Para saber el número total de asignaturas
        $aAprobadas = $this->getAsignaturasAprobadas($id_nom);
        $num_asig_total = count($cAsignaturas);
        $num_creditos_total = 0;

        // array con los id_situacion correspondientes a notas 'superadas'
        $GesNotas = new entity\GestorNota();
        $aIdSuperadas = $GesNotas->getArrayNotasSuperadas(); // Ojo la numéricas

        $a = 0;
        $i = 0;
        $numasig = 0;
        $numcred = 0;
        $numasig_year = 0;
        $numcred_year = 0;
        reset($aAprobadas);
        $tabla = array();
        $tabla_dcha = array();
        while ($a < count($cAsignaturas)) {
            $oAsignatura = $cAsignaturas[$a++];
            $num_creditos_total += $oAsignatura->getCreditos();
            $row = current($aAprobadas);
            next($aAprobadas);
            if ($row === FALSE) {
                $i++;
                $tabla[$i]['titulo'] = $this->getTitulo($oAsignatura->getId_nivel());
                $tabla[$i]['asignatura'] = $oAsignatura->getNombre_corto();
                $tabla[$i]['nota'] = -1;
                $tabla[$i]['fecha'] = -1;
                $tabla[$i]['bAprobada'] = 'f';
                continue;
            }

            while (($oAsignatura->getId_nivel() < $row["id_nivel_asig"]) && ($row["id_nivel"] < 2434)) {
                $i++;
                $tabla[$i]['titulo'] = $this->getTitulo($oAsignatura->getId_nivel());
                $tabla[$i]['asignatura'] = $oAsignatura->getNombre_corto();
                $tabla[$i]['nota'] = -1;
                $tabla[$i]['fecha'] = -1;
                $tabla[$i]['bAprobada'] = 'f';
                $oAsignatura = $cAsignaturas[$a++];
                $num_creditos_total += $oAsignatura->getCreditos();
            }

            if ($oAsignatura->getId_nivel() == $row["id_nivel_asig"]) {
                $i++;
                $tabla[$i]['titulo'] = $this->getTitulo($oAsignatura->getId_nivel());
                // para las opcionales
                if ($row["id_asignatura"] > 3000 && $row["id_asignatura"] < 9000) {
                    $algo = $oAsignatura->getNombre_corto() . "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $row["nombre_corto"];
                    $tabla[$i]['asignatura'] = $algo;
                    $tabla[$i]['nota'] = $row['nota'];
                    $tabla[$i]['fecha'] = $row['fecha']->getFromLocal();
                } else {
                    $tabla[$i]['asignatura'] = $oAsignatura->getNombre_corto();
                    $tabla[$i]['nota'] = $row['nota'];
                    $tabla[$i]['fecha'] = $row['fecha']->getFromLocal();
                }
                $tabla[$i]['bAprobada'] = $row['bAprobada'];

                if (is_true($row['bAprobada'])) {
                    $numasig++;
                    $numcred += $oAsignatura->getCreditos();
                    $oFActa = $row['fecha'];

                    if ($this->oInicio <= $oFActa && $oFActa <= $this->oFin) {
                        $numasig_year++;
                        $numcred_year += $oAsignatura->getCreditos();
                    }
                }

                /*
                if (in_array($row['id_situacion'],$aIdSuperadas)) {
                    $numasig ++;
                    $numcred += $oAsignatura->getCreditos();
                    $oFActa = $row['fecha'];

                    if($this->oInicio <= $oFActa && $oFActa <= $this->oFin) {
                        $numasig_year ++;
                        $numcred_year += $oAsignatura->getCreditos();
                    }
                }
                */
            }

            /*
            if ($siguiente === FALSE) { // YA no hay más aprobadas:
                $i++;
                $tabla[$i]['titulo'] = $this->getTitulo($oAsignatura->getId_nivel());
                $tabla[$i]['asignatura'] = $oAsignatura->getNombre_corto();
                $tabla[$i]['nota'] = -1;
                $tabla[$i]['fecha'] = -1;
                $tabla[$i]['bAprobada'] = 'f';
                continue;
            }
              */
            /*
            if (!$row["id_nivel"]){
                $i++;
                $tabla[$i]['titulo'] = $this->getTitulo($oAsignatura->getId_nivel());
                $tabla[$i]['asignatura'] = $oAsignatura->getNombre_corto();
                $tabla[$i]['nota'] = -1;
                $tabla[$i]['fecha'] = -1;
                $tabla[$i]['bAprobada'] = 'f';
            }
            */
        }

        $oPosicion = new \web\Posicion();

        $a_campos = ['oPosicion' => $oPosicion,
            'ap_nom' => $ap_nom,
            'centro' => $centro,
            'tabla' => $tabla,
            'numasig' => $numasig,
            'num_asig_total' => $num_asig_total,
            'numasig_year' => $numasig_year,
            'numcred' => $numcred,
            'num_creditos_total' => $num_creditos_total,
            'curso_txt' => $this->curso_txt,
            'numcred_year' => $numcred_year,
        ];

        $oView = new core\View(__NAMESPACE__);
        return $oView->render('tesera_ver.phtml', $a_campos);
    }
}
