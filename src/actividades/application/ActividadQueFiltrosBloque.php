<?php

namespace src\actividades\application;

use src\actividades\application\ActividadLugar;
use src\shared\config\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;
use src\usuarios\domain\entity\Role;
use frontend\shared\web\Desplegable;

/**
 * Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza
 * + publicada) en la pantalla `actividad_que`. El bloque solo se muestra
 * a usuarios con permiso de control (`perm_ctr`); para el resto devuelve
 * cadena vacia.
 *
 * Encapsula todos los accesos a repositorios y entidades de dominio necesarios
 * (`Role`, `DelegacionDropdown`, `ActividadLugar`) de forma que el frontend
 * controller no tenga que depender directamente de `src/`.
 */
final class ActividadQueFiltrosBloque
{
    public function __construct(
        private ActividadLugar $actividadLugar,
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    public function ejecutar(
        int $sfsv,
        string $modo,
        string $dl_org,
        string $filtro_lugar,
        int $id_ubi,
        int $publicado,
        bool $proceso_installed
    ): string {
        if (!$this->tienePermisoControl()) {
            return '';
        }

        $mi_dele = ConfigGlobal::mi_delef((string) $sfsv);

        $oDesplFiltroLugar = Desplegable::desdeOpciones($this->delegacionDropdown->dlURegionesFiltro($sfsv), 'filtro_lugar');
        $oDesplFiltroLugar->setAction('fnjs_lugar()');
        $oDesplFiltroLugar->setOpcion_sel($filtro_lugar);

        $oDesplegableCasasHtml = '';
        if (!empty($filtro_lugar)) {
            $aOpcionesCasas = $this->actividadLugar->getLugaresPosibles($filtro_lugar);
            $oDesplegableCasas = Desplegable::desdeOpciones($aOpcionesCasas, 'id_ubi');
            $oDesplegableCasas->setBlanco(true);
            if (!empty($id_ubi)) {
                $oDesplegableCasas->setOpcion_sel($id_ubi);
            }
            $oDesplegableCasasHtml = $oDesplegableCasas->desplegable();
        }

        $oDesplDelegacionesOrg = Desplegable::desdeOpciones($this->delegacionDropdown->delegacionesURegiones($sfsv, true), 'dl_org');
        $oDesplDelegacionesOrg->setOpcion_sel($dl_org);
        if ($modo === 'importar') {
            $oDesplDelegacionesOrg->setOpcion_no([$mi_dele]);
        }
        if ($modo === 'publicar') {
            $oDesplDelegacionesOrg->setOpciones([$mi_dele => $mi_dele]);
            $oDesplDelegacionesOrg->setBlanco(false);
        }
        if ($proceso_installed) {
            $oDesplDelegacionesOrg->setAction('fnjs_actualizar_fases();');
        }

        $chk_publicado_1 = $publicado === 1 ? "checked='true'" : '';
        $chk_publicado_2 = $publicado === 2 ? "checked='true'" : '';
        $chk_publicado_3 = (!in_array($publicado, [1, 2], true)) ? "checked='true'" : '';

        $etiqueta_lugar_pais_dl = _("lugar según país o dl");
        $etiqueta_lugar = _("lugar");
        $etiqueta_organiza = _("organiza");
        $etiqueta_publicada = _("publicada");
        $txt_si = _("si");
        $txt_no = _("no");
        $txt_todas = _("todas");

        $html = '<tr>';
        $html .= '<td class="etiqueta">' . $etiqueta_lugar_pais_dl . ':</td>';
        $html .= '<td colspan="3">' . $oDesplFiltroLugar->desplegable() . '</td>';
        $html .= '<td class="etiqueta">' . $etiqueta_lugar . '</td>';
        $html .= '<td id="lst_lugar" colspan="1">' . $oDesplegableCasasHtml . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td class="etiqueta">' . $etiqueta_organiza . ':</td>';
        $html .= '<td colspan="3">' . $oDesplDelegacionesOrg->desplegable() . '</td>';
        if ($modo !== 'importar') {
            $html .= '<td>' . $etiqueta_publicada . ':';
            $html .= '<input type="radio" name="publicado" value="1" ' . $chk_publicado_1 . ' />' . $txt_si;
            $html .= '<input type="radio" name="publicado" value="2" ' . $chk_publicado_2 . ' />' . $txt_no;
            $html .= '<input type="radio" name="publicado" value="3" ' . $chk_publicado_3 . ' />' . $txt_todas;
            $html .= '</td>';
        }
        $html .= '</tr>';

        return $html;
    }

    private function tienePermisoControl(): bool
    {
        $oUsuario = ConfigGlobal::MiUsuario();
        $oRole = new Role();
        $oRole->setId_role($oUsuario?->getId_role() ?? 0);
        return !$oRole->isRolePau('ctr');
    }
}
