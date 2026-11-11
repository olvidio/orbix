<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\PersonaPublicacion;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\criteria\DelegacionField;

/**
 * Opciones de sigla DL para el formulario notas de persona (acta / certificado).
 */
final class ActaPersonaFormListas implements SiglaActaPermitida
{
    /**
     * Siglas que se ofrecen siempre como certificado aunque no salgan de `xu_dl`
     * activas (p. ej. «crs+», inactiva en la tabla). Van tal cual, con su prefijo.
     */
    public const SIGLAS_CERTIFICADO_FIJAS = ['crs+'];

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
    ) {
    }

    /**
     * - Acta: solo la sigla propia (dl o cr); lo de fuera llega como certificado.
     * - Certificado: regiones activas de `xu_dl` sin esquema Orbix, sin el prefijo «cr».
     *
     * @return array{
     *   mi_sigla: string,
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

        return [
            'mi_sigla' => $miSigla,
            'opciones_certificado_dl' => self::conSiglasFijas(
                self::siglasCertificadoSinEsquema($this->delegacionesSinEsquemaOrbix($delegaciones)),
            ),
            'siglas_acta_permitidas' => [$miSigla],
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
        ksort($out, SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }

    /**
     * @param array<string, string> $opciones
     * @return array<string, string>
     */
    public static function conSiglasFijas(array $opciones): array
    {
        foreach (self::SIGLAS_CERTIFICADO_FIJAS as $sigla) {
            $opciones[$sigla] = $sigla;
        }
        ksort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
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
     * Siglas activas de la tabla delegaciones sin esquema Orbix (ni la propia ni «Otra»).
     *
     * @param iterable<\src\ubis\domain\entity\Delegacion> $delegaciones
     * @return list<string>
     */
    private function delegacionesSinEsquemaOrbix(iterable $delegaciones): array
    {
        $miDele = ConfigGlobal::mi_dele();
        // Esquemas que existen de verdad en la base de datos (nombres de `comun`, sin sufijo v/f).
        $esquemas = (new DBPropiedades())->array_posibles_esquemas(true, true);
        $esquemas = is_array($esquemas) ? $esquemas : [];

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
            if (self::tieneEsquemaOrbix($region, $dlCode, $esquemas)) {
                continue;
            }
            $out[] = $dlCode;
        }

        return $out;
    }

    /**
     * El esquema de una delegación es «region-dl» (H-dlb); el de una región puede ser
     * «region-crRegion» (Galbel-crGalbel) o «region-region» (H-H, M-M).
     *
     * @param array<array-key, string> $esquemas esquemas existentes, sin sufijo v/f
     */
    public static function tieneEsquemaOrbix(string $region, string $dlCode, array $esquemas): bool
    {
        if (in_array($region . '-' . $dlCode, $esquemas, true)) {
            return true;
        }

        return !self::esSiglaDeDelegacion($dlCode)
            && in_array($region . '-' . $region, $esquemas, true);
    }
}
