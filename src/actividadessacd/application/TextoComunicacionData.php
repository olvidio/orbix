<?php

namespace src\actividadessacd\application;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\shared\security\HashB;

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
     * @return array{texto: string, ctx_guardar?: string}
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
        $texto = count($cTextos) === 0 ? '' : (string)$cTextos[0]->getTexto();

        return [
            'texto' => $texto,
            'ctx_guardar' => HashB::sign('texto_comunicacion_guardar', [
                'clave' => $clave,
                'idioma' => $idioma,
            ]),
        ];
    }

}
