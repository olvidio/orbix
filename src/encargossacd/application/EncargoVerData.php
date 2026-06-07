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
 * El frontend arma los `frontend\shared\web\Desplegable` a partir de los arrays devueltos.
 */
final class EncargoVerData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private LocalRepositoryInterface $localRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private EncargoTipoRepositoryInterface $encargoTipoRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(
        string $que,
        int $id_enc,
        int $id_tipo_enc,
        string $grupo,
        string $filtro_ctr,
        string $desc_enc,
        string $desc_lugar,
        int $id_zona,
    ): array {
        $oAplicacion = $this->aplicacionService;

        $idioma_enc = '';
        $id_ubi = 0;

        if ($grupo === '8') {
            $filtro_ctr = '8';
        }

        if ($que === '' || $que === 'editar') {
            $oEncargo = $this->encargoRepository->findById($id_enc);
            if ($oEncargo !== null) {
                $id_ubi = (int)$oEncargo->getId_ubi();
                $id_zona = (int)$oEncargo->getId_zona();
                $id_tipo_enc = (int)$oEncargo->getId_tipo_enc();
                $desc_enc = (string)$oEncargo->getDesc_enc();
                $desc_lugar = (string)$oEncargo->getDesc_lugar();
                $idioma_enc = (string)$oEncargo->getIdioma_enc();

                $sf_sv = (int)$oEncargo->getGrupo_encargo();
                $filtro_ctr = (string) $sf_sv;
                if ($sf_sv === 0 && $id_ubi !== 0) {
                    $filtro_ctr = (string)$this->calcularFiltroDesdeCentro($id_ubi);
                }
            }
        }

        if ($id_tipo_enc !== 0) {
            $tipo = $this->encargoTipoRepository->encargo_de_tipo($id_tipo_enc);
            $grupo = is_scalar($tipo['grupo'] ?? null) ? (string) $tipo['grupo'] : '';
        } else {
            $id_tipo_enc = (int)$this->encargoTipoRepository->id_tipo_encargo($grupo, '...');
        }

        $id_tipo_enc_txt = (string)$id_tipo_enc;
        $ee = $this->encargoTipoRepository->encargo_de_tipo($id_tipo_enc_txt);
        if ($id_tipo_enc !== 0 && str_contains($id_tipo_enc_txt, '.')) {
            $grupo_posibles = is_array($ee['grupo'] ?? null) ? $ee['grupo'] : [];
        } else {
            $grupo = $id_tipo_enc !== 0 ? $id_tipo_enc_txt[0] : $grupo;
            $ee_grupo = $this->encargoTipoRepository->encargo_de_tipo('....');
            $grupo_posibles = is_array($ee_grupo['grupo'] ?? null) ? $ee_grupo['grupo'] : [];
        }

        $posibles_encargo_tipo = [];
        if ($grupo !== '') {
            $cEncargoTipos = $this->encargoTipoRepository->getEncargoTipos(
                ['id_tipo_enc' => '^' . $grupo],
                ['id_tipo_enc' => '~'],
            );
            if ($cEncargoTipos !== []) {
                foreach ($cEncargoTipos as $oEncargoTipo) {
                    $posibles_encargo_tipo[(string)$oEncargoTipo->getId_tipo_enc()] = (string)$oEncargoTipo->getTipo_enc();
                }
            }
        }

        $opciones_seccion = $oAplicacion->getArraySeccion();
        $aOpciones_zonas = $this->zonaRepository->getArrayZonas();
        $a_locales = $this->localRepository->getArrayLocales();

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
            'grupo_posibles' => $this->arrayStringKeyed($grupo_posibles),
            'posibles_encargo_tipo' => $this->arrayStringKeyed($posibles_encargo_tipo),
            'opciones_seccion' => $this->arrayStringKeyed($opciones_seccion),
            'opciones_zonas' => $this->arrayStringKeyed($aOpciones_zonas),
            'opciones_locales' => $this->arrayStringKeyed($a_locales),
        ];
    }

    private function calcularFiltroDesdeCentro(int $id_ubi): int
    {
        $id_ubi_str = (string)$id_ubi;
        if ((int)($id_ubi_str[0] ?? 0) === 2) {
            $repo = $this->centroEllasRepository;
        } else {
            $repo = $this->centroDlRepository;
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
    private function arrayStringKeyed(array $in): array
    {
        $out = [];
        foreach ($in as $k => $v) {
            if (!is_scalar($v)) {
                continue;
            }
            $out[(string) $k] = (string) $v;
        }

        return $out;
    }
}
