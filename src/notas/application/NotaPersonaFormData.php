<?php

namespace src\notas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\entity\Persona;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Prepara los datos que necesita `form_notas_de_una_persona.phtml` para pintar el form
 * de alta / edicion de una `PersonaNota`.
 *
 * Dos modos:
 *   - Edicion (`id_asignatura_real` presente): carga la nota existente
 *     y precarga el desplegable de preceptores.
 *   - Alta: calcula las asignaturas pendientes (las opcionales se
 *     poblaran via AJAX en cliente).
 */
final class NotaPersonaFormData
{

    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly ProfesorStgrRepositoryInterface $rofesorStgrRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $id_pau = input_int($input, 'id_pau');
        $id_asignatura_real = $this->resolveAsignaturaReal($input);

        if ($id_asignatura_real !== '') {
            return $this->prepareEditar($id_pau, (int) $id_asignatura_real);
        }

        return $this->prepareNuevo($id_pau);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function resolveAsignaturaReal(array $input): string
    {
        $sel = (array)($input['sel'] ?? []);
        $pau = input_string($input, 'pau');
        if ($sel !== [] && $pau === 'p') {
            $sel0 = $sel[0];
            if (is_string($sel0)) {
                strtok($sel0, '#');
                $part = strtok('#');
                return is_string($part) ? $part : '';
            }
        }
        $mod = input_string($input, 'mod');
        if ($mod !== '' && $mod !== 'nuevo') {
            return input_string($input, 'id_asignatura_real');
        }
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareEditar(int $id_pau, int $id_asignatura_real): array
    {
        $PersonaNotaRepository = $this->personaNotaRepository;
        $cPersonaNotas = $PersonaNotaRepository->getPersonaNotas([
            '_ordre' => 'tipo_acta DESC',
            'id_nom' => $id_pau,
            'id_asignatura' => $id_asignatura_real,
        ]);
        if ($cPersonaNotas === []) {
            throw new \RuntimeException(_("No se encuentra la nota a editar"));
        }
        $oPersonaNota = $cPersonaNotas[0];

        $AsignaturaRepository = $this->asignaturaRepository;
        $oAsignatura = $AsignaturaRepository->findById($id_asignatura_real);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura_real));
        }
        $id_nivel = $oPersonaNota->getId_asignatura() > 3000
            ? $oPersonaNota->getIdNivelVo()->value()
            : $oAsignatura->getId_nivel();

        $oF_acta = $oPersonaNota->getF_acta();
        $id_activ = $oPersonaNota->getId_activ();

        return [
            'mod' => 'editar',
            'id_asignatura_real' => (string) $id_asignatura_real,
            'id_nivel' => $id_nivel,
            'nombre_corto' => $oAsignatura->getNombre_corto(),
            'id_situacion' => $oPersonaNota->getId_situacion(),
            'nota_num' => $oPersonaNota->getNota_num(),
            'nota_max' => $oPersonaNota->getNota_max(),
            'acta' => $oPersonaNota->getActa(),
            'tipo_acta' => $oPersonaNota->getTipo_acta(),
            'f_acta' => $oF_acta instanceof DateTimeLocal ? $oF_acta->getFromLocal() : '',
            'f_acta_iso' => $oF_acta instanceof DateTimeLocal ? $oF_acta->format('Y-m-d') : '',
            'preceptor' => $oPersonaNota->isPreceptor(),
            'id_preceptor' => $oPersonaNota->getId_preceptor(),
            'detalle' => $oPersonaNota->getDetalle(),
            'epoca' => $oPersonaNota->getEpoca(),
            'id_activ' => $id_activ,
            'nom_activ' => $this->resolveNomActiv($id_activ),
            'profesores' => $this->getProfesoresDl(),
            'asignaturas_faltan' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareNuevo(int $id_pau): array
    {
        $AsignaturaRepository = $this->asignaturaRepository;

        $cAsignaturas = $AsignaturaRepository->getAsignaturas(
            ['active' => 't', 'id_nivel' => 3000, '_ordre' => 'id_nivel'],
            ['id_nivel' => '<']
        );

        $PersonaNotaRepository = $this->personaNotaRepository;
        $aSuperadas = NotaSituacion::getArraySuperadas();
        $cSuperadas = $PersonaNotaRepository->getPersonaNotas(
            [
                'id_situacion' => implode(',', $aSuperadas),
                'id_nom' => $id_pau,
                'id_nivel' => 3000,
                '_ordre' => 'id_nivel',
            ],
            ['id_situacion' => 'IN', 'id_nivel' => '<']
        );

        $aSuperadasMap = [];
        foreach ($cSuperadas as $oPN) {
            $aSuperadasMap[$oPN->getId_nivel()] = $oPN->getId_asignatura();
        }

        $aFaltan = [];
        foreach ($cAsignaturas as $oAsig) {
            $id_nivel = $oAsig->getId_nivel();
            if (array_key_exists($id_nivel, $aSuperadasMap)) {
                continue;
            }
            $aFaltan[$id_nivel] = $oAsig->getNombre_corto();
        }
        $aFaltan[9997] = '---------';
        $aFaltan[9998] = _("fin cuadrienio");
        $aFaltan[9999] = _("fin bienio");

        return [
            'mod' => 'nuevo',
            'id_asignatura_real' => '',
            'id_nivel' => '',
            'nombre_corto' => '',
            'id_situacion' => '',
            'nota_num' => '',
            'nota_max' => '',
            'acta' => '',
            'tipo_acta' => '',
            'f_acta' => '',
            'f_acta_iso' => '',
            'preceptor' => '',
            'id_preceptor' => '',
            'detalle' => '',
            'epoca' => '',
            'id_activ' => '',
            'nom_activ' => '',
            'profesores' => [],
            'asignaturas_faltan' => $aFaltan,
        ];
    }

    private function resolveNomActiv(?int $id_activ): string
    {
        if ($id_activ === null || $id_activ === 0) {
            return '';
        }
        $repo = $this->actividadAllRepository;
        $oActividad = $repo->findById($id_activ);
        return $oActividad?->getNom_activ() ?? '';
    }

    /**
     * @return array<int, string>
     */
    private function getProfesoresDl(): array
    {
        $repo = $this->rofesorStgrRepository;
        $cProfesores = $repo->getProfesoresStgr();
        $aProfesores = [];
        foreach ($cProfesores as $oProfesor) {
            $oPersona = Persona::findPersonaEnGlobal($oProfesor->getId_nom());
            if ($oPersona === null) {
                continue;
            }
            $aProfesores[$oProfesor->getId_nom()] = $oPersona->getPrefApellidosNombre();
        }
        uasort($aProfesores, 'src\shared\domain\helpers\strsinacentocmp');
        return $aProfesores;
    }

    /**
     * Constantes y listas derivadas de value objects para el formulario (sin `use src`
     * en el controlador frontend). Incluye etiquetas traducidas y `lista_situacion_no_acta`.
     *
     * @return array{
     *   aOpcionesSituacion: array<int, string>,
     *   lista_situacion_no_acta: string,
     *   vo: array{
     *     NotaSituacion: array<string, int>,
     *     TipoActa: array<string, int>,
     *     NotaEpoca: array<string, int>,
     *   }
     * }
     */
    public static function formNotasVoPack(): array
    {
        $cNotasNoSup = NotaSituacion::getArrayNoSuperadas();
        $lista_situacion_no_acta = '"11"';
        foreach ($cNotasNoSup as $id_sit) {
            $lista_situacion_no_acta .= ',"' . $id_sit . '"';
        }

        return [
            'aOpcionesSituacion' => NotaSituacion::getArraySituacionTxt(),
            'lista_situacion_no_acta' => $lista_situacion_no_acta,
            'vo' => [
                'NotaSituacion' => [
                    'DESCONOCIDO' => NotaSituacion::DESCONOCIDO,
                    'SUPERADA' => NotaSituacion::SUPERADA,
                    'CURSADA' => NotaSituacion::CURSADA,
                    'MAGNA' => NotaSituacion::MAGNA,
                    'SUMMA' => NotaSituacion::SUMMA,
                    'CONVALIDADA' => NotaSituacion::CONVALIDADA,
                    'PREVISTA_CA' => NotaSituacion::PREVISTA_CA,
                    'PREVISTA_INV' => NotaSituacion::PREVISTA_INV,
                    'NO_HECHA_CA' => NotaSituacion::NO_HECHA_CA,
                    'NO_HECHA_INV' => NotaSituacion::NO_HECHA_INV,
                    'NUMERICA' => NotaSituacion::NUMERICA,
                    'EXENTO' => NotaSituacion::EXENTO,
                    'EXAMINADO' => NotaSituacion::EXAMINADO,
                    'FALTA_CERTIFICADO' => NotaSituacion::FALTA_CERTIFICADO,
                ],
                'TipoActa' => [
                    'FORMATO_ACTA' => TipoActa::FORMATO_ACTA,
                    'FORMATO_CERTIFICADO' => TipoActa::FORMATO_CERTIFICADO,
                ],
                'NotaEpoca' => [
                    'EPOCA_CA' => NotaEpoca::EPOCA_CA,
                    'EPOCA_INVIERNO' => NotaEpoca::EPOCA_INVIERNO,
                    'EPOCA_OTRO' => NotaEpoca::EPOCA_OTRO,
                ],
            ],
        ];
    }

    /**
     * Devuelve los niveles de las opcionales genericas (id_sector = 1)
     * para la condicion JS `fnjs_cmb_opcional`.
     *
     * @return array{condicion_js:string, op_genericas_json:string}
     */
    public function opcionalesGenericasHelpers(): array
    {
        $repo = $this->asignaturaRepository;
        $cGenericas = $repo->getAsignaturas(
            ['active' => 't', 'id_sector' => 1, 'id_nivel' => 3000, '_ordre' => 'nombre_corto'],
            ['id_nivel' => '<']
        );
        $condicion = '';
        foreach ($cGenericas as $oOpcional) {
            $condicion .= 'id==' . $oOpcional->getId_nivel() . ' || ';
        }
        return [
            'condicion_js' => substr($condicion, 0, -4),
            'op_genericas_json' => $repo->getListaOpGenericas('json'),
        ];
    }
}
