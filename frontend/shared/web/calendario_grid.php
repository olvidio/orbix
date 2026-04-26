<?php

/**
 * Funciones auxiliares para dibujar el grid del planning (SlickGrid).
 * Las funciones son globales (sin namespace) para mantener la compatibilidad
 * con los controladores que hacen `include_once` de este fichero.
 */

use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Dibuja un planning SlickGrid a partir de una lista de actividades.
 *
 * Variables globales que deben definirse en la página que lo invoca:
 *   $colorColumnaUno, $colorColumnaDos  - colores alternos de columnas
 *   $table_border                       - atributos HTML de la tabla
 *
 * Para el formato de `$actividades` y el resto de parámetros ver la
 * documentación original en `apps/web/calendario_grid.php`.
 */
function dibujar_calendario(int $dd, string $cabecera, string $oInicio, string $oFin, array $actividades, int $mod, int $nueva, int $doble = 1)
{
    global $colorColumnaUno, $colorColumnaDos, $table_border;
    $bgcolor = $colorColumnaUno;
    $semana = [_("D"), _("L"), _("M"), _("X"), _("J"), _("V"), _("S")];
    $mes = [_("enero"), _("febrero"), _("marzo"), _("abril"), _("mayo"), _("junio"), _("julio"),
        _("agosto"), _("septiembre"), _("octubre"), _("noviembre"), _("diciembre")];
    $txt_nueva = _("clic para crear una nueva actividad en esta casa");
    $txt_aviso = _("esta fila es para que represente correctamente las divisiones de día");

    $h_ini_0 = 0;
    $m_ini_0 = 0;
    $s_ini_0 = 0;
    $h_fi_0 = 0;
    $m_fi_0 = 0;
    $s_fi_0 = 0;
    $dini_0 = $oInicio->format('d');
    $mini_0 = $oInicio->format('m');
    $aini_0 = $oInicio->format('Y');
    $inicio_iso = $oInicio->format('Y-m-d');
    $dfi_0 = $oFin->format('d');
    $mfi_0 = $oFin->format('m');
    $afi_0 = $oFin->format('Y');
    $fin_iso = $oFin->format('Y-m-d');

    (float)$num_sec_ini_0 = mktime($h_ini_0, $m_ini_0, $s_ini_0, $mini_0, $dini_0, $aini_0);
    (float)$num_sec_fi_0 = mktime($h_fi_0, $m_fi_0, $s_fi_0, $mfi_0, $dfi_0, $afi_0);
    (float)$num_sec = $num_sec_fi_0 - $num_sec_ini_0;
    // Ojo: hay que redondear; la división no es exacta y a veces da (x).01... y otras (x-1).98...
    $total_dias_0 = round($num_sec / 86400) + 1;

    $total_dias = $dd * $total_dias_0;

    $ample = 90 / $total_dias;
    $txt_tabla = "<div><table $table_border>";
    $txt_head = '';
    $filametadata = '';
    $fila_m = 0;
    $data_m = "{id: \"m\"";
    $grid_col = 1;
    $colmetadata = '';
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
            $inc_c = $dd * ($c - $c_anterior);
            $lletra_mes = $mes[$m_anterior];
            $txt_head .= "<th colspan=$inc_c width=\"$ample%\" class=\"mes\" >$lletra_mes - $any</th>";
            $c_anterior = $c;
            $m_anterior = $m;

            $data_m .= ",\"c$grid_col\": \"$lletra_mes - $any\"";
            if (!empty($colmetadata)) $colmetadata .= ',';
            $colmetadata .= "\"c$grid_col\": { \"colspan\": $inc_c }";
            $grid_col += $inc_c;
        }
    }
    $data_m .= "}";
    $filametadata .= "if (row == $fila_m) { return { \"columns\": { $colmetadata } }; } \n";
    if (!empty($doble)) {
        $txt_head .= "<th rowspan=3 class='cap'>$cabecera</th></tr><tr>";
    } else {
        $txt_head .= "</tr><tr>";
    }

    $fila_wd = 1;
    $data_wd = "{id: \"wd\"";
    $grid_col = 1;
    $colmetadata = '';
    for ($c = 0; $c < $total_dias / $dd; $c++) {
        $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
        $diumenge = ($w == 0) ? "diumenge" : "lletra";
        $lletra_dia = $semana[$w];
        $txt_head .= "<th colspan=$dd align=center class=$diumenge >$lletra_dia</th>";
        $data_wd .= ",\"c$grid_col\": \"$lletra_dia\"";
        if (!empty($colmetadata)) $colmetadata .= ',';
        $colmetadata .= "\"c$grid_col\": { \"colspan\": $dd }";
        $grid_col += $dd;
    }
    $txt_head .= "</tr><tr>";
    $data_wd .= "}";
    $filametadata .= "if (row == $fila_wd) { return { \"columns\": { $colmetadata } }; } \n";

    $fila_nd = 2;
    $data_nd = "{id: \"nd\"";
    $grid_col = 1;
    $colmetadata = '';
    for ($c = 0; $c < $total_dias / $dd; $c++) {
        $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
        $diumenge = ($w == 0) ? "diumengenum" : "num";
        $num_dia = date("j", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
        $txt_head .= "<th colspan=$dd align=center class=$diumenge >$num_dia</th>";
        $data_nd .= ",\"c$grid_col\": \"$num_dia\"";
        if (!empty($colmetadata)) $colmetadata .= ',';
        $colmetadata .= "\"c$grid_col\": { \"colspan\": $dd }";
        $grid_col += $dd;
    }
    $txt_head .= "</tr>";
    $data_nd .= "}";
    $filametadata .= "if (row == $fila_nd) { return { \"columns\": { $colmetadata } }; } \n";

    $colsHeader = '[';
    $colsHeader .= "{id: \"nom\", name: \"nom\", field: \"nom\", width:300, sortable: false, cssClass:\"cell-nom\"}";
    for ($c = 1; $c < $total_dias; $c++) {
        $colsHeader .= ",{id: \"c$c\", name: \"\", field: \"c$c\", width:6}";
    }
    $colsHeader .= ']';

    $ancho = 0;
    $periodos_sv = [];
    $data = "[$data_m,$data_wd,$data_nd";
    $aaa = 0;
    $grid_fila = 3;
    $cssActiv_init = "var estil={};\n";
    $cssActiv = "";
    foreach ($actividades as $ww) {
        $aaa++;
        $data .= ',';
        foreach ($ww as $per => $actividad) {
            list($pau, $id_pau, $persona) = preg_split('/#/', $per);
            if (empty($persona)) {
                $data = substr($data, 0, -1);
                continue;
            }

            if (ConfigGlobal::is_app_installed('calendario')) {
                $oDBA = $GLOBALS['oDBA'];
                if ($pau == "u") {
                    $id_ubi = $id_pau;
                    $sql_periodo = "SELECT to_char(f_ini,'YYYYMMDD') as f_ini,to_char(f_fin,'YYYYMMDD') as f_fin , sfsv_num
									FROM du_periodos
									WHERE id_ubi=$id_ubi
									  AND (f_ini BETWEEN '$inicio_iso' AND '$fin_iso' OR f_fin BETWEEN '$inicio_iso' AND '$fin_iso')
									ORDER BY id_ubi,f_ini";
                    $oDBSt_q_periodo = $oDBA->query($sql_periodo);
                    $periodos_sv[$id_ubi] = $oDBSt_q_periodo->fetchAll();
                }
            }

            $long = strlen($persona);
            if ($ancho < $long) {
                $ancho = $long;
            }

            $num_a = sizeof($actividad);
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
            $n_dini = [];
            $n_dfi = [];
            for ($a = 0; $a < $num_a; $a++) {
                $activi = $actividad[$a];
                $nom_curt[$a] = $activi["nom_curt"] ?? '';
                $nom[$a] = $activi["nom_llarg"] ?? '';
                $ini = $activi["f_ini"] ?? '';
                $hini = $activi["h_ini"] ?? '';
                $fi = $activi["f_fi"] ?? '';
                $hfi = $activi["h_fi"] ?? '';
                $id_tipo_activ[$a] = $activi["id_tipo_activ"] ?? '';
                $lnk[$a] = $activi["pagina"] ?? '';
                $id_activ[$a] = $activi["id_activ"] ?? '';
                $propio[$a] = $activi["propio"] ?? '';

                $hora_ini[$a] = 0;
                $m_ini[$a] = 0;
                $s_ini[$a] = 0;

                $hora_fi[$a] = 0;
                $m_fi[$a] = 0;
                $s_fi[$a] = 0;
                if ($dd > 1) {
                    if (empty($hini)) {
                        $hini = ($ini == $fi) ? "3:00" : "21:00";
                    }
                    if (empty($hfi)) {
                        $hfi = ($ini == $fi) ? "20:00" : "10:00";
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
                $oFinAct = DateTimeLocal::createFromLocal($fi);
                $dfi[$a] = $oFinAct->format('d');
                $mfi[$a] = $oFinAct->format('m');
                $afi[$a] = $oFinAct->format('Y');

                settype($dini[$a], "integer");
                settype($dfi[$a], "integer");

                (int)$sec_dias_del_any_ini = mktime($hora_ini[$a], $m_ini[$a], $s_ini[$a], $mini[$a], $dini[$a], $aini[$a]);
                $dias_del_any_ini = round(($sec_dias_del_any_ini - $num_sec_ini_0) / 86400);
                (int)$sec_dias_del_any_fi = mktime($hora_fi[$a], $m_fi[$a], $s_fi[$a], $mfi[$a], $dfi[$a], $afi[$a]);
                $dias_del_any_fi = round(($sec_dias_del_any_fi - $num_sec_ini_0) / 86400);

                if ($dd > 1) {
                    // h_ini/h_fi: las horas que faltan para completar el día
                    // (positivo: incrementar inicio; negativo: adelantar inicio).
                    $h_ini = ((($sec_dias_del_any_ini - $num_sec_ini_0) / 86400) - $dias_del_any_ini) * 24;
                    $h_fi = ((($sec_dias_del_any_fi - $num_sec_ini_0) / 86400) - $dias_del_any_fi) * 24;

                    if ($h_ini < 0) {
                        if ($h_ini >= (-4)) {
                            $inc_h_ini = 0;
                        } elseif ($h_ini >= (-14)) {
                            $inc_h_ini = -1;
                        } else {
                            $inc_h_ini = -2;
                        }
                    } else {
                        if ($h_ini <= 10) {
                            $inc_h_ini = 1;
                        } elseif ($h_ini <= 20) {
                            $inc_h_ini = 2;
                        } else {
                            $inc_h_ini = 3;
                        }
                    }
                    if ($h_fi < 0) {
                        if ($h_fi >= (-4)) {
                            $inc_h_fi = 0;
                        } elseif ($h_fi >= (-14)) {
                            $inc_h_fi = -1;
                        } else {
                            $inc_h_fi = -2;
                        }
                    } else {
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
                $n_dini[$a] = $inc_h_ini + $dd * $dias_del_any_ini;
                $n_dfi[$a] = $inc_h_fi + $dd * ($dias_del_any_fi);
            }

            $max_filas = 0;
            $fila = [];
            $fila_dia_new = [];
            $fila_dia = array_fill(0, 20, 'v');
            for ($d = 1; $d < $total_dias; $d++) {
                $n_act = 0;
                for ($a = 0; $a < $num_a; $a++) {
                    if ($n_dfi[$a] < $n_dini[$a]) {
                        $error = "Error. La actividad: " . $nom[$a] . " de " . $persona . " Termina antes de empezar.";
                        $e3 = $n_dfi[$a];
                        $e4 = $n_dini[$a];
                        echo "$error $e3-$e4 <br>";
                        break 3;
                    }
                    if ($d >= $n_dini[$a] && $d <= $n_dfi[$a]) {
                        $n_act++;
                        if ($max_filas < $n_act) {
                            $max_filas = $n_act;
                        }
                        if ($d == $n_dini[$a]) {
                            $f = 0;
                            foreach ($fila_dia as $val) {
                                if ($val == "v") {
                                    $fila[$a] = $f;
                                    $fila_dia[$f] = "x";
                                    break;
                                }
                                $f++;
                            }
                        }
                        if ($d == $n_dfi[$a]) {
                            $fila_dia_new[$fila[$a]] = "v";
                        }
                    }
                }
                $f = 0;
                foreach ($fila_dia_new as $val) {
                    if ($val == "v") {
                        $fila_dia[$f] = "v";
                        $fila_dia_new[$f] = "";
                    }
                    $f++;
                }
            }

            $ancho = 300;
            if ($max_filas == 0) {
                $max_filas = 1;
            }
            $data .= "";
            $id = "f" . $aaa . "c" . $d;
            for ($f = 0; $f < $max_filas; $f++) {
                if ($f > 0) {
                    $data .= ',';
                    $grid_fila++;
                }
                $data .= "{id: $grid_fila,\"nom\": \"$persona\"";
                $cm = 0;
                $colmetadata = '';
                $cssActiv_init .= "estil[$grid_fila]={}\n";
                for ($d = 1; $d < $total_dias + 1; $d++) {
                    $texto = "";
                    $reserva = "";
                    if (ConfigGlobal::is_app_installed('calendario')) {
                        if ($pau == "u") {
                            $reserva = reservado($dd, $mini_0, $dini_0, $d, $aini_0, $id_ubi, $periodos_sv);
                        }
                    }
                    for ($a = 0; $a < $num_a; $a++) {
                        if (isset($fila[$a]) && $fila[$a] == $f) {
                            if ($d == $n_dini[$a]) {
                                $inc = $n_dfi[$a] - $n_dini[$a];
                                if ($n_dfi[$a] > $total_dias) {
                                    $inc = $total_dias - $n_dini[$a];
                                }
                                $inc2 = $inc + 1;
                                $clase_act = clase($id_tipo_activ[$a], $propio[$a]);
                                if (substr($id_tipo_activ[$a], 0, 1) == 1 && $reserva == "sf") {
                                    $conflicto = "link_red";
                                } else {
                                    $conflicto = "link";
                                }
                                if (!empty($mod) && !empty($lnk[$a])) {
                                    $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\"><span class=\"$conflicto\" onclick=\"cambiar_activ('$id_activ[$a]','$mod');\">$nom_curt[$a]</span></td>";
                                } else {
                                    $clase_act = $clase_act . "_nomod";
                                    $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\">$nom_curt[$a]</td>";
                                }
                                $data .= ",\"c$d\": \"" . addslashes($nom_curt[$a]) . "\"";
                                $cm++;
                                if ($cm > 1) $colmetadata .= ',';
                                $colmetadata .= "\"c$d\": { \"colspan\": $inc2 }";
                                $cssActiv .= "estil[$grid_fila][\"c$d\"]=\"$clase_act\";\n";

                                $d = $d + $inc;
                            }
                        }
                    }
                    if ($d > $total_dias && $texto) {
                    } else {
                        $dia = bcdiv(($d - 1), $dd, 0);
                        $p = $d - (($dia) * $dd);
                        if ($p > 1) {
                            $p = 2;
                        }
                        $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $dia, $aini_0));
                        $diumenge = ($w == 1) ? "diumenge" . $p : "nada" . $p;

                        $bgcolor = $colorColumnaUno;
                        (($d - 1) / $dd) % 2 ? 0 : $bgcolor = $colorColumnaDos;
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
                    }
                }
                $data .= '}';
                if ($cm > 0) {
                    $filametadata .= "if (row == $grid_fila) { return { \"columns\": { $colmetadata } }; } \n";
                }
            }
        }
        $grid_fila++;
    }

    $data .= ']';
    $id_tabla = 'calendario';
    $tt = "
	  <style>
	  .slick-cell {
		  font-size: 9px;
		  text-align: center;
	}

		.cell-nom {
		  font-weight: bold;
		  font-size: 11px;
		  text-align: left;
		}

	  </style>
	";
    $tt .= "
	<script>
	  var dataView_$id_tabla;
	  var grid_$id_tabla;
	  var columns_$id_tabla = $colsHeader;
	  var data_$id_tabla = $data;

	  var options = {
		enableCellNavigation: false
		,enableColumnReorder: false
		,topPanelHeight: 25
		,autoHeight: false
		,autosizeColumns: false
		,autoEdit: false
		,frozenColumn: 0
		,frozenRow: 3
	  };

	var sortcol = \"title\";
	var sortdir = 1;
	var searchString = \"\";

	  ";
    $tt .= "
	function comparer(a, b) {
	  var x = a[sortcol], y = b[sortcol];
	  return (x == y ? 0 : (x > y ? 1 : -1));
	}

	";

    $tt .= "
	$(\".grid-header .ui-icon\")
			.addClass(\"ui-state-default ui-corner-all\")
			.on(\"mouseover\", function (e) {
			  $(e.target).addClass(\"ui-state-hover\")
			})
			.on(\"mouseout\", function (e) {
			  $(e.target).removeClass(\"ui-state-hover\")
			});


	  $(function () {
		dataView_$id_tabla = new SlickDataView();
		dataView_$id_tabla.getItemMetadata = function (row) { $filametadata };

		grid_$id_tabla = new SlickGrid(\"#grid_$id_tabla\", dataView_$id_tabla, columns_$id_tabla, options);
		grid_$id_tabla.setSelectionModel(new Slick.RowSelectionModel());

		dataView_$id_tabla.beginUpdate();
		dataView_$id_tabla.setItems(data_$id_tabla);
		dataView_$id_tabla.endUpdate();

		$(\"#grid_$id_tabla\").resizable();
		grid_$id_tabla.setOptions({ 'frozenColumn': 0 });

		$cssActiv_init
		$cssActiv

		grid_$id_tabla.addCellCssStyles(0, estil);
	  })
	</script>
	";


    $tt .= "<div id=\"GridContainer_" . $id_tabla . "\"  style=\"width:1200px;\" >
		<div class=\"grid-header\">
		</div>
		<div id=\"grid_$id_tabla\"  style=\"width:1200px; height:500px\"></div>
		</div>";

    echo $tt;
}

/**
 * Devuelve el estado de reserva de un día (sf, sv, res, pascua) para colorear
 * la columna correspondiente del planning.
 */
function reservado($dd, $mini_0, $dini_0, $dia, $aini_0, $id_ubi, $periodos_sv)
{
    if ($id_ubi == 1) {
        return "";
    }
    $periodo_ubi = $periodos_sv[$id_ubi];
    if (!is_array($periodo_ubi)) {
        return "";
    }
    $dia2 = $dia / $dd;
    $dia_real = date("Ymd", mktime(0, 0, 0, $mini_0, $dini_0 + $dia2, $aini_0));
    $color = "";
    $dia_pascua = date("Ymd", easter_date($aini_0));
    if ($dia_real == $dia_pascua) return "pascua";
    foreach ($periodo_ubi as $per) {
        if ($dia_real <= $per['f_fin'] && $dia_real >= $per['f_ini']) {
            if ($per['sfsv_num'] == 1) $color = "sv";
            if ($per['sfsv_num'] == 2) $color = "sf";
            if ($per['sfsv_num'] == 3) $color = "res";
            break;
        } elseif ($dia_real < $per['f_ini']) {
            $color = "";
            break;
        }
    }
    return $color;
}

/**
 * Devuelve la clase CSS en función del tipo de actividad (sv, sf u otras) y
 * de si la actividad es propia o personal.
 */
function clase($id_tipo_activ, $propio)
{
    if ($propio == "t") return "actpropio";
    if ($propio == "p") return "actpersonal";
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
    return $clase;
}
