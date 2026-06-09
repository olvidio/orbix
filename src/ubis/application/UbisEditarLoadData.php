<?php

namespace src\ubis\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\shared\config\ConfigGlobal;
use src\ubis\application\services\UbiPermisos;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEx;
use src\ubis\domain\CuadrosLaborBits;
use function src\shared\domain\helpers\is_true;

/**
 * Carga ficha ubis (centro/casa) para `frontend/ubis/controller/ubis_editar.php`
 * sin repositorios en el frontend.
 *
 * @return array<string, mixed>
 */
final class UbisEditarLoadData
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private UbisEditarNormalizeDlData $ubisEditarNormalizeDlData,
    ) {
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public function execute(array $post): array
    {
        $Qid_ubi = input_int($post, 'id_ubi');
        $Qobj_pau = input_string($post, 'obj_pau');
        $Qnuevo = input_string($post, 'nuevo');
        $tipo_ubi_in = input_string($post, 'tipo_ubi');

        if (!empty($Qnuevo)) {
            return self::buildNuevo($post, $Qobj_pau, $tipo_ubi_in);
        }

        if ($Qobj_pau === '') {
            throw new \RuntimeException(_('falta definir obj_pau'));
        }

        $repo = $this->repositoryFor($Qobj_pau);
        $oUbi = $repo->findById($Qid_ubi);
        if ($oUbi === null) {
            throw new \RuntimeException(sprintf(_('No se encuentra ubi id %s'), (string)$Qid_ubi));
        }

        $tipo_ubi = (string)$oUbi->getTipo_ubi();
        $dl = (string)($oUbi->getDl() ?? '');
        $id_ubi = (int)$oUbi->getId_ubi();
        $region = (string)($oUbi->getRegion() ?? '');
        $nombre_ubi = (string)$oUbi->getNombre_ubi();

        $es_de_dl = UbiPermisos::dlPerteneceAMiDelegacion($dl);
        if (str_contains($tipo_ubi, 'ctr') && !$es_de_dl) {
            $tipo_ubi = 'ctrex';
            $Qobj_pau = 'CentroEx';
        }
        if (str_contains($tipo_ubi, 'cdc') && !$es_de_dl) {
            $tipo_ubi = 'cdcex';
            $Qobj_pau = 'CasaEx';
        }
        if ($es_de_dl) {
            $Qobj_pau = $this->ubisEditarNormalizeDlData->execute($id_ubi, $tipo_ubi, $nombre_ubi, $Qobj_pau);
        }

        $botones = self::computeBotones($Qobj_pau, '', $oUbi);

        $base = [
            'tipo_ubi' => $tipo_ubi,
            'obj_pau' => $Qobj_pau,
            'id_ubi' => $id_ubi,
            'id_direccion' => '',
            'es_de_dl' => $es_de_dl,
            'botones' => $botones,
        ];

        $laborMap = CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv());

        return match ($tipo_ubi) {
            'ctrdl', 'ctrsf' => array_merge($base, match (true) {
                $oUbi instanceof CentroDl => self::serializeCentroDlFields($oUbi),
                $oUbi instanceof Centro => self::serializeCentroFields($oUbi),
                default => throw new \RuntimeException(_('tipo de entidad inesperado para centro dl')),
            }, ['tipo_labor_bit_map' => $laborMap]),
            'ctrex' => array_merge($base, match (true) {
                $oUbi instanceof CentroEx => self::serializeCentroExFields($oUbi),
                $oUbi instanceof CentroDl => self::serializeCentroDlFields($oUbi),
                $oUbi instanceof Centro => self::serializeCentroFields($oUbi),
                default => throw new \RuntimeException(_('tipo de entidad inesperado para centro ex')),
            }, ['tipo_labor_bit_map' => $laborMap]),
            'cdcdl', 'cdcex' => array_merge($base, self::serializeCasaFields(
                $oUbi instanceof Casa ? $oUbi : throw new \RuntimeException(_('tipo de entidad inesperado para casa'))
            )),
            default => throw new \RuntimeException('tipo_ubi no soportado: ' . $tipo_ubi),
        };
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private static function buildNuevo(array $post, string $Qobj_pau, string $tipo_ubi): array
    {
        $tipo_ubi_in = $tipo_ubi;
        if ($Qobj_pau === '') {
            $Qobj_pau = match ($tipo_ubi_in) {
                'ctrdl', 'ctrsf' => 'CentroDl',
                'ctrex' => 'CentroEx',
                'cdcdl' => 'CasaDl',
                'cdcex' => 'CasaEx',
                default => '',
            };
        }
        if ($Qobj_pau === '') {
            throw new \RuntimeException(_('falta definir obj_pau'));
        }

        $dl = input_string($post, 'dl');
        $region = input_string($post, 'region');
        $nombre_ubi = input_string($post, 'nombre_ubi');
        $nombre_ubi = urldecode($nombre_ubi);

        if ($dl === '' && str_contains($Qobj_pau, 'Dl')) {
            if (str_contains($tipo_ubi_in, 'ctr')) {
                $dl = ConfigGlobal::mi_delef();
            }
            if (str_contains($tipo_ubi_in, 'cdc')) {
                $dl = ConfigGlobal::mi_dele();
            }
        }
        if ($region === '' && str_contains($Qobj_pau, 'Dl')) {
            $region = ConfigGlobal::mi_region();
        }

        $botones = self::computeBotones($Qobj_pau, input_string($post, 'nuevo'), null, $dl);

        $base = [
            'tipo_ubi' => $tipo_ubi_in,
            'obj_pau' => $Qobj_pau,
            'id_ubi' => '',
            'id_direccion' => '',
            'es_de_dl' => str_contains($Qobj_pau, 'Dl'),
            'botones' => $botones,
            'chk' => 'checked',
        ];

        $sv = false;
        $sf = false;
        if (str_contains($tipo_ubi_in, 'cdc')) {
            if (ConfigGlobal::mi_sfsv() === 1) {
                $sv = true;
            }
            if (ConfigGlobal::mi_sfsv() === 2) {
                $sf = true;
            }
        }

        $laborMap = CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv());

        return match ($tipo_ubi_in) {
            'ctrdl', 'ctrsf' => array_merge($base, [
                'dl' => $dl === '' ? ConfigGlobal::mi_delef() : $dl,
                'region' => $region === '' ? ConfigGlobal::mi_region() : $region,
                'nombre_ubi' => $nombre_ubi,
                'chk_cdc' => '',
                'tipo_labor' => null,
                'tipo_labor_bit_map' => $laborMap,
                'id_ctr_padre' => null,
                'tipo_ctr' => null,
                'num_pi' => null,
                'num_cartas' => null,
                'num_cartas_mensuales' => null,
                'num_habit_indiv' => null,
                'plazas' => null,
                'n_buzon' => null,
                'observ' => null,
            ]),
            'ctrex' => array_merge($base, [
                'dl' => $dl,
                'region' => $region,
                'nombre_ubi' => $nombre_ubi,
                'chk_cdc' => '',
                'tipo_labor' => null,
                'tipo_labor_bit_map' => $laborMap,
                'id_ctr_padre' => null,
                'tipo_ctr' => null,
            ]),
            'cdcdl', 'cdcex' => array_merge($base, [
                'dl' => $tipo_ubi_in === 'cdcdl' ? ($dl === '' ? ConfigGlobal::mi_dele() : $dl) : $dl,
                'region' => $tipo_ubi_in === 'cdcdl' ? ($region === '' ? ConfigGlobal::mi_region() : $region) : $region,
                'nombre_ubi' => $nombre_ubi,
                'tipo_casa' => null,
                'plazas' => null,
                'plazas_min' => null,
                'num_sacd' => null,
                'sv_chk' => is_true($sv) ? 'checked' : '',
                'sf_chk' => is_true($sf) ? 'checked' : '',
            ]),
            default => throw new \RuntimeException('tipo_ubi no soportado: ' . $tipo_ubi_in),
        };
    }

    private static function computeBotones(string $Qobj_pau, string $Qnuevo, Centro|CentroDl|CentroEx|Casa|null $oUbi, string $dlOverride = ''): int|string
    {
        if ($Qnuevo !== '') {
            $dl = $dlOverride;
            $obj = UbiPermisos::normalizeObjPau($Qobj_pau);
            if ($dl === '' && in_array($obj, ['CentroDl', 'CasaDl'], true)) {
                $dl = str_contains($obj, 'Centro') ? ConfigGlobal::mi_delef() : ConfigGlobal::mi_dele();
            }

            return UbiPermisos::puedeModificarPorObjeto($Qobj_pau, $dl) ? '1,2' : 0;
        }

        $dl = $oUbi !== null ? (string) ($oUbi->getDl() ?? '') : '';

        return UbiPermisos::puedeModificarPorObjeto($Qobj_pau, $dl) ? '1,2' : 0;
    }

    private function repositoryFor(string $obj_pau): CentroRepositoryInterface|CentroDlRepositoryInterface|CentroExRepositoryInterface|CasaRepositoryInterface|CasaDlRepositoryInterface|CasaExRepositoryInterface
    {
        return $this->ubiRepositoryResolver->getRepository($obj_pau);
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeCentroDlFields(CentroDl $o): array
    {
        return [
            'chk' => $o->isActive() ? 'checked' : '',
            'dl' => $o->getDl(),
            'region' => $o->getRegion(),
            'nombre_ubi' => $o->getNombre_ubi(),
            'chk_cdc' => is_true($o->isCdc()) ? 'checked' : '',
            'tipo_labor' => $o->getTipo_labor(),
            'id_ctr_padre' => $o->getId_ctr_padre(),
            'tipo_ctr' => $o->getTipo_ctr(),
            'num_pi' => $o->getNum_pi(),
            'num_cartas' => $o->getNum_cartas(),
            'num_cartas_mensuales' => $o->getNum_cartas_mensuales(),
            'num_habit_indiv' => $o->getNum_habit_indiv(),
            'plazas' => $o->getPlazas(),
            'n_buzon' => $o->getN_buzon(),
            'observ' => $o->getObserv(),
        ];
    }

    /**
     * Ficha centro legacy (`obj_pau` = Centro) en vista sv/sf, sin campos numéricos de CentroDl.
     *
     * @return array<string, mixed>
     */
    private static function serializeCentroFields(Centro $o): array
    {
        return [
            'chk' => $o->isActive() ? 'checked' : '',
            'dl' => $o->getDl(),
            'region' => $o->getRegion(),
            'nombre_ubi' => $o->getNombre_ubi(),
            'chk_cdc' => is_true($o->isCdc()) ? 'checked' : '',
            'tipo_labor' => $o->getTipo_labor(),
            'id_ctr_padre' => $o->getId_ctr_padre(),
            'tipo_ctr' => $o->getTipo_ctr(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeCentroExFields(CentroEx $o): array
    {
        return [
            'chk' => $o->isActive() ? 'checked' : '',
            'dl' => $o->getDl(),
            'region' => $o->getRegion(),
            'nombre_ubi' => $o->getNombre_ubi(),
            'chk_cdc' => is_true($o->isCdc()) ? 'checked' : '',
            'tipo_labor' => $o->getTipo_labor(),
            'id_ctr_padre' => $o->getId_ctr_padre(),
            'tipo_ctr' => $o->getTipo_ctr(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function serializeCasaFields(Casa $o): array
    {
        $sv = $o->isSv();
        $sf = $o->isSf();

        return [
            'chk' => $o->isActive() ? 'checked' : '',
            'dl' => $o->getDl(),
            'region' => $o->getRegion(),
            'nombre_ubi' => $o->getNombre_ubi(),
            'tipo_casa' => $o->getTipo_casa(),
            'plazas' => $o->getPlazas(),
            'plazas_min' => $o->getPlazas_min(),
            'num_sacd' => $o->getNum_sacd(),
            'sv_chk' => is_true($sv) ? 'checked' : '',
            'sf_chk' => is_true($sf) ? 'checked' : '',
        ];
    }
}
