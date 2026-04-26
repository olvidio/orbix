<?php

namespace src\asistentes\application;

use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use web\Hash;

/**
 * Selector de centro para actividades pendientes (`lista_ultim_que_ctr.php`).
 */
final class ListaUltimQueCtrData
{
    /**
     * @param array<string, mixed> $input
     * @return array{opciones_centros: array<string|int, string>, hash_form_html: string, form_action: string}
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

        $oHash = new Hash();
        $oHash->setCamposForm('id_ubi');
        $oHash->setArraycamposHidden([
            'que' => $Qque,
            'curso' => $Qcurso,
        ]);

        return [
            'opciones_centros' => $aOpciones,
            'hash_form_html' => $oHash->getCamposHtml(),
            'form_action' => 'frontend/asistentes/controller/lista_ultima_activ.php',
        ];
    }
}
