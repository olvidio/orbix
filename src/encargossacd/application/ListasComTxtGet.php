<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;

/**
 * Lectura del texto de comunicacion para un par (clave, idioma).
 *
 * Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar
 * el dispatcher multiproposito (criterio `refactor.md`).
 */
final class ListasComTxtGet
{

    public function __construct(
        private EncargoTextoRepositoryInterface $encargoTextoRepository
    ) {
    }

    /**
     * @return array{texto: string}
     */
    public function execute(string $clave, string $idioma): array
    {
        $cEncargoTextos = $this->encargoTextoRepository->getEncargoTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);

        $texto = '';
        if ($cEncargoTextos !== []) {
            $texto = (string)$cEncargoTextos[0]->getTexto();
        }

        return ['texto' => $texto];
    }
}
