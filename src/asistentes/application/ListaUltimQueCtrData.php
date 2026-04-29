<?php

namespace src\asistentes\application;

use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Selector de centro para actividades pendientes (`lista_ultim_que_ctr.php`).
 * Hash del formulario y URL del action en {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.
 */
final class ListaUltimQueCtrData
{
    /**
     * @param array<string, mixed> $input
     * @return array{opciones_centros: array<string|int, string>, hash_main: array{campos_form: string, campos_hidden: array<string, mixed>}, paths: array{form_action: string}}
     */
    public static function build(array $input): array
    {
        $Qque = (string)($input['que'] ?? '');
        $Qcurso = (string)($input['curso'] ?? '');

        $PersonaSRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        $aIdCentros = $PersonaSRepository->getArrayIdCentros();

        $aOpciones = [];
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        foreach ($aIdCentros as $id_ubi) {
            $oCentroDl = $CentroDlRepository->findById($id_ubi);
            $nombre_ubi = $oCentroDl->getNombre_ubi();
            $aOpciones[$id_ubi] = $nombre_ubi;
        }
        natcasesort($aOpciones);
        $aOpciones['999'] = _("todos");

        return [
            'opciones_centros' => $aOpciones,
            'hash_main' => [
                'campos_form' => 'id_ubi',
                'campos_hidden' => [
                    'que' => $Qque,
                    'curso' => $Qcurso,
                ],
            ],
            'paths' => [
                'form_action' => 'frontend/asistentes/controller/lista_ultima_activ.php',
            ],
        ];
    }
}
