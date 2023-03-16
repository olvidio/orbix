<?php

namespace web;

use core;
use ubis\model\entity\GestorCasaPeriodo;
use asistentes\model\entity\Asistente;

/**
 * Esta página sólo tiene las funciones. Es para hacer incluir en la página que sea
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

/**
 * Función para dibujar un plannig.
 *
 *variables globales
 *    .Deben definirse en la página desde donde se llama a la función.
 *
 *    $colorColumnaUno    - color de fondo de las columnas de los dias pares
 *    $colorColumnaDos    - color de fondo de las columnas de los dias impares
 *    $table_border        - string para definir las características de la tabla
 *                            ej: "frame=border rules=all"
 *
 * @param integer $dd - 1 ó 3 divisiones por día. (en el caso 3 los periodos son: 0-10; 10-20; 20-24 )
 * @param string $cabecera - Es el título que se pone en la primera celda.
 * @param string $oInicio - fecha de inicio del planning. web\DateTimeLocal
 * @param string $oFin - fecha de final del planning. web\DateTimeLocal
 *
 * @param integer $mod - 0 ó nº. Para controlar si puede modificar las actividades.
 *                    Está por cuestiones de velocidad (tamaño de la página).
 *                    Si es 0, no se puede hacer click en la actividad.
 *                    Si es otro número (>0) la variable $mod se pasa con la página del link.
 * @param integer $nueva - 0 ó 1. Para controlar si puede asignar una nueva actividades a la persona.
 *                    Está por cuestiones de velocidad (tamaño de la página).
 *
 * @param array $actividades - Es un vector con los siguientes elementos:
 *
 *            1. La clave es el nombre de la persona con su id_nom(o id_ubi) separados por '#'.
 * Añado pau: p=persona, a=actividad, u=ubi.
 *                    ej: "pau#2345#fulanito de tal"
 *
 *            2. El valor es un vector que contiene todas las actividades de esa persona:
 *
 *                    [0] nom_curt        - nombre corto de la actividad.
 *                    [1] nom_llarg        - nombre largo de la actividad.
 *                    [2] f_ini            - día de inicio, string: "dd/mm/aaaa"; separadores posibles: / . -
 *                    [3] h_ini            - hora de inicio, string: "hh:mm"; separadores posibles: : / . -
 *                    [4] f_fi            - día de fin, string: "dd/mm/aaaa"; separadores posibles: / . -
 *                    [5] h_fi            - hora de fin, string: "hh:mm"; separadores posibles: : / . -
 *                    [6] id_tipo_activ    - id de la actividad.
 *                    [7] pagina            - página del link. a dónde ir al hacer click.
 *                                        Se le añaden los parámetros: id_nom (o id_ubi), id_actividad, $mod
 *                    [8] id                - id_activ
 *                    [9] propio            - si la actividad es propia o no (f o t). Añadido para des: 23.1.2007
 *                   [10] plaza            - si la asistencia esta en pedida o asignada. Añadido para des: 14.3.2020
 *
 * @param integer $doble - 0 ó 1. Para que las cabeceras de filas y columnas también a la izquierda y abajo.
 *
 * @return    mixed    no devuelve nada
 */
class Planning
{

    private $scolorColumnaUno;
    private $scolorColumnaDos;
    private $stable_border;

    private $idd;
    private $scabecera;
    private $oInicio;
    private $oFin;
    private $a_actividades;
    private $imod;
    private $inueva;
    private $idoble = 1;

    public function dibujar()
    {
        $html = '';
        $bgcolor = $this->scolorColumnaUno;
        //dias de la semana y meses
        $semana = array(_("D"), _("L"), _("M"), _("X"), _("J"), _("V"), _("S"));
        $mes = array(_("enero"), _("febrero"), _("marzo"), _("abril"), _("mayo"), _("junio"), _("julio"),
            _("agosto"), _("septiembre"), _("octubre"), _("noviembre"), _("diciembre"));
        $txt_nueva = _("clic para crear una nueva actividad en esta casa");
        $txt_aviso = _("esta fila es para que represente correctamente las divisiones de día");

        //por defecto:
        $h_ini_0 = 0;
        $m_ini_0 = 0;
        $s_ini_0 = 0;
        $h_fi_0 = 0;
        $m_fi_0 = 0;
        $s_fi_0 = 0;
        //list($dini_0,$mini_0,$aini_0) = preg_split('/[\.\/-]/', $inicio );
        $dini_0 = $this->oInicio->format('d');
        $mini_0 = $this->oInicio->format('m');
        $aini_0 = $this->oInicio->format('Y');
        $inicio_iso = $this->oInicio->format('Y-m-d');
        //list($dfi_0,$mfi_0,$afi_0) = preg_split('/[\.\/-]/', $fin );
        $dfi_0 = $this->oFin->format('d');
        $mfi_0 = $this->oFin->format('m');
        $afi_0 = $this->oFin->format('Y');
        $fin_iso = $this->oFin->format('Y-m-d');
        // si el año esta en dos cifras:
        //if ($aini_0 < 100) $aini_0=$aini_0+2000;
        //if ($afi_0 < 100) $afi_0=$afi_0+2000;

        //esto lo hacia con la "z" de la instruccion date, pero se lia para más de un año...
        (float)$num_sec_ini_0 = mktime($h_ini_0, $m_ini_0, $s_ini_0, $mini_0, $dini_0, $aini_0);
        (float)$num_sec_fi_0 = mktime($h_fi_0, $m_fi_0, $s_fi_0, $mfi_0, $dfi_0, $afi_0);
        (float)$num_sec = $num_sec_fi_0 - $num_sec_ini_0;
        // Ojo!! Hay que redondear, pues al hacer la división no es exacto y a veces da (x).01... y otras (x-1).98...
        $total_dias_0 = round($num_sec / 86400) + 1; // 86400 segundos es un dia


        //divido cada dia en tres: despues de desayunar, antes de comer, antes de cenar. $this->idd=3
        $total_dias = $this->idd * $total_dias_0;

        //la primera columna ocupa el 10%
        $ample = 90 / $total_dias;
        //$total=$total_dias*25;
        $txt_tabla = "<div><table $this->stable_border>";
        $txt_head = "<tr> <th rowspan=3  class=\"cap\">$this->scabecera </th>";
        // 1ª Fila. meses: Junio, Julio...
        $c_anterior = 0;
        for ($c = 0; $c < $total_dias_0; $c++) {
            $m = date("m", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0)) - 1;
            $any = (string)date("Y", mktime(0, 0, 0, $mini_0, $dini_0 + $c - 1, $aini_0));
            if ($c == 0) {
                $m_anterior = $m;
            }
            if ($c >= ($total_dias_0 - 1)) {
                $c = $c + 1;
            }
            if ($m != $m_anterior || $c >= $total_dias_0) {
                $inc_c = $this->idd * ($c - $c_anterior);
                $lletra_mes = $mes[$m_anterior];
                $txt_head .= "<th colspan=$inc_c widht=\"$ample%\" class=\"mes\" >$lletra_mes - $any</th>";
                $c_anterior = $c;
                $m_anterior = $m;
            }
        }
        if (!empty($this->idoble)) {
            $txt_head .= "<th rowspan=3 class='cap'>$this->scabecera</th></tr><tr>";
        } else {
            $txt_head .= "</tr><tr>";
        }

        // 2ª Fila. Dias de la semana: L, M, X...
        for ($c = 0; $c < $total_dias / $this->idd; $c++) {
            $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            if ($w == 0) {
                $diumenge = "diumenge";
            } else {
                $diumenge = "lletra";
            }
            $lletra_dia = $semana[$w];
            $txt_head .= "<th colspan=$this->idd align=center class=$diumenge >$lletra_dia</th>";
        }
        $txt_head .= "</tr><tr>";

        // 3ª Fila. Días del mes: 1, 2, 3...
        for ($c = 0; $c < $total_dias / $this->idd; $c++) {
            $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            if ($w == 0) {
                $diumenge = "diumengenum";
            } else {
                $diumenge = "num";
            }
            $num_dia = date("j", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            $txt_head .= "<th colspan=$this->idd align=center class=$diumenge >$num_dia</th>";
        }
        $txt_head .= "</tr>";

        $html .= $txt_tabla;
        $html .= "<thead>$txt_head</thead>";
        if (!empty($this->idoble)) {
            $html .= "<tfoot>$txt_head</tfoot>";
        }
        //Un macro bucle doble para cada persona-casa
        $ancho = 0;
        $periodos_sv = [];
        //print_r($this->a_actividades);
        foreach ($this->a_actividades as $ww) {
            foreach ($ww as $per => $actividad) {
                //list($pau,$id_pau,$persona,$centro) = preg_split('/#/', $per ); //separo el id_ubi del nombre
                list($pau, $id_pau, $persona) = preg_split('/#/', $per); //separo el id_ubi del nombre

                if ($pau == "u") { // para los ubis...
                    $id_ubi = $id_pau;
                    $gesCasaPeriodo = new GestorCasaPeriodo();
                    $periodos_sv[$id_ubi] = $gesCasaPeriodo->getArrayCasaPeriodos($id_ubi, $this->oInicio, $this->oFin);
                }
                //mido el tamaño de los nombres
                $long = strlen($persona);
                if ($ancho < $long) {
                    $ancho = $long;
                }

                $num_a = sizeof($actividad);
                //echo "$persona: $num_a<br>";
                //en el primer bucle pongo todas las variables en vectores, y en el segundo compruebo que no existan intersecciones de fechas.
                //print_r($actividad);
                $dini = [];
                $mini = [];
                $aini = [];
                $hora_ini = [];
                $m_ini = [];
                $s_ini = [];
                $dfi = [];
                $mfi = [];
                $afi = [];
                $hora_fi = [];
                $m_fi = [];
                $s_fi = [];
                $nom_curt = [];
                $nom = [];
                $id_tipo_activ = [];
                $lnk = [];
                $id_activ = [];
                $propio = [];
                $plaza = [];
                $n_dini = [];
                $n_dfi = [];
                for ($a = 0; $a < $num_a; $a++) {

                    $activi = $actividad[$a];
                    //print_r($activi);
                    $nom_curt[$a] = (isset($activi["nom_curt"])) ? $activi["nom_curt"] : '';
                    $nom[$a] = (isset($activi["nom_llarg"])) ? $activi["nom_llarg"] : '';
                    $ini = (isset($activi["f_ini"])) ? $activi["f_ini"] : '';
                    $hini = (isset($activi["h_ini"])) ? $activi["h_ini"] : '';
                    $fi = (isset($activi["f_fi"])) ? $activi["f_fi"] : '';
                    $hfi = (isset($activi["h_fi"])) ? $activi["h_fi"] : '';
                    $id_activ[$a] = (isset($activi["id_activ"])) ? $activi["id_activ"] : 0;

                    if (empty($ini)) {
                        echo _("PREMIO: Ha conseguido crear una actividad sin fecha de inicio.");
                        echo "<br>";
                        echo sprintf(_("id_activ: %s, nombre: %s %s"), $id_activ[$a], $nom_curt[$a], $nom[$a]);
                        echo "<br>";
                        unset($actividad[$a]);
                        continue;
                    }
                    if (empty($fi)) {
                        echo _("PREMIO: Ha conseguido crear una actividad sin fecha finalización.");
                        echo "<br>";
                        echo sprintf(_("id_activ: %s, nombre: %s %s"), $id_activ[$a], $nom_curt[$a], $nom[$a]);
                        echo "<br>";
                        unset($actividad[$a]);
                        continue;
                    }
                    $id_tipo_activ[$a] = (isset($activi["id_tipo_activ"])) ? $activi["id_tipo_activ"] : '';
                    $lnk[$a] = (isset($activi["pagina"])) ? $activi["pagina"] : '';
                    $propio[$a] = (isset($activi["propio"])) ? $activi["propio"] : '';
                    $plaza[$a] = (isset($activi["plaza"])) ? $activi["plaza"] : '';

                    $hora_ini[$a] = 0;
                    $m_ini[$a] = 0;
                    $s_ini[$a] = 0;

                    $hora_fi[$a] = 0;
                    $m_fi[$a] = 0;
                    $s_fi[$a] = 0;
                    if ($this->idd > 1) {
                        //si no hay hora de inicio-fin, cojo por defecto:
                        if (empty($hini)) {
                            if ($ini == $fi) { //caso de empezar y terminar el mismo día
                                $hini = "3:00";
                            } else {
                                $hini = "21:00";
                            }
                        }
                        if (empty($hfi)) {
                            if ($ini == $fi) { //caso de empezar y terminar el mismo día
                                $hfi = "20:00";
                            } else {
                                $hfi = "10:00";
                            }
                        }
                        if (!empty($hini)) {
                            $time = explode(':', $hini);
                            if (isset($time[0])) $hora_ini[$a] = $time[0];
                            if (isset($time[1])) $m_ini[$a] = $time[1];
                            if (isset($time[2])) $s_ini[$a] = $time[2];
                        }
                        if (!empty($hfi)) {
                            $time = explode(':', $hfi);
                            if (isset($time[0])) $hora_fi[$a] = $time[0];
                            if (isset($time[1])) $m_fi[$a] = $time[1];
                            if (isset($time[2])) $s_fi[$a] = $time[2];
                        }
                    }

                    $oIniAct = DateTimeLocal::createFromLocal($ini);
                    $dini[$a] = $oIniAct->format('d');
                    $mini[$a] = $oIniAct->format('m');
                    $aini[$a] = $oIniAct->format('Y');
                    $this->oFinAct = DateTimeLocal::createFromLocal($fi);
                    $dfi[$a] = $this->oFinAct->format('d');
                    $mfi[$a] = $this->oFinAct->format('m');
                    $afi[$a] = $this->oFinAct->format('Y');

                    settype($dini[$a], "integer");
                    settype($dfi[$a], "integer");
                    //echo "dias: $ini -> $dini[$a],$mini[$a],$aini[$a]:: $fi -> $dfi[$a],$mfi[$a],$afi[$a]<br>";
                    //echo "hora: $hora_ini[$a],$m_ini[$a],$s_ini[$a],$mini[$a],$dini[$a],$aini[$a]<br>";

                    (int)$sec_dias_del_any_ini = mktime($hora_ini[$a], $m_ini[$a], $s_ini[$a], $mini[$a], $dini[$a], $aini[$a]);
                    $dias_del_any_ini = round(($sec_dias_del_any_ini - $num_sec_ini_0) / 86400);
                    (int)$sec_dias_del_any_fi = mktime($hora_fi[$a], $m_fi[$a], $s_fi[$a], $mfi[$a], $dfi[$a], $afi[$a]);
                    $dias_del_any_fi = round(($sec_dias_del_any_fi - $num_sec_ini_0) / 86400);
                    //echo "sec_act: $sec_dias_del_any_ini:: $dias_del_any_ini:::: $sec_dias_del_any_fi:: $dias_del_any_fi<br>";

                    //calculo los dias respecto al inicio del calendario, para tener una referencia independiente, y poder controlar las intersecciones.
                    // Si hay una división por dia ($this->idd=1), no miro a que hora empieza-acaba.
                    if ($this->idd > 1) {
                        // las horas que faltan para completar el dia.
                        // Si es positivo(>0) hay que incrementar el inicio
                        // Si es negativo(<0) hay que adelantar el inicio
                        $h_ini = ((($sec_dias_del_any_ini - $num_sec_ini_0) / 86400) - $dias_del_any_ini) * 24;
                        $h_fi = ((($sec_dias_del_any_fi - $num_sec_ini_0) / 86400) - $dias_del_any_fi) * 24;
                        //echo "h: $h_ini, $h_fi<br>";

                        // miro en que bloque está: desayuno, comida, cena.
                        if ($h_ini < 0) { // si es negativo es que empieza el dia antes del calculado.
                            if ($h_ini >= (-4)) {
                                $inc_h_ini = 0;
                            } elseif ($h_ini >= (-14)) {
                                $inc_h_ini = -1;
                            } else {
                                $inc_h_ini = -2;
                            }
                        } else { // es positivo
                            if ($h_ini <= 10) {
                                $inc_h_ini = 1;
                            } elseif ($h_ini <= 20) {
                                $inc_h_ini = 2;
                            } else {
                                $inc_h_ini = 3;
                            }
                        }
                        if ($h_fi < 0) { // si es negativo es que acaba el dia antes del calculado.
                            if ($h_fi >= (-4)) {
                                $inc_h_fi = 0;
                            } elseif ($h_fi >= (-14)) {
                                $inc_h_fi = -1;
                            } else {
                                $inc_h_fi = -2;
                            }
                        } else { // es positivo
                            if ($h_fi <= 10) {
                                $inc_h_fi = 1;
                            } elseif ($h_fi <= 20) {
                                $inc_h_fi = 2;
                            } else {
                                $inc_h_fi = 3;
                            }
                        }
                    } else {
                        $h_ini = 1;
                        $h_fi = 1;
                        $inc_h_ini = 1;
                        $inc_h_fi = 1;
                    }
                    $n_dini[$a] = $inc_h_ini + $this->idd * $dias_del_any_ini;
                    $n_dfi[$a] = $inc_h_fi + $this->idd * ($dias_del_any_fi);
                    //echo "ini: $n_dini[$a], $inc_h_ini, $dias_del_any_ini<br>";
                    //echo "fi:  $n_dfi[$a], $inc_h_fi, $dias_del_any_fi<br>";

                }
                // 2º bucle. 
                // por cada día miro cuantas actividades hay. Si hay más de una la pongo en una segunda fila.
                // La variable $fila_dia tiene que filas están ocupadas para cada día.
                // La variable $max_filas me sirve a la hora de dibujar, para saber cuántas filas tiene un persona.

                $max_filas = 0;
                $fila = array();
                $fila_dia_new = array(); //dimensiono el vector vacio
                $fila_dia = array("v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v", "v"); //dimensiono el vector vacio con 20.
                for ($d = 1; $d < $total_dias; $d++) {
                    $n_act = 0; //numero de actividades para el dia $d.
                    for ($a = 0; $a < $num_a; $a++) {
                        if (empty($actividad[$a])) {
                            continue;
                        }
                        if ($n_dfi[$a] < $n_dini[$a]) { //error no puede ser la fecha ini porsterior a fin
                            $error = "Error. La actividad: " . $nom[$a] . " de " . $persona . " Termina antes de empezar.";
                            $e3 = $n_dfi[$a];
                            $e4 = $n_dini[$a];
                            $html .= "$error $e3-$e4 <br>";
                            break 3;
                        }
                        if ($d >= $n_dini[$a] && $d <= $n_dfi[$a]) {
                            $n_act++;
                            //echo "$persona dia: $d  act nº: $n_act,  cv: $nom[$a] $n_dini[$a] a $n_dfi[$a]<br>";
                            if ($max_filas < $n_act) {
                                $max_filas = $n_act;
                            } //para saber el número máximo de filas
                            // si es el primer dia toca coger fila
                            if ($d == $n_dini[$a]) {
                                //miro de coger la primera fila desocupada
                                $f = 0;
                                foreach ($fila_dia as $val) {
                                    if ($val == "v") {
                                        $fila[$a] = $f;
                                        $fila_dia[$f] = "x"; //marco fila ocupada
                                        break;
                                    }
                                    $f++;
                                }
                            }
                            // si es el último dia, libero la fila
                            if ($d == $n_dfi[$a]) {
                                //marco fila desocupada, pero en una variable distinta que no actualizo hasta que acabe el día
                                $fila_dia_new[$fila[$a]] = "v";
                            }
                        }
                    }
                    //poner los cambios (sólo desocupados) en $fila_dia
                    foreach ($fila_dia_new as $f => $val) {
                        if ($val == "v") {
                            $fila_dia[$f] = "v";
                            $fila_dia_new[$f] = "";
                        }
                    }
                }

                //ahora a dibujar
                // La primera columna con los nombres. Las siguientes con las actividades de ese nombre
                $ancho = 300;
                if ($max_filas == 0) {
                    $max_filas = 1;
                } //para que salgan celdas en blanco para los que no tienen nada y poder asignarles algo
                $html .= "<tbody>";
                //if ($this->idd>1) { 
                //	echo "<colgroup></colgroup>";
                //	for ($d=0;$d<$total_dias/$this->idd;$d++) {
                //		echo "<colgroup span=$this->idd></colgroup>";
                //	}
                //}
                if (!empty($this->inueva)) {
                    if (empty($id_pau)) {
                        $html .= "<tr class=\"delgada\"><td rowspan=$max_filas class=\"delgada\" title=\"$txt_aviso\">$persona</td>";
                    } else {
                        $html .= "<tr><td rowspan=$max_filas class=\"nom link\" onclick=\"fnjs_nueva_activ('$id_pau')\" title=\"$txt_nueva\">$persona</td>";
                    }
                } else {
                    if (empty($id_pau)) {
                        $html .= "<tr class=\"delgada\"><td rowspan=$max_filas class=\"delgada\" title=\"$txt_aviso\">$persona</td>";
                    } else {
                        $html .= "<tr><td rowspan=$max_filas class=\"nom\">$persona</td>";
                    }
                }
                for ($f = 0; $f < $max_filas; $f++) {
                    if ($f > 0) {
                        $html .= "<tr>";
                    }
                    for ($d = 1; $d < $total_dias + 1; $d++) {
                        $texto = "";
                        $reserva = "";
                        if ($pau === "u") {
                            $reserva = $this->reservado($mini_0, $dini_0, $d, $aini_0, $id_ubi, $periodos_sv);
                        }
                        for ($a = 0; $a < $num_a; $a++) {
                            if (isset($fila[$a]) && $fila[$a] == $f) {
                                if ($d == $n_dini[$a]) {
                                    $inc = $n_dfi[$a] - $n_dini[$a];
                                    // en el caso de que se acabe la hoja, hay que cortar:
                                    if ($n_dfi[$a] > $total_dias) {
                                        $inc = $total_dias - $n_dini[$a];
                                    }
                                    $inc2 = $inc + 1;
                                    $clase_act = $this->clase($id_tipo_activ[$a], $propio[$a], $plaza[$a]);
                                    if (substr($id_tipo_activ[$a], 0, 1) == 1 && $reserva === "sf") {
                                        $conflicto = "link_red";
                                    } else {
                                        $conflicto = "link";
                                    }
                                    if (!empty($this->imod) && !empty($lnk[$a])) {
                                        $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\"><span class=\"$conflicto\" onclick=\"fnjs_cambiar_activ('$id_activ[$a]','$this->imod');\">$nom_curt[$a]</span></td>";
                                    } else {
                                        $clase_act = $clase_act . "_nomod";
                                        $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\">$nom_curt[$a]</td>";
                                    }
                                    $d = $d + $inc;
                                    $html .= $texto;
                                }
                            }
                        }
                        if ($d > $total_dias && $texto) {
                        } else {
                            $dia = \bcdiv(($d - 1), $this->idd, 0);
                            $p = $d - (($dia) * $this->idd);
                            if ($p > 1) {
                                $p = 2;
                            }
                            $w = \date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $dia, $aini_0));
                            if ($w == 1) { //el lunes he de poner la linea que separa las semanas.
                                $diumenge = "diumenge" . $p;
                            } else {
                                $diumenge = "nada" . $p;
                            }

                            $bgcolor = $this->scolorColumnaUno;                        // color de las columnas según si es par o impar
                            (($d - 1) / $this->idd) % 2 ? 0 : $bgcolor = $this->scolorColumnaDos;    // por cada dia, todas las divisiones del dia iguales
                            //$bg_color_r=reservado($mini_0,$dini_0,$dia,$aini_0,$id_ubi,$periodos_sv);
                            switch ($reserva) {
                                case "sf":
                                    $bgcolor = "FFCCCC";
                                    break;
                                case "sv":
                                    $bgcolor = "CCCCFF";
                                    break;
                                case "res":
                                    $bgcolor = "CCFFCC";
                                    break;
                                case "pascua":
                                    $bgcolor = 'red';
                                    break;
                            }

                            if (empty($texto)) {
                                /*if (!empty($this->inueva)) {
                                    $this->inueva_act="Esto seria para asignar una nueva actividad a id_ubi:".$id_ubi;
                                    echo "<td bgcolor=".$bgcolor." class=$diumenge onclick=\"javascript:alert('$this->inueva_act')\">&nbsp;</td>";
                                } else { 
                                */
                                $html .= "<td bgcolor=" . $bgcolor . " class=$diumenge>&nbsp;</td>";
                                //}
                            }
                        }
                    }
                    if ($f == 0 && $this->idoble) {
                        $html .= "<td rowspan=$max_filas class=\"nom\">$persona</td>";
                    }
                    $html .= "</tr>";
                }
            }
        } // de los foreach
        $html .= "</tbody></table></div>";

        return $html;
    } //--------------------------------------  fin de dibujar_calendario  -----------------------

    // -------------------------------------------------------------------------------------------
    /**
     *Sirve para selecionar el color de fondo si está reservado.
     */
    private function reservado($mini_0, $dini_0, $dia, $aini_0, $id_ubi, $periodos_sv)
    {
        if ($id_ubi == 1) {
            return "";
        }
        $periodo_ubi = $periodos_sv[$id_ubi];
        if (!is_array($periodo_ubi)) {
            return "";
        }
        $dia2 = $dia / $this->idd;
        $dia_real = date("Ymd", mktime(0, 0, 0, $mini_0, $dini_0 + $dia2, $aini_0));
        $color = "";
        // Domingo de pascua:
        $dia_pascua = date("Ymd", easter_date($aini_0));
        if ($dia_real == $dia_pascua) return "pascua";
        foreach ($periodo_ubi as $per) {
            if ($dia_real <= $per['iso_fin'] && $dia_real >= $per['iso_ini']) {
                if ($per['sfsv'] == 1) $color = "sv";
                if ($per['sfsv'] == 2) $color = "sf";
                if ($per['sfsv'] == 3) $color = "res";
                break;
            } elseif ($dia_real < $per['iso_ini']) {
                $color = "";
                break;
            }
        }
        //echo "dr: $dia_real, dini: $per['f_ini'], dfin: $per['f_fin']";
        return $color;
    }

    /**
     *Es para no volver a escribir todo en la función select.
     *Sirve para selecionar el color en funcion del tipo de actividad: sv, sf, resto
     */
    private function clase($id_tipo_activ, $propio, $plaza)
    {
        switch (substr($id_tipo_activ, 0, 1)) {
            case 1:
                $clase = "actsv";
                break;
            case 2:
                $clase = "actsf";
                break;
            default:
                $clase = "actotras";
        }
        // sobreescribo
        if ($propio === TRUE) {
            $clase = 'actpropio';
        }
        if ($propio === "p") {
            $clase = 'actpersonal';
        }
        if (!empty($plaza) && $plaza < Asistente::PLAZA_ASIGNADA) {
            $clase = 'provisional ' . $clase;
        }
        return $clase;
    }


    /**
     * oInicio
     * @return DateTimeLocal
     */
    public function getInicio()
    {
        return $this->oInicio;
    }

    /**
     * oInicio
     * @param DateTimeLocal $oInicio
     * @return Planning
     */
    public function setInicio($oInicio)
    {
        $this->oInicio = $oInicio;
        return $this;
    }

    /**
     * oFin
     * @return DateTimeLocal
     */
    public function getFin()
    {
        return $this->oFin;
    }

    /**
     * oFin
     * @param DateTimeLocal $oFin
     * @return Planning
     */
    public function setFin($oFin)
    {
        $this->oFin = $oFin;
        return $this;
    }

    /**
     * scolorColumnaUno
     * @return string
     */
    public function getColorColumnaUno()
    {
        return $this->scolorColumnaUno;
    }

    /**
     * scolorColumnaUno
     * @param string $scolorColumnaUno
     * @return Planning
     */
    public function setColorColumnaUno($scolorColumnaUno)
    {
        $this->scolorColumnaUno = $scolorColumnaUno;
        return $this;
    }

    /**
     * scolorColumnaDos
     * @return string
     */
    public function getColorColumnaDos()
    {
        return $this->scolorColumnaDos;
    }

    /**
     * scolorColumnaDos
     * @param string $scolorColumnaDos
     * @return Planning
     */
    public function setColorColumnaDos($scolorColumnaDos)
    {
        $this->scolorColumnaDos = $scolorColumnaDos;
        return $this;
    }

    /**
     * stable_border
     * @return string
     */
    public function getTable_border()
    {
        return $this->stable_border;
    }

    /**
     * stable_border
     * @param string $stable_border
     * @return Planning
     */
    public function setTable_border($stable_border)
    {
        $this->stable_border = $stable_border;
        return $this;
    }

    /**
     * idd
     * @return integer
     */
    public function getDd()
    {
        return $this->idd;
    }

    /**
     * idd
     * @param integer $idd
     * @return Planning
     */
    public function setDd($idd)
    {
        $this->idd = $idd;
        return $this;
    }

    /**
     * scabecera
     * @return string
     */
    public function getCabecera()
    {
        return $this->scabecera;
    }

    /**
     * scabecera
     * @param string $scabecera
     * @return Planning
     */
    public function setCabecera($scabecera)
    {
        $this->scabecera = $scabecera;
        return $this;
    }

    /**
     * a_actividades
     * @return array
     */
    public function getActividades()
    {
        return $this->a_actividades;
    }

    /**
     * a_actividades
     * @param array $a_actividades
     * @return Planning
     */
    public function setActividades($a_actividades)
    {
        $this->a_actividades = $a_actividades;
        return $this;
    }

    /**
     * imod
     * @return integer
     */
    public function getMod()
    {
        return $this->imod;
    }

    /**
     * imod
     * @param integer $imod
     * @return Planning
     */
    public function setMod($imod)
    {
        $this->imod = $imod;
        return $this;
    }

    /**
     * inueva
     * @return integer
     */
    public function getNueva()
    {
        return $this->inueva;
    }

    /**
     * inueva
     * @param integer $inueva
     * @return Planning
     */
    public function setNueva($inueva)
    {
        $this->inueva = $inueva;
        return $this;
    }

    /**
     * idoble
     * @return integer
     */
    public function getDoble()
    {
        return $this->idoble;
    }

    /**
     * idoble
     * @param integer $idoble
     * @return Planning
     */
    public function setDoble($idoble)
    {
        $this->idoble = $idoble;
        return $this;
    }

}
