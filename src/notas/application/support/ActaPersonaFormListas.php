<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\PersonaPublicacion;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Opciones de sigla DL para el formulario notas de persona (acta / certificado).
 */
final class ActaPersonaFormListas implements SiglaActaPermitida
{
    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * @return array{
     *   mi_sigla: string,
     *   dl_sin_esquema: array<string, string>,
     *   opciones_certificado_dl: array<string, string>,
     *   siglas_acta_permitidas: list<string>
     * }
     */
    public function listas(): array
    {
        $miSigla = ConfigGlobal::mi_delef();
        $dlSinEsquema = $this->delegacionesSinEsquemaOrbix();
        $siglasActa = array_values(array_unique(array_merge([$miSigla], array_keys($dlSinEsquema))));

        return [
            'mi_sigla' => $miSigla,
            'dl_sin_esquema' => $dlSinEsquema,
            'opciones_certificado_dl' => $this->siglasEsquemaOrbixExceptoPropia(),
            'siglas_acta_permitidas' => $siglasActa,
        ];
    }

    /**
     * @return array{
     *   acta_sigla_sel: string,
     *   acta_num: string,
     *   acta_cert_dl_sel: string,
     *   acta_cert_num: string
     * }
     */
    public function valoresDesdeActa(?string $acta, int $tipoActa): array
    {
        $listas = $this->listas();
        $miSigla = $listas['mi_sigla'];
        $acta = trim((string) $acta);
        $vacios = [
            'acta_sigla_sel' => $miSigla,
            'acta_num' => '',
            'acta_cert_dl_sel' => '',
            'acta_cert_num' => '',
        ];
        if ($acta === '') {
            return $vacios;
        }

        if ($tipoActa === TipoActa::FORMATO_CERTIFICADO) {
            return $this->valoresCertificadoDesdeActa($acta, $listas['opciones_certificado_dl'], $vacios);
        }

        $pref = ActaPrefijosDeEsquema::prefijoDeActa($acta);
        $num = ActaPrefijosDeEsquema::numeroDeActa($acta);

        return [
            'acta_sigla_sel' => $pref !== '' ? $pref : $miSigla,
            'acta_num' => $num,
            'acta_cert_dl_sel' => '',
            'acta_cert_num' => '',
        ];
    }

    public function siglaPermitidaEnActa(string $sigla): bool
    {
        return in_array($sigla, $this->listas()['siglas_acta_permitidas'], true);
    }

    /**
     * En certificados, las regiones se listan sin el prefijo «cr» (p. ej. crGalbel → Galbel).
     * En actas (tipo 1) el prefijo «cr» sí forma parte de la sigla.
     */
    public static function siglaCertificadoSinPrefijoCr(string $sigla): string
    {
        $sigla = trim($sigla);
        if (strlen($sigla) > 2 && strncasecmp($sigla, 'cr', 2) === 0) {
            return substr($sigla, 2);
        }

        return $sigla;
    }

    /**
     * @param array<string, string> $opcionesCertDl
     * @param array{acta_sigla_sel: string, acta_num: string, acta_cert_dl_sel: string, acta_cert_num: string} $vacios
     * @return array{acta_sigla_sel: string, acta_num: string, acta_cert_dl_sel: string, acta_cert_num: string}
     */
    private function valoresCertificadoDesdeActa(string $acta, array $opcionesCertDl, array $vacios): array
    {
        $pref = ActaPrefijosDeEsquema::prefijoDeActa($acta);
        if ($pref !== '') {
            $prefNorm = self::siglaCertificadoSinPrefijoCr(PersonaPublicacion::normalizarDl($pref));
            if ($prefNorm !== '' && isset($opcionesCertDl[$prefNorm])) {
                $vacios['acta_cert_dl_sel'] = $prefNorm;
                $resto = trim(substr($acta, strlen($pref)));
                $num = ActaPrefijosDeEsquema::numeroDeActa($acta);
                $vacios['acta_cert_num'] = $num !== '' ? $num : ltrim($resto);

                return $vacios;
            }
        }

        $keys = array_keys($opcionesCertDl);
        usort($keys, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        foreach ($keys as $dlKey) {
            if ($acta === $dlKey || str_starts_with($acta, $dlKey . ' ')) {
                $resto = trim(substr($acta, strlen($dlKey)));
                $vacios['acta_cert_dl_sel'] = $dlKey;
                $vacios['acta_cert_num'] = ltrim($resto);

                return $vacios;
            }
        }

        if ($pref !== '') {
            $prefNorm = self::siglaCertificadoSinPrefijoCr(PersonaPublicacion::normalizarDl($pref));
            $vacios['acta_cert_dl_sel'] = $prefNorm !== '' ? $prefNorm : $pref;
            $num = ActaPrefijosDeEsquema::numeroDeActa($acta);
            $vacios['acta_cert_num'] = $num !== '' ? $num : ltrim(trim(substr($acta, strlen($pref))));

            return $vacios;
        }

        $vacios['acta_cert_num'] = $acta;

        return $vacios;
    }

    /**
     * Siglas DL de esquemas Orbix existentes (lista del login), excepto la propia.
     *
     * @return array<string, string> sigla => sigla
     */
    private function siglasEsquemaOrbixExceptoPropia(): array
    {
        $miDelef = ConfigGlobal::mi_delef();
        $miDele = ConfigGlobal::mi_dele();
        $dbProp = new DBPropiedades();
        $aDl = $dbProp->array_posibles_dl_de_esquemas(false, true);
        if (!is_array($aDl)) {
            return [];
        }

        $out = [];
        foreach ($aDl as $dl => $_label) {
            $sigla = PersonaPublicacion::normalizarDl((string) $dl);
            if ($sigla === '' || $this->esSiglaPropia($sigla, $miDelef, $miDele)) {
                continue;
            }
            $sigla = self::siglaCertificadoSinPrefijoCr($sigla);
            if ($sigla === '') {
                continue;
            }
            $out[$sigla] = $sigla;
        }
        ksort($out, SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }

    private function esSiglaPropia(string $dlStr, string $miDelef, string $miDele): bool
    {
        $dlNorm = PersonaPublicacion::normalizarDl($dlStr);
        $miDelefNorm = PersonaPublicacion::normalizarDl($miDelef);
        if ($dlNorm === $miDelefNorm || $dlNorm === $miDele) {
            return true;
        }

        return false;
    }

    /**
     * DL activas (tabla delegaciones) sin esquema Orbix.
     *
     * @return array<string, string> sigla => etiqueta
     */
    private function delegacionesSinEsquemaOrbix(): array
    {
        $miDele = ConfigGlobal::mi_dele();
        $sfsv = ConfigGlobal::mi_sfsv();
        $suffix = $sfsv === 2 ? 'f' : 'v';
        $database = $sfsv === 2 ? 'sf' : 'sv';
        $oConfigDB = new ConfigDB($database);

        $delegaciones = $this->delegacionRepository->getDelegaciones(
            ['active' => true, '_ordre' => 'dl'],
        );

        $out = [];
        foreach ($delegaciones as $delegacion) {
            $dlCode = (string) ($delegacion->getDlVo()->value() ?? '');
            $region = (string) ($delegacion->getRegionVo()->value() ?? '');
            if ($dlCode === '' || $region === '' || $dlCode === $miDele) {
                continue;
            }
            if (strcasecmp($dlCode, 'Otra') === 0) {
                continue;
            }
            if ($this->delegacionTieneEsquemaOrbix($region, $dlCode, $oConfigDB, $suffix)) {
                continue;
            }
            $sigla = $dlCode . ($sfsv === 2 ? 'f' : '');
            $out[$sigla] = $sigla;
        }

        ksort($out, SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }

    private function delegacionTieneEsquemaOrbix(
        string $region,
        string $dlCode,
        ConfigDB $oConfigDB,
        string $suffix,
    ): bool {
        return $oConfigDB->tieneEsquema($region . '-' . $dlCode . $suffix);
    }
}
