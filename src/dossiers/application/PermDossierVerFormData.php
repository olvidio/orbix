<?php

namespace src\dossiers\application;

use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Formulario "permisos de acceso" para un tipo de dossier.
 *
 * El backend devuelve sólo datos:
 *  - `go_to_link_spec` ({path, query}) para que el frontend firme con HashFront.
 *  - `hash_config` (campos_form, campos_no, campos_hidden) para que el frontend componga el
 *    bloque hidden con HashFront; el valor de `go_to` dentro de `campos_hidden` se inyecta
 *    firmado en el borde del frontend.
 *
 * @return array<string, mixed>
 */
class PermDossierVerFormData
{
    public static function listaPermLinkSpec(string $tipo): array
    {
        return [
            'path' => 'frontend/dossiers/controller/perm_dossiers.php',
            'query' => ['tipo' => $tipo],
        ];
    }

    public static function build(int $Qid_tipo_dossier, string $Qtipo): array
    {
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

        $hashConfig = [
            'campos_form' => 'id_tipo_dossier!id_tipo_dossier_rel!tabla_from!tabla_to!campo_to!descripcion!app!class!codigo',
            'campos_no' => 'que!' . $campos_chk,
            // `campos_hidden` sin `go_to`: el frontend debe inyectarlo ya firmado para que la
            // firma del hash coincida con el HTML generado.
            'campos_hidden' => [
                'campos_chk' => $campos_chk,
            ],
        ];

        $txt_eliminar = _("¿Está seguro que desea eliminar este dossier?");

        return [
            'hash_config' => $hashConfig,
            'go_to_link_spec' => self::listaPermLinkSpec($Qtipo),
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
        ];
    }
}
