<?php

declare(strict_types=1);

namespace frontend\encargossacd\support;

use web\Desplegable;
use web\Hash;

/**
 * Agrupa las piezas UI repetidas en los controllers `sacd_ficha`,
 * `sacd_ausencias` y `sacd_ausencias_jefe_zona`:
 *
 * - el `<select>` de `filtro_sacd` con las 4 opciones (`n`, `a`, `sssc`, `cp_sss`);
 * - los `Hash` para el proxy AJAX `sacd_ficha_ajax.php` (rama `get_select` /
 *   `ficha`);
 * - el `Hash` para `horario_sacd_ver.php`.
 *
 * Antes estaba duplicado byte a byte en tres controllers. Centralizarlo aqui
 * reduce ruido y evita que una rama se desincronice de las otras.
 */
final class SacdFichaAjaxHashes
{
    /**
     * Opciones del filtro de tipo SACD (compartidas por toda la carpeta).
     *
     * @return array<string, string>
     */
    public static function opcionesFiltroSacd(): array
    {
        return [
            'n' => 'n',
            'a' => 'agd',
            'sssc' => 'sss+',
            'cp_sss' => 'cp',
        ];
    }

    /**
     * `<select>` de `filtro_sacd` listo para la vista.
     */
    public static function desplegableFiltroSacd(string $seleccionado): Desplegable
    {
        $oDespl = new Desplegable();
        $oDespl->setNombre('filtro_sacd');
        $oDespl->setBlanco(false);
        $oDespl->setOpciones(self::opcionesFiltroSacd());
        $oDespl->setAction('fnjs_lista_sacd()');
        $oDespl->setOpcion_sel($seleccionado);

        return $oDespl;
    }

    /**
     * Rutas + hashes hacia `sacd_ficha_ajax.php` y `horario_sacd_ver.php`
     * que las tres pantallas pasan a sus vistas.
     *
     * @return array{
     *     url_ajax: string,
     *     h_ficha: string,
     *     h_lista: string,
     *     url_horario: string,
     *     h_horario: string,
     * }
     */
    public static function hashesComunes(): array
    {
        $url_ajax = 'frontend/encargossacd/controller/sacd_ficha_ajax.php';

        $oHashFicha = new Hash();
        $oHashFicha->setUrl($url_ajax);
        $oHashFicha->setCamposForm('que!id_nom');

        $oHashLst = new Hash();
        $oHashLst->setUrl($url_ajax);
        $oHashLst->setCamposForm('que!id_nom!filtro_sacd');

        $url_horario = 'frontend/encargossacd/controller/horario_sacd_ver.php';
        $oHashHorario = new Hash();
        $oHashHorario->setUrl($url_horario);
        $oHashHorario->setCamposForm('filtro_sacd!id_enc!id_nom');

        return [
            'url_ajax' => $url_ajax,
            'h_ficha' => $oHashFicha->linkSinVal(),
            'h_lista' => $oHashLst->linkSinVal(),
            'url_horario' => $url_horario,
            'h_horario' => $oHashHorario->linkSinVal(),
        ];
    }
}
