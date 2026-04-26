<?php

namespace src\usuarios\application;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

/**
 * Devuelve las preferencias de usuario necesarias para renderizar una tabla
 * (HTML simple o SlickGrid) en el front.
 *
 * Entrada:
 *  - `id_tabla` (opcional): identificador del grid. Si viene vacío, no se
 *    devolverán preferencias específicas del grid (útil cuando sólo se
 *    necesita saber si el usuario prefiere HTML o SlickGrid).
 *
 * Salida: array asociativo con la forma:
 *   [
 *       'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion'
 *       'slickgrid'     => null|array,            // prefs 'slickGrid_<id_tabla>_<idioma>'
 *   ]
 *
 * Para slickgrid se busca primero la preferencia del usuario actual; si no
 * existe, se usa la del usuario 44 (default).
 */
final class PreferenciaTablaData
{
    public static function execute(string $id_tabla = ''): array
    {
        $repo = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $id_usuario = ConfigGlobal::mi_id_usuario();

        $formato_tabla = '';
        $oPref = $repo->findById($id_usuario, 'tabla_presentacion');
        if ($oPref !== null) {
            $formato_tabla = (string)$oPref->getPreferenciaVo()->value();
        }

        $slickgrid = null;
        if ($id_tabla !== '') {
            $idioma = ConfigGlobal::mi_Idioma();
            $tipo = 'slickGrid_' . $id_tabla . '_' . $idioma;
            foreach ([$id_usuario, 44] as $uid) {
                $oPref = $repo->findById((int)$uid, $tipo);
                if ($oPref === null) {
                    continue;
                }
                $sPrefs = (string)$oPref->getPreferenciaVo()->value();
                if ($sPrefs === '') {
                    continue;
                }
                $aPrefs = json_decode($sPrefs, true);
                if (is_array($aPrefs)) {
                    $slickgrid = $aPrefs;
                    break;
                }
            }
        }

        return [
            'formato_tabla' => $formato_tabla,
            'slickgrid' => $slickgrid,
        ];
    }
}
