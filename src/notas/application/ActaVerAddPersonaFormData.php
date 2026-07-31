<?php

declare(strict_types=1);

namespace src\notas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\shared\config\ConfigGlobal;

/**
 * Datos para el formulario de añadir alumno+nota en `acta_ver` (caso B).
 *
 * Personas: DL local + publicadas para mi DL, sin Repaso y sin nota previa
 * en la asignatura del acta.
 *
 * @return array{
 *     error?: string,
 *     puede_anadir?: bool,
 *     acta?: string,
 *     f_acta?: string,
 *     id_asignatura?: int,
 *     id_nivel?: int,
 *     id_activ?: int,
 *     nota_max_default?: int,
 *     opciones_personas?: array<int, string>
 * }
 */
final class ActaVerAddPersonaFormData
{
    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly PersonaPubRepositoryInterface $personaPubRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $acta = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acta');
        if ($acta === '') {
            return ['error' => _('Falta el acta'), 'puede_anadir' => false];
        }

        $oActa = $this->actaRepository->findById($acta);
        if (!$oActa instanceof Acta) {
            return ['error' => _('No se encuentra el acta'), 'puede_anadir' => false];
        }

        $id_asignatura = (int) ($oActa->getId_asignatura() ?? 0);
        $id_activ = (int) ($oActa->getId_activ() ?? 0);
        $f_acta = $oActa->getF_acta()?->getFromLocal() ?? '';

        if ($id_asignatura < 1) {
            return ['error' => _('El acta no tiene asignatura'), 'puede_anadir' => false];
        }

        $id_nivel = 0;
        $cAsignatura = $this->asignaturaRepository->getAsignaturas(['id_asignatura' => $id_asignatura]);
        if ($cAsignatura !== []) {
            $id_nivel = (int) ($cAsignatura[0]->getId_nivel() ?? 0);
        }

        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $nota_max_default = (int) $oConfig->getNotaMax();

        return [
            'puede_anadir' => true,
            'acta' => $acta,
            'f_acta' => $f_acta,
            'id_asignatura' => $id_asignatura,
            'id_nivel' => $id_nivel,
            'id_activ' => $id_activ,
            'nota_max_default' => $nota_max_default,
            'opciones_personas' => $this->opcionesPersonasAlumnos($id_asignatura),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function opcionesPersonasAlumnos(int $id_asignatura): array
    {
        $excluidos = $this->idsConNotaEnAsignatura($id_asignatura);

        $aWhere = [
            'situacion' => 'A',
            'nivel_stgr' => NivelStgrId::R,
            '_ordre' => 'apellido1,apellido2,nom',
        ];
        $aOperador = ['nivel_stgr' => '!='];

        $opciones = [];

        foreach ($this->personaDlRepository->getPersonas($aWhere, $aOperador) as $oPersona) {
            $id = (int) $oPersona->getId_nom();
            if ($this->excluirAlumno($id, $oPersona->getNivel_stgr(), $excluidos)) {
                continue;
            }
            $opciones[$id] = (string) $oPersona->getPrefApellidosNombre();
        }

        try {
            $miDl = ConfigGlobal::mi_dele();
            foreach ($this->personaPubRepository->getPersonas($aWhere, $aOperador) as $oPersona) {
                $id = (int) $oPersona->getId_nom();
                if (isset($opciones[$id])) {
                    continue;
                }
                if ($this->excluirAlumno($id, $oPersona->getNivel_stgr(), $excluidos)) {
                    continue;
                }
                // Solo publicados expresamente para mi DL (no p_de_paso con "*").
                if ($miDl === '' || !$this->publicadoVigenteParaDl($oPersona->getPublicado_para(), $miDl)) {
                    continue;
                }
                $dl = $oPersona->getDl() ?? '';
                $etiqueta = (string) $oPersona->getPrefApellidosNombre();
                if ($dl !== '') {
                    $etiqueta .= ' (' . $dl . ')';
                }
                $opciones[$id] = $etiqueta;
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                _('No se han podido cargar las personas publicadas') . ': ' . $e->getMessage(),
                0,
                $e
            );
        }

        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        return $opciones;
    }

    private function publicadoVigenteParaDl(mixed $publicadoPara, string $miDl): bool
    {
        $vigente = PersonaPublicacion::mapaVigente(PersonaPublicacion::mapaFromJsonb($publicadoPara));

        return array_key_exists($miDl, $vigente);
    }

    /**
     * @param array<int, true> $excluidosPorNota
     */
    private function excluirAlumno(int $id_nom, mixed $nivel_stgr, array $excluidosPorNota): bool
    {
        if ($id_nom < 1) {
            return true;
        }
        if (isset($excluidosPorNota[$id_nom])) {
            return true;
        }
        if ($nivel_stgr === null || $nivel_stgr === '') {
            return false;
        }

        return (int) $nivel_stgr === NivelStgrId::R;
    }

    /**
     * @return array<int, true>
     */
    private function idsConNotaEnAsignatura(int $id_asignatura): array
    {
        if ($id_asignatura < 1) {
            return [];
        }

        $ids = [];
        foreach ($this->personaNotaRepository->getPersonaNotas(['id_asignatura' => $id_asignatura]) as $oNota) {
            $id = (int) $oNota->getId_nom();
            if ($id > 0) {
                $ids[$id] = true;
            }
        }

        return $ids;
    }
}
