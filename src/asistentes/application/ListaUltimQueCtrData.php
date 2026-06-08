<?php

namespace src\asistentes\application;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Selector de centro para actividades pendientes (`lista_ultim_que_ctr.php`).
 * Hash del formulario y URL del action en {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.
 */
final class ListaUltimQueCtrData
{
    public function __construct(
        private PersonaSRepositoryInterface $personaSRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{opciones_centros: array<string|int, string>, hash_main: array{campos_form: string, campos_hidden: array<string, mixed>}, paths: array{form_action: string}}
     */
    public function build(array $input): array
    {
        $Qque = input_string($input, 'que');
        $Qcurso = input_string($input, 'curso');

        $PersonaSRepository = $this->personaSRepository;
        $aIdCentros = $PersonaSRepository->getArrayIdCentros();

        $aOpciones = [];
        $CentroDlRepository = $this->centroDlRepository;
        foreach ($aIdCentros as $id_ubi) {
            $oCentroDl = $CentroDlRepository->findById((int) $id_ubi);
            if ($oCentroDl === null) {
                continue;
            }
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
