<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\notas\domain\contracts\MapaPrefijoActaEsquemaRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Prefijos de acta «propios» de la DL de sesión (mapa BD + dl actual).
 */
final class ActaPrefijosDeEsquema
{
    public function __construct(
        private readonly MapaPrefijoActaEsquemaRepositoryInterface $mapaPrefijoActaEsquemaRepository,
    ) {
    }

    /**
     * Esquema base de la sesión (`H-dlal`), sin sufijo v/f.
     */
    public static function esquemaBaseSesion(): string
    {
        $region = ConfigGlobal::mi_region();
        $dl = ConfigGlobal::mi_dele();
        if ($region === '' || $dl === '') {
            return '';
        }

        return $region . '-' . $dl;
    }

    /**
     * Prefijos con los que buscar/crear actas en esta DL (incl. absorbidas).
     *
     * @return list<string>
     */
    public function prefijosSesion(): array
    {
        $base = self::esquemaBaseSesion();
        $prefs = $base !== ''
            ? $this->mapaPrefijoActaEsquemaRepository->prefijosPorEsquemaBase($base)
            : [];

        $mine = ConfigGlobal::mi_delef();
        if ($mine !== '' && !in_array($mine, $prefs, true)) {
            array_unshift($prefs, $mine);
        }

        $mineSinF = ConfigGlobal::mi_dele();
        if ($mineSinF !== '' && !in_array($mineSinF, $prefs, true)) {
            $prefs[] = $mineSinF;
        }

        return array_values(array_unique($prefs));
    }

    /**
     * Patrón regex OR para buscar un nº de acta con todos los prefijos propios.
     *
     * @param string $numeroParte Parte numérica (p. ej. `12` o `12/24`)
     * @param bool $soloNumeroSinAnio Si true, completa con `/` + año actual a 2 dígitos
     */
    public function patronBusquedaPorNumero(string $numeroParte, bool $soloNumeroSinAnio): string
    {
        $prefs = $this->prefijosSesion();
        if ($prefs === []) {
            return '';
        }

        $partes = [];
        foreach ($prefs as $dl) {
            $partes[] = $soloNumeroSinAnio
                ? $dl . ' ' . $numeroParte . '/' . date('y')
                : $dl . ' ' . $numeroParte;
        }

        return implode('|', $partes);
    }
}
