<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

/**
 * Datos para la pantalla de textos de comunicacion
 * (`frontend/encargossacd/controller/listas_com_txt.php`).
 *
 * Devuelve las opciones de idiomas configurados y el texto inicial
 * correspondiente a la clave/idioma por defecto (`com_sacd` / `es`).
 */
final class ListasComTxtData
{

    public function __construct(
        private EncargoTextoRepositoryInterface $encargoTextoRepository,
        private LocalRepositoryInterface $localRepository
    ) {
    }

    /**
     * @return array{ a_locales: array<string, string>, texto_inicial: string }
     */
    public function execute(): array
    {
        $a_locales = self::normalizeStringKeys($this->localRepository->getArrayLocales());

        $cEncargoTextos = $this->encargoTextoRepository->getEncargoTextos([
            'clave' => 'com_sacd',
            'idioma' => 'es',
        ]);
        $texto_inicial = '';
        if ($cEncargoTextos !== []) {
            $texto_inicial = (string) $cEncargoTextos[0]->getTexto();
        }

        return [
            'a_locales' => $a_locales,
            'texto_inicial' => $texto_inicial,
        ];
    }

    /**
     * @param array<int|string, string> $opciones
     * @return array<string, string>
     */
    private static function normalizeStringKeys(array $opciones): array
    {
        $out = [];
        foreach ($opciones as $k => $v) {
            $out[(string) $k] = $v;
        }

        return $out;
    }
}
