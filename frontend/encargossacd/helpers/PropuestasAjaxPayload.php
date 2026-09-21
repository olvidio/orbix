<?php

declare(strict_types=1);

namespace frontend\encargossacd\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashF;
use frontend\shared\web\Desplegable;

/**
 * Renderiza en frontend los fragmentos legacy de propuestas que `src/` devuelve como datos.
 */
final class PropuestasAjaxPayload
{
    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function render(array $data): array
    {
        if (($data['success'] ?? false) !== true) {
            return $data;
        }

        $popup = $data['popup'] ?? null;
        if ($popup === 'lista_sacd') {
            $data['html'] = self::listaSacd($data);
        } elseif ($popup === 'dedicacion') {
            $data['html'] = self::dedicacion($data);
        } elseif ($popup === 'info') {
            $data['html'] = self::info($data);
        } elseif (array_key_exists('row', $data)) {
            $data['html'] = self::row($data['row']);
        }

        return $data;
    }

    /** @param array<string, mixed> $data */
    private static function listaSacd(array $data): string
    {
        $opciones = is_array($data['opciones'] ?? null) ? $data['opciones'] : [];
        $tipo = self::string($data['tipo'] ?? '');
        if (!in_array($tipo, ['titular', 'suplente', 'colaborador'], true)) {
            $tipo = '';
        }
        $idItem = self::int($data['id_item'] ?? 0);
        $idEnc = self::int($data['id_enc'] ?? 0);
        $oDesplegable = new Desplegable('prop_sacd', $opciones, self::string($data['id_sacd'] ?? ''));
        $oDesplegable->setAction("fnjs_cmb_sacd('$tipo',$idItem,$idEnc);");

        return self::closeButton() . '<br><br>' . $oDesplegable->desplegable();
    }

    /** @param array<string, mixed> $data */
    private static function dedicacion(array $data): string
    {
        $url = AppUrlConfig::browserUrlFromAppRelative('frontend/encargossacd/controller/propuestas_ajax.php');
        $oHash = new HashF();
        $oHash->setUrl($url);
        $oHash->setArrayCamposHidden([
            'que' => 'dedicacion_update',
            'id_sacd' => self::int($data['id_sacd'] ?? 0),
            'id_item' => self::int($data['id_item'] ?? 0),
            'id_enc' => self::int($data['id_enc'] ?? 0),
        ]);
        $oHash->setCamposForm('dedic_m!dedic_t!dedic_v');

        $html = self::escape(self::string($data['apellidos_nombre'] ?? ''));
        $html .= self::closeButton() . '<br>';
        $html .= "<form method='post' id='modulos' action=''>";
        $html .= $oHash->getCamposHtml();
        $html .= "<table style='width: 400px;' class='tono2'><tr><td colspan=3>"
            . self::escape(self::string($data['desc_enc'] ?? '')) . '</td></tr>';
        $html .= "<td><input type='text' size='1' name='dedic_m' value='"
            . self::escape(self::string($data['dedic_m'] ?? '')) . "'>" . _('mañanas') . '</td>';
        $html .= "<td><input type='text' size='1' name='dedic_t' value='"
            . self::escape(self::string($data['dedic_t'] ?? '')) . "'>" . _('tarde 1ª hora') . '</td>';
        $html .= "<td><input type='text' size='1' name='dedic_v' value='"
            . self::escape(self::string($data['dedic_v'] ?? '')) . "'>" . _('tarde 2ª hora') . '</td></tr>';
        $html .= "<tr><td colspan=3><input type='button' onClick='fnjs_guardar_horario();' value='"
            . self::escape(_('ok')) . "'></td></tr></table></form>";

        return $html;
    }

    /** @param array<string, mixed> $data */
    private static function info(array $data): string
    {
        $html = '<span class="nom">' . self::escape(self::string($data['apellidos_nombre'] ?? '')) . '</span>';
        $html .= self::closeButton();
        $encargos = is_array($data['encargos'] ?? null) ? $data['encargos'] : [];
        foreach ($encargos as $encargo) {
            $html .= '<br><br>' . self::escape(self::string($encargo));
        }

        return $html;
    }

    private static function row(mixed $row): string
    {
        if (!is_array($row)) {
            return '';
        }
        $tipo = self::string($row['tipo'] ?? '');
        if ($tipo === 'borrar') {
            return 'borrar';
        }
        if ($tipo === 'celdas') {
            return self::proposalCells($row);
        }
        if ($tipo === 'fila_colaborador') {
            $idItem = self::int($row['id_item'] ?? 0);
            $idEnc = self::int($row['id_enc'] ?? 0);
            $idSacd = self::int($row['id_sacd'] ?? 0);
            return '<tr id="tr_colaborador' . $idItem . '" class="sf" title="' . $idSacd . '"><td>'
                . _('colaborador') . '</td><td>-</td>'
                . self::proposalCells([
                    'encargo_tipo' => 'colaborador',
                    'id_item' => $idItem,
                    'id_enc' => $idEnc,
                    'id_sacd' => $idSacd,
                    'nombre' => self::string($row['nombre'] ?? ''),
                    'nom_tipo' => null,
                ])
                . '</tr>';
        }

        return '';
    }

    /** @param array<string, mixed> $row */
    private static function proposalCells(array $row): string
    {
        $tipo = self::string($row['encargo_tipo'] ?? '');
        $idItem = self::int($row['id_item'] ?? 0);
        $idEnc = self::int($row['id_enc'] ?? 0);
        $idSacd = self::int($row['id_sacd'] ?? 0);
        $nombre = self::escape(self::string($row['nombre'] ?? ''));
        $nomTipo = $row['nom_tipo'] === null ? '' : self::escape(self::string($row['nom_tipo'] ?? ''));

        $verSacd = self::escape("fnjs_ver_sacd_posibles('$tipo',$idItem,$idEnc)");
        $info = self::escape("fnjs_info('$tipo',$idItem)");
        $dedicacion = self::escape("fnjs_dedicacion('$tipo',$idItem,$idEnc)");

        return ($nomTipo === '' ? '' : '<td>' . $nomTipo . '</td><td>-</td>')
            . '<td><span class="link" id="' . self::escape($tipo . '_' . $idItem) . '" title="' . $idSacd
            . '" onClick="' . $verSacd . '">' . $nombre . '</span></td>'
            . '<td><span class="link" onClick="' . $info . '">' . _('+ info') . '</span></td>'
            . '<td><span class="link" onClick="' . $dedicacion . '">?</span></td><td id="td_' . $idItem . '"></td>';
    }

    private static function closeButton(): string
    {
        return '<span class="x" onClick="fnjs_cerrar_propuesta_popup(); return false;" title="'
            . self::escape(_('cerrar')) . '">[x]</span>';
    }

    private static function int(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private static function string(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
