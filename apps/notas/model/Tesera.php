<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace notas\model;

use core\ViewPhtml;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use web\Posicion;
use function core\is_true;

/**
 * Description of tessera
 *
 * @author Daniel Serrabou <dani@moneders.net>
 */
class Tesera
{

    private $id_nom;
    private string $curso_txt;
    private DateTimeLocal $oInicio;
    private DateTimeLocal $oFin;

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
        $this->oInicio = new DateTimeLocal($inicio);
        $this->oFin = new DateTimeLocal($fin);
    }

    private function getTitulo($id_nivel)
    {
        $html = "";
        switch ($id_nivel) {
            case 1101:
                $html = '<tr><td colspan="3" style="text-align: center;"><h3>'
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
                $html = '<tr><td><br></td></tr> <tr><td colspan="4" style="text-align: center;"><h3>'
                    . ucfirst(_("teología")) .
                    '</h3></td></tr> <tr><td colspan="3"><b>'
                    . ucfirst(_("año")) . ' I</b></td></tr>
					';
                break;
            case 2201:
                //pruebo de cerrar la tabla anidada -->
                $html = '</table></td>
				<td class="border semi">
				<table style="width: 100%">';

                $html .= '<tr><td colspan="3" style="text-align: center;"><h3>'
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

    public function getAsignaturasPosibles(int $plan = 26)
    {
        $this->getCurso();

        // Asignaturas posibles:
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['active'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        if ($plan === 97) {
            // desaparece el id_nivel 2114 y aparecen id_nivel 2112, 2113
            // tampoco debería haber ninguna opcional de las nuevas, pero se supone que se hacongelado
            // antes de crear las opcionales.
            $aWhere = [];
            $aOperador = [];
            $aWhere['id_nivel'] = '2112,2113';
            $aOperador['id_nivel'] = 'IN';
            $aWhere['_ordre'] = 'id_nivel';
            $cAsignaturas97 = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);
            // quitar la 2114
            $cAsignaturasNew = [];
            foreach ($cAsignaturas as $k => $oAsignatura) {
                if ($oAsignatura->getId_nivel() === 2114) {
                    // añado las viejas, para que esten en orden
                    $cAsignaturasNew = array_merge($cAsignaturasNew, $cAsignaturas97);
                    continue;
                }
                $cAsignaturasNew[] = $oAsignatura;
            }
            $cAsignaturas = $cAsignaturasNew;
        }
        return $cAsignaturas;
    }

    public function getAsignaturasAprobadas(int $id_nom, int $plan = 26)
    {
        // Asignaturas cursadas:
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $cNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
        $aAprobadas = [];
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        foreach ($cNotas as $oPersonaNota) {
            $id_asignatura = $oPersonaNota->getId_asignatura();
            $id_nivel = $oPersonaNota->getIdNivelVo()->value();
            $acta = $oPersonaNota->getActaVo()?->value();
            $oF_acta = $oPersonaNota->getF_acta();
            $id_situacion = $oPersonaNota->getId_situacion();
            $bAprobada = $oPersonaNota->isAprobada();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            if ($id_asignatura > 3000) {
                $id_nivel_asig = $id_nivel;
            } else {
                if ($plan === 97) {
                    if ($id_nivel === 2114) {
                        continue;
                    }
                } else {
                    if (!$oAsignatura->isActive()) {
                        continue;
                    }
                }
                $id_nivel_asig = $oAsignatura->getId_nivel();
            }
            $n = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel_asig'] = $id_nivel_asig;
            $aAprobadas[$n]['id_nivel'] = $id_nivel;
            $aAprobadas[$n]['id_asignatura'] = $id_asignatura;
            $aAprobadas[$n]['nombre_asignatura'] = $oAsignatura->getNombreAsignaturaVo()->value();
            $aAprobadas[$n]['nombre_corto'] = $oAsignatura->getNombre_corto();
            $aAprobadas[$n]['fecha'] = $oF_acta;
            $aAprobadas[$n]['id_situacion'] = $id_situacion;
            $aAprobadas[$n]['bAprobada'] = $bAprobada;
            //$aAprobadas[$n]['nota']= $oNota->getDescripcion();
            $nota = $oPersonaNota->getNota_txt();
            $aAprobadas[$n]['nota'] = $nota;
            $aAprobadas[$n]['acta'] = $acta; // para imprimir
        }
        ksort($aAprobadas);

        return $aAprobadas;
    }

    public function verTesera($id_nom)
    {

        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $centro = $oPersona->getCentro_o_dl();

        $plan = $this->getPlan($id_nom);
        $cAsignaturas = $this->getAsignaturasPosibles($plan);
        // Para saber el número total de asignaturas
        $aAprobadas = $this->getAsignaturasAprobadas($id_nom, $plan);
        $num_asig_total = count($cAsignaturas);
        $num_creditos_total = 0;

        // array con los id_situacion correspondientes a notas 'superadas'
        $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
        $aIdSuperadas = $NotaRepository->getArrayNotasSuperadas(); // Ojo la numéricas

        $a = 0;
        $i = 0;
        $numasig = 0;
        $numcred = 0;
        $numasig_year = 0;
        $numcred_year = 0;
        reset($aAprobadas);
        $tabla = [];
        $tabla_dcha = [];
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
            }
        }

        $oPosicion = new Posicion();

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

        $oView = new ViewPhtml(__NAMESPACE__);
        $oView->renderizar('tesera_ver.phtml', $a_campos);
    }

    public function getPlan($id_nom)
    {
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_asignatura'] = 9998; //cuadrienio terminado
        $cNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere, $aOperador);
        if (count($cNotas) === 0) {
            return 26;
        }
        $oF_acta = $cNotas[0]->getF_acta();
        $oFechaLimite = new DateTimeLocal('2026-03-30');

        if ($oF_acta < $oFechaLimite) {
            return 97;
        }
        return 26;
    }
}
