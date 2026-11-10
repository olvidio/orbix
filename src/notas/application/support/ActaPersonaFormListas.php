<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\PersonaPublicacion;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\criteria\DelegacionField;

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
        $delegaciones = $this->delegacionRepository->matching(
            Criteria::create()->orderBy(DelegacionField::Dl),
        );
        $estados = [];
        foreach ($delegaciones as $delegacion) {
            $estados[] = [
                (string) ($delegacion->getDlVo()->value() ?? ''),
                (bool) $delegacion->isActive(),
            ];
        }
        $sinEsquema = $this->delegacionesSinEsquemaOrbix($delegaciones);
        $dlSinEsquema = self::siglasActaSinEsquema($sinEsquema, ConfigGlobal::mi_sfsv() === 2);
        $siglasActa = array_values(array_unique(array_merge([$miSigla], array_keys($dlSinEsquema))));

        $opcionesCertificado = array_diff_key(
            $this->siglasEsquemaOrbixExceptoPropia(),
            self::siglasCertificadoInactivas($estados),
        ) + self::siglasCertificadoSinEsquema($sinEsquema);
        ksort($opcionesCertificado, SORT_NATURAL | SORT_FLAG_CASE);

        return [
            'mi_sigla' => $miSigla,
            'dl_sin_esquema' => $dlSinEsquema,
            'opciones_certificado_dl' => self::conSiglaRegionDelEsquema(
                $opcionesCertificado,
                ConfigGlobal::mi_region(),
            ),
            'siglas_acta_permitidas' => $siglasActa,
        ];
    }

    /**
     * Una sigla «dl…» es una delegación; el resto (crB, cg, …) son regiones u
     * organismos que no levantan actas en Orbix: sus notas llegan como certificado.
     */
    public static function esSiglaDeDelegacion(string $sigla): bool
    {
        return strncasecmp(trim($sigla), 'dl', 2) === 0;
    }

    /**
     * Delegaciones sin esquema Orbix que se ofrecen como sigla de acta (p. ej. dlu).
     *
     * @param list<string> $siglasSinEsquema siglas de `xu_dl` activas y sin esquema
     * @return array<string, string> sigla => sigla (con sufijo «f» en sf)
     */
    public static function siglasActaSinEsquema(array $siglasSinEsquema, bool $esSf): array
    {
        $out = [];
        foreach ($siglasSinEsquema as $dl) {
            if (!self::esSiglaDeDelegacion($dl)) {
                continue;
            }
            $sigla = $dl . ($esSf ? 'f' : '');
            $out[$sigla] = $sigla;
        }
        ksort($out, SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }

    /**
     * Regiones sin esquema Orbix (crB, crAfom, cg, …): se ofrecen como certificado,
     * sin el prefijo «cr».
     *
     * @param list<string> $siglasSinEsquema siglas de `xu_dl` activas y sin esquema
     * @return array<string, string> sigla => sigla
     */
    public static function siglasCertificadoSinEsquema(array $siglasSinEsquema): array
    {
        $out = [];
        foreach ($siglasSinEsquema as $dl) {
            if (self::esSiglaDeDelegacion($dl)) {
                continue;
            }
            $sigla = self::siglaCertificadoSinPrefijoCr($dl);
            if ($sigla !== '') {
                $out[$sigla] = $sigla;
            }
        }

        return $out;
    }

    /**
     * Siglas de certificado cuya delegación está inactiva en `xu_dl` (p. ej. Iers,
     * absorbida en crH): no se ofrecen aunque su esquema siga existiendo. Una sigla
     * que no está en la tabla, o que tiene alguna fila activa, no se considera inactiva.
     *
     * @param list<array{0: string, 1: bool}> $estados pares [dl, activa]
     * @return array<string, string> sigla => sigla
     */
    public static function siglasCertificadoInactivas(array $estados): array
    {
        $activas = [];
        $inactivas = [];
        foreach ($estados as [$dl, $activa]) {
            $sigla = self::siglaCertificadoSinPrefijoCr($dl);
            if ($sigla === '') {
                continue;
            }
            if ($activa) {
                $activas[$sigla] = $sigla;
            } else {
                $inactivas[$sigla] = $sigla;
            }
        }

        return array_diff_key($inactivas, $activas);
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
     * Esquema de región STGR (H-H, M-M, Galbel-crGalbel), no una DL (H-dlb, M-dlmO).
     */
    public static function esEsquemaRegionStgr(string $region, string $dl): bool
    {
        $region = trim($region);
        $dl = PersonaPublicacion::normalizarDl(trim($dl));
        if ($region === '' || $dl === '') {
            return false;
        }
        if (strcasecmp($region, $dl) === 0) {
            return true;
        }

        return strlen($dl) > 2 && strncasecmp($dl, 'cr', 2) === 0;
    }

    /**
     * Temporal: el desplegable de certificado incluye la sigla de la región del
     * esquema actual (p. ej. H-dlbv → H), aunque esa región esté en aquinate.
     *
     * @param array<string, string> $opciones
     * @return array<string, string>
     */
    public static function conSiglaRegionDelEsquema(array $opciones, string $regionEsquema): array
    {
        $sigla = self::siglaCertificadoSinPrefijoCr(trim($regionEsquema));
        if ($sigla === '') {
            return $opciones;
        }
        unset($opciones[$sigla]);

        return [$sigla => $sigla] + $opciones;
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
     * Siglas de región de esquemas Orbix (H, M, Galbel, …), no las DL de H/M (dlb, dlp, …).
     *
     * @return array<string, string> sigla => sigla
     */
    private function siglasEsquemaOrbixExceptoPropia(): array
    {
        $miDelef = ConfigGlobal::mi_delef();
        $miDele = ConfigGlobal::mi_dele();
        $dbProp = new DBPropiedades();
        $aEsquemas = $dbProp->array_posibles_esquemas(false, true);
        if (!is_array($aEsquemas)) {
            return [];
        }

        $out = [];
        foreach ($aEsquemas as $esquema) {
            $esquema = (string) $esquema;
            $region = strtok($esquema, '-');
            $dlParte = strtok('-');
            if (!is_string($region) || $region === '' || !is_string($dlParte) || $dlParte === '') {
                continue;
            }
            $dl = PersonaPublicacion::normalizarDl($dlParte);
            if ($dl === '' || !self::esEsquemaRegionStgr($region, $dl)) {
                continue;
            }
            if ($this->esSiglaPropia($dl, $miDelef, $miDele)) {
                continue;
            }
            $sigla = self::siglaCertificadoSinPrefijoCr($dl);
            if ($sigla === '' || strncasecmp($sigla, 'dl', 2) === 0) {
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
     * Siglas activas de la tabla delegaciones sin esquema Orbix (ni la propia ni «Otra»).
     *
     * @param iterable<\src\ubis\domain\entity\Delegacion> $delegaciones
     * @return list<string>
     */
    private function delegacionesSinEsquemaOrbix(iterable $delegaciones): array
    {
        $miDele = ConfigGlobal::mi_dele();
        $sfsv = ConfigGlobal::mi_sfsv();
        $suffix = $sfsv === 2 ? 'f' : 'v';
        $database = $sfsv === 2 ? 'sf' : 'sv';
        $oConfigDB = null;

        $out = [];
        foreach ($delegaciones as $delegacion) {
            if (!$delegacion->isActive()) {
                continue;
            }
            $dlCode = (string) ($delegacion->getDlVo()->value() ?? '');
            $region = (string) ($delegacion->getRegionVo()->value() ?? '');
            if ($dlCode === '' || $region === '' || $dlCode === $miDele) {
                continue;
            }
            if (strcasecmp($dlCode, 'Otra') === 0) {
                continue;
            }
            $oConfigDB ??= new ConfigDB($database);
            if ($this->delegacionTieneEsquemaOrbix($region, $dlCode, $oConfigDB, $suffix)) {
                continue;
            }
            $out[] = $dlCode;
        }

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
