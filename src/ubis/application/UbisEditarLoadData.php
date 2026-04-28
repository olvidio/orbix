<?php

namespace src\ubis\application;

use frontend\shared\config\OrbixRuntime;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEx;
use function src\shared\domain\helpers\is_true;

/**
 * Carga ficha ubis (centro/casa) para `frontend/ubis/controller/ubis_editar.php`
 * sin repositorios en el frontend.
 *
 * @return array<string, mixed>
 */
final class UbisEditarLoadData
{
    /**
     * @param array<string, mixed> $post
     */
    public static function execute(array $post): array
    {
        $Qid_ubi = (int)($post['id_ubi'] ?? 0);
        $Qobj_pau = (string)($post['obj_pau'] ?? '');
        $Qnuevo = (string)($post['nuevo'] ?? '');
        $tipo_ubi_in = (string)($post['tipo_ubi'] ?? '');

        if (!empty($Qnuevo)) {
            return self::buildNuevo($post, $Qobj_pau, $tipo_ubi_in);
        }

        if ($Qobj_pau === '') {
            throw new \RuntimeException(_('falta definir obj_pau'));
        }

        $repo = self::repositoryFor($Qobj_pau);
        $oUbi = $repo->findById($Qid_ubi);
        if ($oUbi === null) {
            throw new \RuntimeException(sprintf(_('No se encuentra ubi id %s'), (string)$Qid_ubi));
        }

        $tipo_ubi = (string)$oUbi->getTipo_ubi();
        $dl = (string)($oUbi->getDl() ?? '');
        $id_ubi = (int)$oUbi->getId_ubi();
        $region = (string)($oUbi->getRegion() ?? '');
        $nombre_ubi = (string)$oUbi->getNombre_ubi();

        $es_de_dl = false;
        if (str_contains($tipo_ubi, 'ctr')) {
            if ($dl === OrbixRuntime::miDelef()) {
                $es_de_dl = true;
            } else {
                $tipo_ubi = 'ctrex';
            }
        }
        if (str_contains($tipo_ubi, 'cdc')) {
            if ($dl === OrbixRuntime::miDele()) {
                $es_de_dl = true;
            } else {
                $tipo_ubi = 'cdcex';
            }
        }
        if ($es_de_dl) {
            $Qobj_pau = UbisEditarNormalizeDlData::execute($id_ubi, $tipo_ubi, $nombre_ubi, $Qobj_pau);
        }

        $botones = self::computeBotones($Qobj_pau, '', $es_de_dl);

        $base = [
            'tipo_ubi' => $tipo_ubi,
            'obj_pau' => $Qobj_pau,
            'id_ubi' => $id_ubi,
            'id_direccion' => '',
            'es_de_dl' => $es_de_dl,
            'botones' => $botones,
        ];

        return match ($tipo_ubi) {
            'ctrdl', 'ctrsf' => array_merge($base, self::serializeCentroDlFields(
                $oUbi instanceof CentroDl ? $oUbi : throw new \RuntimeException(_('tipo de entidad inesperado para centro dl'))
            )),
            'ctrex' => array_merge($base, self::serializeCentroExFields(
                $oUbi instanceof CentroEx ? $oUbi : throw new \RuntimeException(_('tipo de entidad inesperado para centro ex'))
            )),
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

        $dl = (string)($post['dl'] ?? '');
        $region = (string)($post['region'] ?? '');
        $nombre_ubi = (string)($post['nombre_ubi'] ?? '');
        $nombre_ubi = urldecode($nombre_ubi);

        if ($dl === '' && str_contains($Qobj_pau, 'Dl')) {
            if (str_contains($tipo_ubi_in, 'ctr')) {
                $dl = OrbixRuntime::miDelef();
            }
            if (str_contains($tipo_ubi_in, 'cdc')) {
                $dl = OrbixRuntime::miDele();
            }
        }
        if ($region === '' && str_contains($Qobj_pau, 'Dl')) {
            $region = OrbixRuntime::miRegion();
        }

        $botones = self::computeBotones($Qobj_pau, (string)($post['nuevo'] ?? ''), false);

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
            if (OrbixRuntime::miSfsv() === 1) {
                $sv = true;
            }
            if (OrbixRuntime::miSfsv() === 2) {
                $sf = true;
            }
        }

        return match ($tipo_ubi_in) {
            'ctrdl', 'ctrsf' => array_merge($base, [
                'dl' => $dl === '' ? OrbixRuntime::miDelef() : $dl,
                'region' => $region === '' ? OrbixRuntime::miRegion() : $region,
                'nombre_ubi' => $nombre_ubi,
                'chk_cdc' => '',
                'tipo_labor' => null,
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
                'id_ctr_padre' => null,
                'tipo_ctr' => null,
            ]),
            'cdcdl', 'cdcex' => array_merge($base, [
                'dl' => $tipo_ubi_in === 'cdcdl' ? ($dl === '' ? OrbixRuntime::miDele() : $dl) : $dl,
                'region' => $tipo_ubi_in === 'cdcdl' ? ($region === '' ? OrbixRuntime::miRegion() : $region) : $region,
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

    private static function computeBotones(string $Qobj_pau, string $Qnuevo, bool $es_de_dl): int|string
    {
        $botones = 0;
        if (str_contains($Qobj_pau, 'Dl')) {
            if ($Qnuevo !== '' || $es_de_dl) {
                if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
                    $botones = '1,2';
                }
            }
        } elseif (str_contains($Qobj_pau, 'Ex')) {
            if ($_SESSION['oPerm']->have_perm_oficina('scdl')) {
                $botones = '1,2';
            }
        }

        return $botones;
    }

    private static function repositoryFor(string $obj_pau): CentroDlRepositoryInterface|CentroExRepositoryInterface|CasaDlRepositoryInterface|CasaExRepositoryInterface
    {
        return match ($obj_pau) {
            'CentroDl' => $GLOBALS['container']->get(CentroDlRepositoryInterface::class),
            'CentroEx' => $GLOBALS['container']->get(CentroExRepositoryInterface::class),
            'CasaDl' => $GLOBALS['container']->get(CasaDlRepositoryInterface::class),
            'CasaEx' => $GLOBALS['container']->get(CasaExRepositoryInterface::class),
            default => throw new \InvalidArgumentException('obj_pau ubi no válido: ' . $obj_pau),
        };
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
