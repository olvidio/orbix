<?php

namespace src\actividadessacd\application;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;

/**
 * Devuelve el texto de comunicacion asociado a `{clave, idioma}`.
 */
final class TextoComunicacionData
{
    public function __construct(
        private ActividadSacdTextoRepositoryInterface $actividadSacdTextoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{texto: string}
     */
    public function execute(array $input): array
    {
        $clave = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'clave');
        $idioma = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'idioma');
        if ($clave === '' || $idioma === '') {
            return ['texto' => ''];
        }

        $cTextos = $this->actividadSacdTextoRepository->getActividadSacdTextos([
            'clave' => $clave,
            'idioma' => $idioma,
        ]);
        if (count($cTextos) === 0) {
            return ['texto' => ''];
        }
        return ['texto' => (string)$cTextos[0]->getTexto()];
    }

}
