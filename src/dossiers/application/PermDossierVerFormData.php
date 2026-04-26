<?php

namespace src\dossiers\application;

use frontend\shared\config\AppUrlConfig;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use function src\shared\domain\helpers\is_true;
use web\Hash;

/**
 * Formulario "permisos de acceso" para un tipo de dossier.
 *
 * @return array<string, mixed>
 */
class PermDossierVerFormData
{
    public static function build(int $Qid_tipo_dossier, string $Qtipo): array
    {
        $a_dataUrl = ['tipo' => $Qtipo];
        $go_to = Hash::link(
            AppUrlConfig::getPublicAppBaseUrl() . '/frontend/dossiers/controller/perm_dossiers.php?' . http_build_query(
                $a_dataUrl
            )
        );
        $url_guardar = '/src/dossiers/tipo_dossier_guardar';
        $url_eliminar = '/src/dossiers/tipo_dossier_eliminar';

        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        $oTipoDossier = $TipoDossierRepository->findById($Qid_tipo_dossier);
        $depende_modificar = $oTipoDossier->isDepende_modificar();

        $botones = 0;
        $perm_admin = false;
        if (
            $_SESSION['oPerm']->have_perm_oficina('admin_sv') ||
            $_SESSION['oPerm']->have_perm_oficina('admin_sf')
        ) {
            $botones = "1,2";
            $perm_admin = true;
        }

        $oCuadros = new PermisoDossier();
        $permiso_lectura = $oTipoDossier->getPermiso_lectura();
        $permiso_escritura = $oTipoDossier->getPermiso_escritura();
        $permiso_lectura_html = $oCuadros->cuadros_check('permiso_lectura', $permiso_lectura);
        $permiso_escritura_html = $oCuadros->cuadros_check('permiso_escritura', $permiso_escritura);

        $chk = (is_true($depende_modificar)) ? 'checked' : '';
        $campos_chk = 'depende_modificar!permiso_lectura!permiso_escritura';
        $oHash = new Hash();
        $oHash->setCamposForm('id_tipo_dossier!id_tipo_dossier_rel!tabla_from!tabla_to!campo_to!descripcion!app!class!codigo');
        $oHash->setCamposNo('que!' . $campos_chk);
        $a_camposHidden = [
            'go_to' => $go_to,
            'campos_chk' => $campos_chk,
        ];
        $oHash->setArraycamposHidden($a_camposHidden);
        $txt_eliminar = _("¿Está seguro que desea eliminar este dossier?");

        return [
            'hash_campos_html' => $oHash->getCamposHtml(),
            'permiso_lectura_html' => $permiso_lectura_html,
            'permiso_escritura_html' => $permiso_escritura_html,
            'url_guardar' => $url_guardar,
            'url_eliminar' => $url_eliminar,
            'txt_eliminar' => $txt_eliminar,
            'perm_admin' => $perm_admin,
            'id_tipo_dossier' => $Qid_tipo_dossier,
            'descripcion' => $oTipoDossier->getDescripcion(),
            'tabla_from' => $oTipoDossier->getTabla_from(),
            'tabla_to' => $oTipoDossier->getTabla_to(),
            'campo_to' => $oTipoDossier->getCampo_to(),
            'id_tipo_dossier_rel' => $oTipoDossier->getId_tipo_dossier_rel(),
            'permiso_lectura' => $permiso_lectura,
            'permiso_escritura' => $permiso_escritura,
            'app' => $oTipoDossier->getApp(),
            'class' => $oTipoDossier->getClass(),
            'codigo' => $oTipoDossier->getCodigo() ?? '',
            'chk' => $chk,
            'botones' => $botones,
            'go_to' => $go_to,
        ];
    }
}
