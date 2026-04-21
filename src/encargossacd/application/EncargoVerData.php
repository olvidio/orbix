<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Datos para la pantalla `encargo_ver` (nuevo / editar encargo).
 *
 * El frontend arma los `web\Desplegable` a partir de los arrays devueltos.
 */
final class EncargoVerData
{
    /**
     * @return array<string, mixed>
     */
    public static function execute(
        string $que,
        int $id_enc,
        int $id_tipo_enc,
        string $grupo,
        string $filtro_ctr,
        string $desc_enc,
        string $desc_lugar,
        int $id_zona,
    ): array {
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoTipoRepository = $GLOBALS['container']->get(EncargoTipoRepositoryInterface::class);
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
        $oAplicacion = new EncargoAplicacionService();

        $idioma_enc = '';
        $id_ubi = 0;

        if ($grupo === '8') {
            $filtro_ctr = '8';
        }

        if ($que === '' || $que === 'editar') {
            $oEncargo = $EncargoRepository->findById($id_enc);
            if ($oEncargo !== null) {
                $id_ubi = (int)$oEncargo->getId_ubi();
                $id_zona = (int)$oEncargo->getId_zona();
                $id_tipo_enc = (int)$oEncargo->getId_tipo_enc();
                $desc_enc = (string)$oEncargo->getDesc_enc();
                $desc_lugar = (string)$oEncargo->getDesc_lugar();
                $idioma_enc = (string)$oEncargo->getIdioma_enc();

                $sf_sv = (int)$oEncargo->getSf_sv();
                $filtro_ctr = (string)$sf_sv;
                if (($filtro_ctr === '' || $filtro_ctr === '0') && $id_ubi !== 0) {
                    $filtro_ctr = (string)self::calcularFiltroDesdeCentro($id_ubi);
                }
            }
        }

        if ($id_tipo_enc !== 0) {
            $tipo = $EncargoTipoRepository->encargo_de_tipo($id_tipo_enc);
            $grupo = (string)($tipo['grupo'] ?? '');
        } else {
            $id_tipo_enc = (int)$EncargoTipoRepository->id_tipo_encargo($grupo, '...');
        }

        $id_tipo_enc_txt = (string)$id_tipo_enc;
        $ee = $EncargoTipoRepository->encargo_de_tipo($id_tipo_enc_txt);
        if ($id_tipo_enc_txt !== '' && $id_tipo_enc_txt[0] === '.') {
            $grupo_posibles = is_array($ee['grupo'] ?? null) ? $ee['grupo'] : [];
        } else {
            $grupo = $id_tipo_enc_txt !== '' ? $id_tipo_enc_txt[0] : $grupo;
            $ee_grupo = $EncargoTipoRepository->encargo_de_tipo('....');
            $grupo_posibles = is_array($ee_grupo['grupo'] ?? null) ? $ee_grupo['grupo'] : [];
        }

        $posibles_encargo_tipo = [];
        if ($grupo !== '') {
            $cEncargoTipos = $EncargoTipoRepository->getEncargoTipos(
                ['id_tipo_enc' => '^' . $grupo],
                ['id_tipo_enc' => '~'],
            );
            if (is_array($cEncargoTipos)) {
                foreach ($cEncargoTipos as $oEncargoTipo) {
                    $posibles_encargo_tipo[(string)$oEncargoTipo->getId_tipo_enc()] = (string)$oEncargoTipo->getTipo_enc();
                }
            }
        }

        $opciones_seccion = $oAplicacion->getArraySeccion();
        $aOpciones_zonas = $ZonaRepository->getArrayZonas();
        $a_locales = $LocalRepository->getArrayLocales();

        return [
            'que' => $que,
            'id_enc' => $id_enc,
            'id_tipo_enc' => $id_tipo_enc,
            'grupo' => $grupo,
            'filtro_ctr' => $filtro_ctr,
            'desc_enc' => $desc_enc,
            'desc_lugar' => $desc_lugar,
            'idioma_enc' => $idioma_enc,
            'id_ubi' => $id_ubi,
            'id_zona' => $id_zona,
            'grupo_posibles' => self::arrayStringKeyed($grupo_posibles),
            'posibles_encargo_tipo' => self::arrayStringKeyed($posibles_encargo_tipo),
            'opciones_seccion' => self::arrayStringKeyed($opciones_seccion),
            'opciones_zonas' => self::arrayStringKeyed($aOpciones_zonas),
            'opciones_locales' => self::arrayStringKeyed($a_locales),
        ];
    }

    private static function calcularFiltroDesdeCentro(int $id_ubi): int
    {
        $id_ubi_str = (string)$id_ubi;
        if ((int)($id_ubi_str[0] ?? 0) === 2) {
            $repo = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        } else {
            $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        }

        $oCentro = $repo->findById($id_ubi);
        if ($oCentro === null) {
            return 0;
        }

        $tipo_ubi = $oCentro->getTipo_ubi();
        $tipo_ctr = $oCentro->getTipo_ctr();

        if ($tipo_ubi === 'ctrsf') {
            return EncargoGrupo::CENTRO_SF;
        }

        return match ($tipo_ctr) {
            'aj', 'am', 'nj', 'njce', 'nm', 'sj', 'sm', 'sjce' => EncargoGrupo::CENTRO_SV,
            'ss' => EncargoGrupo::CENTRO_SSSC,
            'igloc', 'igl' => EncargoGrupo::IGL,
            'cgioc', 'oc' => EncargoGrupo::CGI,
            default => 0,
        };
    }

    /**
     * @param array<int|string, mixed> $in
     * @return array<string, string>
     */
    private static function arrayStringKeyed(array $in): array
    {
        $out = [];
        foreach ($in as $k => $v) {
            $out[(string)$k] = (string)$v;
        }

        return $out;
    }
}
