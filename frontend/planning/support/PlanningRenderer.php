<?php

namespace frontend\planning\support;

use frontend\shared\helpers\PayloadCoercion;
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
    private string $scolorColumnaUno = '';
    private string $scolorColumnaDos = '';
    private string $scolorColumnaDomingo = '';
    private string $stable_border = '';

    private int $idd = 1;
    private string $scabecera = '';
    private ?\DateTimeInterface $oInicio = null;
    private ?\DateTimeInterface $oFin = null;
    /** @var array<int|string, array<int|string, mixed>> */
    private array $a_actividades = [];
    private int $imod = 0;
    private int $inueva = 0;
    private int $idoble = 1;
    /** @var array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>|null */
    private ?array $casaPeriodosPorUbi = null;

    public function dibujar(): string
    {
        if ($this->oInicio === null || $this->oFin === null) {
            return '';
        }
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

        (float)$num_sec_ini_0 = mktime($h_ini_0, $m_ini_0, $s_ini_0, (int)$mini_0, (int)$dini_0, (int)$aini_0);
        (float)$num_sec_fi_0 = mktime($h_fi_0, $m_fi_0, $s_fi_0, (int)$mfi_0, (int)$dfi_0, (int)$afi_0);
        (float)$num_sec = $num_sec_fi_0 - $num_sec_ini_0;
        $total_dias_0 = round($num_sec / 86400) + 1;

        $total_dias = $this->idd * $total_dias_0;
        $ample = 90 / $total_dias;

        $txt_tabla = "<div><table $this->stable_border>";
        $txt_head = "<tr> <th rowspan=3  class=\"cap\">$this->scabecera </th>";
        $c_anterior = 0;
        for ($c = 0; $c < $total_dias_0; $c++) {
            $tsMes = mktime(0, 0, 0, (int)$mini_0, (int)$dini_0 + $c, (int)$aini_0);
            $tsAny = mktime(0, 0, 0, (int)$mini_0, (int)$dini_0 + $c - 1, (int)$aini_0);
            $m = ($tsMes !== false ? (int)date('m', $tsMes) : 1) - 1;
            $any = $tsAny !== false ? date('Y', $tsAny) : (string)$aini_0;
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
            $tsDia = mktime(0, 0, 0, (int)$mini_0, (int)$dini_0 + $c, (int)$aini_0);
            $w = $tsDia !== false ? (int)date('w', $tsDia) : 0;
            $diumenge = ($w == 0) ? "diumenge" : "lletra";
            $lletra_dia = $semana[$w];
            $txt_head .= "<th colspan=$this->idd style='text-align:center' class=$diumenge >$lletra_dia</th>";
        }
        $txt_head .= "</tr><tr>";

        for ($c = 0; $c < $total_dias / $this->idd; $c++) {
            $tsDia = mktime(0, 0, 0, (int)$mini_0, (int)$dini_0 + $c, (int)$aini_0);
            $w = $tsDia !== false ? (int)date('w', $tsDia) : 0;
            $diumenge = ($w == 0) ? "diumengenum" : "num";
            $num_dia = $tsDia !== false ? date('j', $tsDia) : '0';
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
                if (!is_array($actividad)) {
                    continue;
                }
                [$pau, $id_pau, $persona, $actividad] = $this->parsePersonaFila((string)$per, $actividad);
                $id_ubi = 0;

                if ($pau === 'u') {
                    $id_ubi = (int)$id_pau;
                    if (!array_key_exists($id_ubi, $periodos_sv)) {
                        $periodos_sv[$id_ubi] = [];
                    }
                }
                $long = strlen((string)$persona);
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
                    $nom_curt[$a] = PayloadCoercion::string($activi['nom_curt'] ?? '');
                    $nom[$a] = PayloadCoercion::string($activi['nom_llarg'] ?? '');
                    $ini = PayloadCoercion::string($activi['f_ini'] ?? '');
                    $hini = PayloadCoercion::string($activi['h_ini'] ?? '');
                    $fi = PayloadCoercion::string($activi['f_fi'] ?? '');
                    $hfi = PayloadCoercion::string($activi['h_fi'] ?? '');
                    $id_activ[$a] = PayloadCoercion::int($activi['id_activ'] ?? 0);
                    $css[$a] = PayloadCoercion::string($activi['css'] ?? '');

                    if (empty($ini)) {
                        $html .= _("PREMIO: Ha conseguido crear una actividad sin fecha de inicio.") . '<br>';
                        $html .= sprintf(_('id_activ: %s, nombre: %s %s'), (string)$id_activ[$a], $nom_curt[$a], $nom[$a]) . '<br>';
                        unset($actividad[$a]);
                        continue;
                    }
                    if (empty($fi)) {
                        $html .= _("PREMIO: Ha conseguido crear una actividad sin fecha finalización.") . '<br>';
                        $html .= sprintf(_('id_activ: %s, nombre: %s %s'), (string)$id_activ[$a], $nom_curt[$a], $nom[$a]) . '<br>';
                        unset($actividad[$a]);
                        continue;
                    }
                    $id_tipo_activ[$a] = PayloadCoercion::string($activi['id_tipo_activ'] ?? '');
                    $lnk[$a] = PayloadCoercion::string($activi['pagina'] ?? '');
                    $propio[$a] = PayloadCoercion::string($activi['propio'] ?? '');
                    $plaza[$a] = PayloadCoercion::string($activi['plaza'] ?? '');

                    $hora_ini[$a] = 0;
                    $m_ini[$a] = 0;
                    $s_ini[$a] = 0;
                    $hora_fi[$a] = 0;
                    $m_fi[$a] = 0;
                    $s_fi[$a] = 0;
                    if ($this->idd > 1) {
                        if ($hini === '') {
                            $hini = ($ini == $fi) ? '3:00' : '21:00';
                        }
                        if ($hfi === '') {
                            $hfi = ($ini == $fi) ? '20:00' : '10:00';
                        }
                        $timeIni = explode(':', $hini);
                        $hora_ini[$a] = (int)$timeIni[0];
                        $m_ini[$a] = (int)($timeIni[1] ?? 0);
                        $s_ini[$a] = (int)($timeIni[2] ?? 0);
                        $timeFi = explode(':', $hfi);
                        $hora_fi[$a] = (int)$timeFi[0];
                        $m_fi[$a] = (int)($timeFi[1] ?? 0);
                        $s_fi[$a] = (int)($timeFi[2] ?? 0);
                    }

                    $oIniAct = DateTimeLocal::createFromLocal($ini);
                    if (!$oIniAct instanceof DateTimeLocal) {
                        unset($actividad[$a]);
                        continue;
                    }
                    $dini[$a] = (int)$oIniAct->format('d');
                    $mini[$a] = (int)$oIniAct->format('m');
                    $aini[$a] = (int)$oIniAct->format('Y');
                    $oFinActRow = DateTimeLocal::createFromLocal($fi);
                    if (!$oFinActRow instanceof DateTimeLocal) {
                        unset($actividad[$a]);
                        continue;
                    }
                    $dfi[$a] = (int)$oFinActRow->format('d');
                    $mfi[$a] = (int)$oFinActRow->format('m');
                    $afi[$a] = (int)$oFinActRow->format('Y');

                    $slots = PlanningActivitySlots::indices(
                        $this->idd,
                        (int) $num_sec_ini_0,
                        (int) $dini[$a],
                        (int) $mini[$a],
                        (int) $aini[$a],
                        (int) $hora_ini[$a],
                        (int) $m_ini[$a],
                        (int) $s_ini[$a],
                        (int) $dfi[$a],
                        (int) $mfi[$a],
                        (int) $afi[$a],
                        (int) $hora_fi[$a],
                        (int) $m_fi[$a],
                        (int) $s_fi[$a],
                    );
                    $n_dini[$a] = $slots['n_dini'];
                    $n_dfi[$a] = $slots['n_dfi'];
                }

                $max_filas = 0;
                $fila = [];
                $fila_dia_new = [];
                $fila_dia = array_fill(0, 20, 'v');
                for ($d = 1; $d <= $total_dias; $d++) {
                    if ($this->idd > 1 && ($d - 1) % $this->idd === 0) {
                        for ($aLimp = 0; $aLimp < $num_a; $aLimp++) {
                            if (!isset($fila[$aLimp]) || empty($actividad[$aLimp])) {
                                continue;
                            }
                            if ($n_dfi[$aLimp] < $d) {
                                $fila_dia[$fila[$aLimp]] = 'v';
                            }
                        }
                    }
                    $n_act = 0;
                    for ($a = 0; $a < $num_a; $a++) {
                        if (empty($actividad[$a])) {
                            continue;
                        }
                        if ($n_dfi[$a] < $n_dini[$a]) {
                            $error = 'Error. La actividad: ' . $nom[$a] . ' de ' . $persona . ' Termina antes de empezar.';
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
                                foreach ($fila_dia as $_) {
                                    if ($this->actividadPuedeUsarFila(
                                        $f,
                                        $a,
                                        $num_a,
                                        $fila,
                                        $n_dini,
                                        $n_dfi,
                                        $actividad
                                    )) {
                                        $fila[$a] = $f;
                                        $fila_dia[$f] = 'x';
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
                $html .= '<tbody class="planning-persona">';
                for ($f = 0; $f < $max_filas; $f++) {
                    $claseFila = $this->claseFilaPlanning($f, $max_filas, empty($id_pau));
                    if ($f === 0) {
                        if (!empty($this->inueva) && !empty($id_pau)) {
                            $html .= "<tr class=\"$claseFila\"><td rowspan=$max_filas class=\"nom link\" onclick=\"fnjs_nueva_activ('$id_pau')\" title=\"$txt_nueva\">$persona</td>";
                        } elseif (empty($id_pau)) {
                            $html .= "<tr class=\"$claseFila\"><td rowspan=$max_filas class=\"delgada\" title=\"$txt_aviso\">$persona</td>";
                        } else {
                            $html .= "<tr class=\"$claseFila\"><td rowspan=$max_filas class=\"nom\">$persona</td>";
                        }
                    } else {
                        $html .= "<tr class=\"$claseFila\">";
                    }
                    for ($d = 1; $d < $total_dias + 1; $d++) {
                        $texto = "";
                        $reserva = "";
                        if ($pau === "u") {
                            $reserva = $this->reservado((int)$mini_0, (int)$dini_0, (int)$d, (int)$aini_0, $id_ubi, $periodos_sv);
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
                                    if (substr($id_tipo_activ[$a], 0, 1) === '1' && $reserva === 'sf') {
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
                            $dia = (int)\bcdiv((string)($d - 1), (string)$this->idd, 0);
                            $p = $d - ($dia * $this->idd);
                            if ($p > 1) {
                                $p = 2;
                            }
                            $tsCelda = mktime(0, 0, 0, (int)$mini_0, (int)$dini_0 + $dia, (int)$aini_0);
                            $w = $tsCelda !== false ? (int)date('w', $tsCelda) : 0;
                            if ($w == 0) {
                                $diumenge = "diumenge" . $p;
                            } else {
                                $diumenge = "nada" . $p;
                            }
                            $bgcolor = (int)(($d - 1) / $this->idd) % 2 ? $this->scolorColumnaUno : $this->scolorColumnaDos;
                            if ($w == 0 && !empty($this->scolorColumnaDomingo)) {
                                $bgcolor = $this->scolorColumnaDomingo;
                            }
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
                $html .= '</tbody>';
            }
        }
        $html .= '</table></div>';

        return $html;
    }

    /**
     * @param array<int, int> $fila
     * @param array<int, int> $nDini
     * @param array<int, int> $nDfi
     * @param array<int|string, mixed> $actividades
     */
    private function actividadPuedeUsarFila(
        int $f,
        int $actividadIdx,
        int $numActividades,
        array $fila,
        array $nDini,
        array $nDfi,
        array $actividades
    ): bool {
        for ($other = 0; $other < $numActividades; $other++) {
            if ($other === $actividadIdx || empty($actividades[$other]) || !isset($fila[$other]) || $fila[$other] !== $f) {
                continue;
            }
            if ($nDini[$actividadIdx] <= $nDfi[$other] && $nDfi[$actividadIdx] >= $nDini[$other]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Descompone la clave `pau#id#nombre` (o fila envuelta con indice numerico).
     *
     * @param array<int|string, mixed> $actividad
     * @return array{0: string, 1: string, 2: string, 3: list<array<string, mixed>>}
     */
    private function parsePersonaFila(string $per, array $actividad): array
    {
        if (ctype_digit($per) && $actividad !== []) {
            $nestedKey = array_key_first($actividad);
            if (is_string($nestedKey) && str_contains($nestedKey, '#')) {
                $nested = $actividad[$nestedKey];
                $per = $nestedKey;
                $actividad = is_array($nested) ? $this->normalizeActividadList($nested) : [];
            }
        }

        $parts = explode('#', $per, 4);

        return [
            $parts[0],
            $parts[1] ?? '',
            $parts[2] ?? '',
            $this->normalizeActividadList($actividad),
        ];
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return list<array<string, mixed>>
     */
    private function normalizeActividadList(array $raw): array
    {
        if ($raw === []) {
            return [];
        }
        if (!array_is_list($raw)) {
            return [$this->normalizeActivityRow($raw)];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $out[] = $this->normalizeActivityRow($item);
            }
        }

        return $out;
    }

    /**
     * @param array<mixed, mixed> $raw
     * @return array<string, mixed>
     */
    private function normalizeActivityRow(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * Clase CSS de la fila: separador fino entre filas de la misma persona,
     * borde mas marcado al final de cada persona.
     */
    private function claseFilaPlanning(int $fila, int $maxFilas, bool $esDelgada): string
    {
        $clases = [($fila < $maxFilas - 1) ? 'planning-fila-interna' : 'planning-fila-persona-fin'];
        if ($esDelgada) {
            array_unshift($clases, 'delgada');
        }

        return implode(' ', $clases);
    }

    /**
     * Selecciona el codigo de fondo (sv/sf/res/pascua) si el dia esta
     * reservado para un periodo sf/sv/res o coincide con domingo de pascua.
     */
    /**
     * @param array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>> $periodos_sv
     */
    private function reservado(int $mini_0, int $dini_0, int $dia, int $aini_0, int $id_ubi, array $periodos_sv): string
    {
        if ($id_ubi === 1) {
            return '';
        }
        $periodo_ubi = $periodos_sv[$id_ubi] ?? null;
        if (!is_array($periodo_ubi)) {
            return '';
        }
        $dia2 = (int)($dia / $this->idd);
        $tsReal = mktime(0, 0, 0, $mini_0, $dini_0 + $dia2, $aini_0);
        $dia_real = $tsReal !== false ? date('Ymd', $tsReal) : '';
        $color = '';
        $dia_pascua = date('Ymd', easter_date($aini_0));
        if ($dia_real === $dia_pascua) {
            return 'pascua';
        }
        foreach ($periodo_ubi as $per) {
            $isoIni = $per['iso_ini'];
            $isoFin = $per['iso_fin'];
            $sfsv = $per['sfsv'];
            if ($dia_real <= $isoFin && $dia_real >= $isoIni) {
                if ($sfsv === 1) {
                    $color = 'sv';
                }
                if ($sfsv === 2) {
                    $color = 'sf';
                }
                if ($sfsv === 3) {
                    $color = 'res';
                }
                break;
            }
            if ($dia_real < $isoIni) {
                $color = '';
                break;
            }
        }

        return $color;
    }

    public function getInicio(): ?\DateTimeInterface
    {
        return $this->oInicio;
    }

    public function setInicio(\DateTimeInterface $oInicio): self
    {
        $this->oInicio = $oInicio;
        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->oFin;
    }

    public function setFin(\DateTimeInterface $oFin): self
    {
        $this->oFin = $oFin;
        return $this;
    }

    public function getColorColumnaUno(): string
    {
        return $this->scolorColumnaUno;
    }

    public function setColorColumnaUno(string $scolorColumnaUno): self
    {
        $this->scolorColumnaUno = $scolorColumnaUno;
        return $this;
    }

    public function getColorColumnaDos(): string
    {
        return $this->scolorColumnaDos;
    }

    public function setColorColumnaDos(string $scolorColumnaDos): self
    {
        $this->scolorColumnaDos = $scolorColumnaDos;
        return $this;
    }

    public function getColorColumnaDomingo(): string
    {
        return $this->scolorColumnaDomingo;
    }

    public function setColorColumnaDomingo(string $scolorColumnaDomingo): self
    {
        $this->scolorColumnaDomingo = $scolorColumnaDomingo;
        return $this;
    }

    public function getTable_border(): string
    {
        return $this->stable_border;
    }

    public function setTable_border(string $stable_border): self
    {
        $this->stable_border = $stable_border;
        return $this;
    }

    public function getDd(): int
    {
        return $this->idd;
    }

    public function setDd(int $idd): self
    {
        $this->idd = $idd;
        return $this;
    }

    public function getCabecera(): string
    {
        return $this->scabecera;
    }

    public function setCabecera(string $scabecera): self
    {
        $this->scabecera = $scabecera;
        return $this;
    }

    /**
     * @return array<int|string, array<int|string, mixed>>
     */
    public function getActividades(): array
    {
        return $this->a_actividades;
    }

    /**
     * @param array<int|string, array<int|string, mixed>> $a_actividades
     */
    public function setActividades(array $a_actividades): self
    {
        $this->a_actividades = $a_actividades;
        return $this;
    }

    public function getMod(): int
    {
        return $this->imod;
    }

    public function setMod(int $imod): self
    {
        $this->imod = $imod;
        return $this;
    }

    public function getNueva(): int
    {
        return $this->inueva;
    }

    public function setNueva(int $inueva): self
    {
        $this->inueva = $inueva;
        return $this;
    }

    public function getDoble(): int
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
