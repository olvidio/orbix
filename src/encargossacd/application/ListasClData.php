<?php

namespace src\encargossacd\application;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use PDO;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Listado de cl para cr, restringido a los centros de la sss+.
 * Sustituye la logica de `frontend/encargossacd/controller/listas_cl.php`
 * (era una plantilla con SQL crudo).
 *
 * Devuelve el HTML completo listo para volcarlo al cliente; el frontend se
 * limita a pasar `sf` y a echo del resultado.
 */
final class ListasClData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService
    ) {
    }

    /**
     * @return array{ Html: string }
     */
    public function execute(): array
    {
        $oDB = GlobalPdo::get('oDB');
        $any = (int) date('Y');
        $cabecera_left = sprintf(_('Curso %s'), '');
        $cabecera_right = (string)ConfigGlobal::mi_delef();
        $cabecera_right_2 = _('ref. cr 1/14, 10,c)');

        $oService = $this->aplicacionService;
        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";

        $sql = "SELECT u.nombre_ubi, t.cargo, to_char(t.f_ult_nombramiento,'yymm') AS fecha, "
            . "t.orden_cargo, t.id_nom, t.elencum, t.f_cese, t.nombrado_por, "
            . "t.f_ult_nombramiento, t.renovaciones "
            . 'FROM u_centros_dl u, d_cargos t '
            . "WHERE t.id_ubi=u.id_ubi AND t.f_cese ISNULL AND u.tipo_ctr='ss' "
            . 'ORDER BY t.elencum, t.orden_cargo, t.cargo ';
        $stmt = $oDB->query($sql);
        $filas = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];

        $estilos = '<style>'
            . 'div.salta_pag { page-break-after: always; }'
            . 'div.centro { page-break-inside: avoid; }'
            . '.suplente { text-decoration: underline; }'
            . 'table { width: 680px; }'
            . 'td.derecha { text-align: right; }'
            . 'td.grupo { text-align: left; font-weight: bold; text-decoration: underline; }'
            . 'td.centro { font-weight: bold; }'
            . 'td.suplente { text-decoration: underline; }'
            . '</style>';

        $html = $estilos
            . '<table>'
            . "<tr><td class=izquierda>$cabecera_left</td><td class=derecha>$cabecera_right</td></tr>"
            . "<tr><td></td><td class=derecha>$cabecera_right_2</td></tr>"
            . '</table><table>';

        $centre_actual = 'res';
        foreach ($filas as $cargos) {
            $nombre_ubi = (string)($cargos['nombre_ubi'] ?? '');
            $cargo = (string)($cargos['cargo'] ?? '');
            $data = (string)($cargos['fecha'] ?? '');
            $id_nom = $cargos['id_nom'] ?? '';
            $elencum = (string)($cargos['elencum'] ?? '');
            $nombrado_por = (string)($cargos['nombrado_por'] ?? '');
            $f_ult_nombramiento = (string)($cargos['f_ult_nombramiento'] ?? '');

            $sql_nom = "SELECT p.id_nom, apellido1 || ' ' || apellido2 || ', ' || nom as nom, "
                . 'p.id_tabla, s.socio '
                . 'FROM personas p LEFT JOIN p_sssc s USING (id_nom) '
                . 'WHERE p.id_nom = :id_nom';
            $stmtNom = $oDB->prepare($sql_nom);
            $stmtNom->execute([':id_nom' => $id_nom]);
            $noms_p = $stmtNom->fetch(PDO::FETCH_ASSOC);
            if (!is_array($noms_p)) {
                $noms_p = [];
            }
            $n_agd = isset($noms_p['id_tabla']) && is_scalar($noms_p['id_tabla']) ? (string) $noms_p['id_tabla'] : '';
            switch ($n_agd) {
                case 'a':
                    $agd = ' (agd)';
                    break;
                case 'sssc':
                    $socio = isset($noms_p['socio']) && is_scalar($noms_p['socio']) ? (string) $noms_p['socio'] : '';
                    $agd = " ($socio sss+)";
                    break;
                default:
                    $agd = '';
            }
            $cognom = (isset($noms_p['nom']) && is_scalar($noms_p['nom']) ? (string) $noms_p['nom'] : '') . $agd;

            $ini = (int)mktime(0, 0, 0, 11, 1, $any - 1);
            $fi = (int)mktime(0, 0, 0, 10, 31, $any);
            $flag_s = 0;
            if ($f_ult_nombramiento !== '') {
                $partes = preg_split('/[\.\/-]/', $f_ult_nombramiento);
                if (is_array($partes) && count($partes) === 3) {
                    [$d, $m, $a] = array_map('intval', $partes);
                    $nombr = (int)mktime(0, 0, 0, $m, $d, $a);
                    if ($nombrado_por === 'd' && $nombr > $ini && $nombr < $fi) {
                        $flag_s = 1;
                    }
                }
            }

            if ($centre_actual !== $nombre_ubi) {
                $html .= '<tr><td><br></td></tr>'
                    . '<tr style="font-weight: bold;">'
                    . "<td height='30' colspan='5' valign='TOP'>$nombre_ubi</td>"
                    . '<td></td>'
                    . "<td align='LEFT'>$elencum</td>"
                    . '</tr>';
            }
            $fecha_html = $flag_s === 1 ? "<b>$data</b>" : $data;
            $html .= '<tr>'
                . "<td width='40' nowrap>$cargo</td>"
                . '<td></td>'
                . "<td width='50' nowrap>$fecha_html</td>"
                . '<td></td>'
                . "<td colspan=2 width='250'>$cognom</td>"
                . '</tr>';
            if ($nombre_ubi !== '') {
                $centre_actual = $nombre_ubi;
            }
        }

        $html .= '</table>'
            . '<table>'
            . "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>"
            . '</table>';

        return ['Html' => $html];
    }
}
