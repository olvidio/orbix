<?php

namespace frontend\planning\support;

use frontend\shared\domain\value_objects\DateTimeLocal;

/**
 * Renderizador HTML del planning (tabla de actividades por persona/casa).
 *
 * Antes vivia en `apps/planning/domain/Planning.php` con namespace
 * `planning\domain`. Se ha movido a `frontend/planning/support/` como
 * parte de la migracion del modulo planning — el render HTML es
 * presentacion, no dominio (ver `refactor.md`).
 *
 * El shim legacy `planning\domain\Planning` se mantiene temporalmente
 * como subclase de esta clase hasta que no queden consumidores del
 * namespace antiguo.
 *
 * Variables globales esperadas por el controlador que invoca:
 *  - $colorColumnaUno, $colorColumnaDos, $table_border: definidos en
 *    los estilos `calendario.css.php` + `calendario_color_cols.css.php`.
 *
 * Formato de `$actividades`:
 *  - Clave: `pau#id#nombre`, p. ej. `p#2345#fulanito de tal`.
 *    pau = p(ersona), a(ctividad), u(bi).
 *  - Valor: lista de actividades, cada una con claves `nom_curt`,
 *    `nom_llarg`, `f_ini`, `h_ini`, `f_fi`, `h_fi`, `id_tipo_activ`,
 *    `pagina`, `id_activ`, `propio`, `plaza` (opcional), `css` (opcional).
 */
class PlanningRenderer
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
    private DateTimeLocal|false $oFinAct;

    /** @var array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>|null */
    private ?array $casaPeriodosPorUbi = null;

    public function dibujar(): string
    {
        $html = '';
        $semana = array(_("D"), _("L"), _("M"), _("X"), _("J"), _("V"), _("S"));
        $mes = array(_("enero"), _("febrero"), _("marzo"), _("abril"), _("mayo"), _("junio"), _("julio"),
            _("agosto"), _("septiembre"), _("octubre"), _("noviembre"), _("diciembre"));
        $txt_nueva = _("clic para crear una nueva actividad en esta casa");
        $txt_aviso = _("esta fila es para que represente correctamente las divisiones de día");

        $h_ini_0 = 0;
        $m_ini_0 = 0;
        $s_ini_0 = 0;
        $h_fi_0 = 0;
        $m_fi_0 = 0;
        $s_fi_0 = 0;
        $dini_0 = $this->oInicio->format('d');
        $mini_0 = $this->oInicio->format('m');
        $aini_0 = $this->oInicio->format('Y');
        $dfi_0 = $this->oFin->format('d');
        $mfi_0 = $this->oFin->format('m');
        $afi_0 = $this->oFin->format('Y');

        (float)$num_sec_ini_0 = mktime($h_ini_0, $m_ini_0, $s_ini_0, $mini_0, $dini_0, $aini_0);
        (float)$num_sec_fi_0 = mktime($h_fi_0, $m_fi_0, $s_fi_0, $mfi_0, $dfi_0, $afi_0);
        (float)$num_sec = $num_sec_fi_0 - $num_sec_ini_0;
        $total_dias_0 = round($num_sec / 86400) + 1;

        $total_dias = $this->idd * $total_dias_0;
        $ample = 90 / $total_dias;

        $txt_tabla = "<div><table $this->stable_border>";
        $txt_head = "<tr> <th rowspan=3  class=\"cap\">$this->scabecera </th>";
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
                $txt_head .= "<th colspan=$inc_c style=\"width: $ample%\" class=\"mes\" >$lletra_mes - $any</th>";
                $c_anterior = $c;
                $m_anterior = $m;
            }
        }
        if (!empty($this->idoble)) {
            $txt_head .= "<th rowspan=3 class='cap'>$this->scabecera</th></tr><tr>";
        } else {
            $txt_head .= "</tr><tr>";
        }

        for ($c = 0; $c < $total_dias / $this->idd; $c++) {
            $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            $diumenge = ($w == 0) ? "diumenge" : "lletra";
            $lletra_dia = $semana[$w];
            $txt_head .= "<th colspan=$this->idd style='text-align:center' class=$diumenge >$lletra_dia</th>";
        }
        $txt_head .= "</tr><tr>";

        for ($c = 0; $c < $total_dias / $this->idd; $c++) {
            $w = date("w", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            $diumenge = ($w == 0) ? "diumengenum" : "num";
            $num_dia = date("j", mktime(0, 0, 0, $mini_0, $dini_0 + $c, $aini_0));
            $txt_head .= "<th colspan=$this->idd style='text-align:center' class=$diumenge >$num_dia</th>";
        }
        $txt_head .= "</tr>";

        $html .= $txt_tabla;
        $html .= "<thead>$txt_head</thead>";
        if (!empty($this->idoble)) {
            $html .= "<tfoot>$txt_head</tfoot>";
        }

        $ancho = 0;
        $periodos_sv = $this->casaPeriodosPorUbi ?? [];
        foreach ($this->a_actividades as $ww) {
            foreach ($ww as $per => $actividad) {
                list($pau, $id_pau, $persona) = explode('#', $per);

                if ($pau === 'u') {
                    $id_ubi = (int)$id_pau;
                    if (!array_key_exists($id_ubi, $periodos_sv)) {
                        $periodos_sv[$id_ubi] = [];
                    }
                }
                $long = strlen($persona);
                if ($ancho < $long) {
                    $ancho = $long;
                }

                $num_a = count($actividad);
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
                $css = [];
                for ($a = 0; $a < $num_a; $a++) {
                    $activi = $actividad[$a];
                    $nom_curt[$a] = $activi["nom_curt"] ?? '';
                    $nom[$a] = $activi["nom_llarg"] ?? '';
                    $ini = $activi["f_ini"] ?? '';
                    $hini = $activi["h_ini"] ?? '';
                    $fi = $activi["f_fi"] ?? '';
                    $hfi = $activi["h_fi"] ?? '';
                    $id_activ[$a] = $activi["id_activ"] ?? 0;
                    $css[$a] = $activi["css"] ?? '';

                    if (empty($ini)) {
                        $html .= _("PREMIO: Ha conseguido crear una actividad sin fecha de inicio.") . '<br>';
                        $html .= sprintf(_("id_activ: %s, nombre: %s %s"), $id_activ[$a], $nom_curt[$a], $nom[$a]) . '<br>';
                        unset($actividad[$a]);
                        continue;
                    }
                    if (empty($fi)) {
                        $html .= _("PREMIO: Ha conseguido crear una actividad sin fecha finalización.") . '<br>';
                        $html .= sprintf(_("id_activ: %s, nombre: %s %s"), $id_activ[$a], $nom_curt[$a], $nom[$a]) . '<br>';
                        unset($actividad[$a]);
                        continue;
                    }
                    $id_tipo_activ[$a] = $activi["id_tipo_activ"] ?? '';
                    $lnk[$a] = $activi["pagina"] ?? '';
                    $propio[$a] = $activi["propio"] ?? '';
                    $plaza[$a] = $activi["plaza"] ?? '';

                    $hora_ini[$a] = 0;
                    $m_ini[$a] = 0;
                    $s_ini[$a] = 0;
                    $hora_fi[$a] = 0;
                    $m_fi[$a] = 0;
                    $s_fi[$a] = 0;
                    if ($this->idd > 1) {
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
                    $this->oFinAct = DateTimeLocal::createFromLocal($fi);
                    $dfi[$a] = $this->oFinAct->format('d');
                    $mfi[$a] = $this->oFinAct->format('m');
                    $afi[$a] = $this->oFinAct->format('Y');

                    $dini[$a] = (int)$dini[$a];
                    $dfi[$a] = (int)$dfi[$a];

                    (int)$sec_dias_del_any_ini = mktime($hora_ini[$a], $m_ini[$a], $s_ini[$a], $mini[$a], $dini[$a], $aini[$a]);
                    $dias_del_any_ini = round(($sec_dias_del_any_ini - $num_sec_ini_0) / 86400);
                    (int)$sec_dias_del_any_fi = mktime($hora_fi[$a], $m_fi[$a], $s_fi[$a], $mfi[$a], $dfi[$a], $afi[$a]);
                    $dias_del_any_fi = round(($sec_dias_del_any_fi - $num_sec_ini_0) / 86400);

                    if ($this->idd > 1) {
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
                        $inc_h_ini = 1;
                        $inc_h_fi = 1;
                    }
                    $n_dini[$a] = $inc_h_ini + $this->idd * $dias_del_any_ini;
                    $n_dfi[$a] = $inc_h_fi + $this->idd * ($dias_del_any_fi);
                }

                $max_filas = 0;
                $fila = [];
                $fila_dia_new = [];
                $fila_dia = array_fill(0, 20, 'v');
                for ($d = 1; $d < $total_dias; $d++) {
                    $n_act = 0;
                    for ($a = 0; $a < $num_a; $a++) {
                        if (empty($actividad[$a])) {
                            continue;
                        }
                        if ($n_dfi[$a] < $n_dini[$a]) {
                            $error = "Error. La actividad: " . $nom[$a] . " de " . $persona . " Termina antes de empezar.";
                            $e3 = $n_dfi[$a];
                            $e4 = $n_dini[$a];
                            $html .= "$error $e3-$e4 <br>";
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
                                    if ($val === 'v') {
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
                    foreach ($fila_dia_new as $f => $val) {
                        if ($val === 'v') {
                            $fila_dia[$f] = "v";
                            $fila_dia_new[$f] = "";
                        }
                    }
                }

                if ($max_filas == 0) {
                    $max_filas = 1;
                }
                $html .= "<tbody>";
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
                                    if ($n_dfi[$a] > $total_dias) {
                                        $inc = $total_dias - $n_dini[$a];
                                    }
                                    $inc2 = $inc + 1;
                                    $clase_act = $css[$a];
                                    if (substr((string)$id_tipo_activ[$a], 0, 1) == 1 && $reserva === "sf") {
                                        $conflicto = "link_red";
                                    } else {
                                        $conflicto = "link";
                                    }
                                    if (!empty($this->imod) && !empty($lnk[$a])) {
                                        $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\"><span class=\"texto $conflicto\" onclick=\"fnjs_cambiar_activ('$id_activ[$a]');\">$nom_curt[$a]</span></td>";
                                    } else {
                                        $texto = "<td colspan=\"$inc2\" class=\"$clase_act\" title=\"$nom[$a]\"><span class='texto'>$nom_curt[$a]</span></td>";
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
                            $w = \date("w", mktime(0, 0, 0, $mini_0, (int)$dini_0 + (int)$dia, $aini_0));
                            if ($w == 1) {
                                $diumenge = "diumenge" . $p;
                            } else {
                                $diumenge = "nada" . $p;
                            }
                            $bgcolor = (int)(($d - 1) / $this->idd) % 2 ? $this->scolorColumnaUno : $this->scolorColumnaDos;
                            switch ($reserva) {
                                case "sf":
                                    $bgcolor = "#FFCCCC";
                                    break;
                                case "sv":
                                    $bgcolor = "#CCCCFF";
                                    break;
                                case "res":
                                    $bgcolor = "#CCFFCC";
                                    break;
                                case "pascua":
                                    $bgcolor = 'red';
                                    break;
                            }

                            if (empty($texto)) {
                                $html .= "<td style=\"background-color: $bgcolor\" class=$diumenge>&nbsp;</td>";
                            }
                        }
                    }
                    if ($f == 0 && $this->idoble) {
                        $html .= "<td rowspan=$max_filas class=\"nom\">$persona</td>";
                    }
                    $html .= "</tr>";
                }
            }
        }
        $html .= "</tbody></table></div>";

        return $html;
    }

    /**
     * Selecciona el codigo de fondo (sv/sf/res/pascua) si el dia esta
     * reservado para un periodo sf/sv/res o coincide con domingo de pascua.
     */
    private function reservado($mini_0, $dini_0, $dia, $aini_0, $id_ubi, $periodos_sv): string
    {
        if ($id_ubi == 1) {
            return "";
        }
        $periodo_ubi = $periodos_sv[$id_ubi];
        if (!is_array($periodo_ubi)) {
            return "";
        }
        $dia2 = (int)($dia / $this->idd);
        $dia_real = date("Ymd", mktime(0, 0, 0, $mini_0, $dini_0 + $dia2, $aini_0));
        $color = "";
        $dia_pascua = date("Ymd", easter_date($aini_0));
        if ($dia_real == $dia_pascua) {
            return "pascua";
        }
        foreach ($periodo_ubi as $per) {
            if ($dia_real <= $per['iso_fin'] && $dia_real >= $per['iso_ini']) {
                if ($per['sfsv'] == 1) $color = "sv";
                if ($per['sfsv'] == 2) $color = "sf";
                if ($per['sfsv'] == 3) $color = "res";
                break;
            }
            if ($dia_real < $per['iso_ini']) {
                $color = "";
                break;
            }
        }
        return $color;
    }

    public function getInicio(): \DateTimeInterface
    {
        return $this->oInicio;
    }

    public function setInicio(\DateTimeInterface $oInicio): self
    {
        $this->oInicio = $oInicio;
        return $this;
    }

    public function getFin(): \DateTimeInterface
    {
        return $this->oFin;
    }

    public function setFin(\DateTimeInterface $oFin): self
    {
        $this->oFin = $oFin;
        return $this;
    }

    public function getColorColumnaUno()
    {
        return $this->scolorColumnaUno;
    }

    public function setColorColumnaUno(string $scolorColumnaUno): self
    {
        $this->scolorColumnaUno = $scolorColumnaUno;
        return $this;
    }

    public function getColorColumnaDos()
    {
        return $this->scolorColumnaDos;
    }

    public function setColorColumnaDos(string $scolorColumnaDos): self
    {
        $this->scolorColumnaDos = $scolorColumnaDos;
        return $this;
    }

    public function getTable_border()
    {
        return $this->stable_border;
    }

    public function setTable_border(string $stable_border): self
    {
        $this->stable_border = $stable_border;
        return $this;
    }

    public function getDd()
    {
        return $this->idd;
    }

    public function setDd(int $idd): self
    {
        $this->idd = $idd;
        return $this;
    }

    public function getCabecera()
    {
        return $this->scabecera;
    }

    public function setCabecera(string $scabecera): self
    {
        $this->scabecera = $scabecera;
        return $this;
    }

    public function getActividades()
    {
        return $this->a_actividades;
    }

    public function setActividades(array $a_actividades): self
    {
        $this->a_actividades = $a_actividades;
        return $this;
    }

    public function getMod()
    {
        return $this->imod;
    }

    public function setMod(int $imod): self
    {
        $this->imod = $imod;
        return $this;
    }

    public function getNueva()
    {
        return $this->inueva;
    }

    public function setNueva(int $inueva): self
    {
        $this->inueva = $inueva;
        return $this;
    }

    public function getDoble()
    {
        return $this->idoble;
    }

    public function setDoble(int $idoble): self
    {
        $this->idoble = $idoble;
        return $this;
    }

    /**
     * Periodos sf/sv/res por id_ubi (salida de CasaPeriodosForPlanning en src).
     *
     * @param array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>|null $map
     */
    public function setCasaPeriodosPorUbi(?array $map): self
    {
        $this->casaPeriodosPorUbi = $map;
        return $this;
    }
}
