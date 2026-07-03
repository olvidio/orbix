<?php

namespace src\dossiers\application;

use src\dossiers\domain\PermisoDossierBits;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\permisos\domain\XPermisos;

/**
 * Formulario "permisos de acceso" para un tipo de dossier.
 */
class PermDossierVerFormData
{
    public function __construct(
        private TipoDossierRepositoryInterface $tipoDossierRepository,
    ) {
    }

    /**
     * @return array{path: string, query: array<string, string>}
     */
    public static function listaPermLinkSpec(string $tipo): array
    {
        return [
            'path' => 'frontend/dossiers/controller/perm_dossiers.php',
            'query' => ['tipo' => $tipo],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function build(int $Qid_tipo_dossier, string $Qtipo): array
    {
        $url_guardar = '/src/dossiers/tipo_dossier_guardar';
        $url_eliminar = '/src/dossiers/tipo_dossier_eliminar';

        $oTipoDossier = $this->tipoDossierRepository->findById($Qid_tipo_dossier);
        if ($oTipoDossier === null) {
            throw new \RuntimeException(sprintf('No se encuentra el dossier: %s', $Qid_tipo_dossier));
        }
        $depende_modificar = $oTipoDossier->isDepende_modificar();

        $botones = 0;
        $perm_admin = false;
        $oPerm = $_SESSION['oPerm'] ?? null;
        if (
            $oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('admin_sv') || $oPerm->have_perm_oficina('admin_sf'))
        ) {
            $botones = '1,2';
            $perm_admin = true;
        }

        $permiso_lectura = $oTipoDossier->getPermiso_lectura();
        $permiso_escritura = $oTipoDossier->getPermiso_escritura() ?? 0;

        $chk = \src\shared\domain\helpers\FuncTablasSupport::isTrue($depende_modificar) ? 'checked' : '';
        $campos_chk = 'depende_modificar!permiso_lectura!permiso_escritura';

        $hashConfig = [
            'campos_form' => 'id_tipo_dossier!id_tipo_dossier_rel!tabla_from!tabla_to!campo_to!descripcion!app!class!codigo',
            'campos_no' => 'que!' . $campos_chk,
            'campos_hidden' => [
                'campos_chk' => $campos_chk,
            ],
        ];

        $txt_eliminar = _('¿Está seguro que desea eliminar este dossier?');

        return [
            'hash_config' => $hashConfig,
            'go_to_link_spec' => self::listaPermLinkSpec($Qtipo),
            'permiso_dossier_bit_map' => PermisoDossierBits::labeledMap(),
            'url_guardar' => $url_guardar,
            'url_eliminar' => $url_eliminar,
            'txt_eliminar' => $txt_eliminar,
            'perm_admin' => $perm_admin,
            'id_tipo_dossier' => $Qid_tipo_dossier,
            'descripcion' => $oTipoDossier->getDescripcion() ?? '',
            'tabla_from' => $oTipoDossier->getTabla_from(),
            'tabla_to' => $oTipoDossier->getTabla_to() ?? '',
            'campo_to' => $oTipoDossier->getCampo_to() ?? '',
            'id_tipo_dossier_rel' => $oTipoDossier->getId_tipo_dossier_rel() ?? 0,
            'permiso_lectura' => $permiso_lectura,
            'permiso_escritura' => $permiso_escritura,
            'app' => $oTipoDossier->getApp() ?? '',
            'class' => $oTipoDossier->getClass() ?? '',
            'codigo' => $oTipoDossier->getCodigo() ?? '',
            'chk' => $chk,
            'botones' => $botones,
        ];
    }
}
