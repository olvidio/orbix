<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Guarda las notas de cada matricula (borrador del acta de notas). Se invoca
 * desde la pantalla `acta_notas` cuando el usuario pulsa "Grabar".
 *
 * Sustituye a la rama `que=1` del legacy
 * `apps/actividadestudios/controller/acta_notas_update.php`.
 */
final class ActaNotasMatriculaGuardar
{
    public function __construct(
        private MatriculaRepositoryInterface $matriculaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $nota_corte = $oConfig->getNotaCorte();

        $Qform_preceptor = (array) ($input['form_preceptor'] ?? []);
        $Qid_nom = (array) ($input['id_nom'] ?? []);
        $Qnota_num = (array) ($input['nota_num'] ?? []);
        $Qnota_max = (array) ($input['nota_max'] ?? []);
        $Qacta = (array) ($input['acta_nota'] ?? []);

        $num_alumnos = count($Qid_nom);

        for ($n = 0; $n < $num_alumnos; $n++) {
            $preceptor = (!empty($Qform_preceptor[$n]) && $Qform_preceptor[$n] === 'p');
            $idNomRaw = $Qid_nom[$n] ?? 0;
            $idNom = is_numeric($idNomRaw) ? (int) $idNomRaw : 0;
            if ($idNom <= 0) {
                continue;
            }

            $oMatricula = $this->matriculaRepository->findById($Qid_activ, $Qid_asignatura, $idNom);
            if ($oMatricula === null) {
                continue;
            }
            $oMatricula->setPreceptor($preceptor);
            $notaNumRaw = self::scalarString($Qnota_num[$n] ?? '');
            $notaMaxRaw = self::scalarFloat($Qnota_max[$n] ?? 0);
            $nn = str_replace(',', '.', $notaNumRaw);
            if ($notaNumRaw !== '' && $notaMaxRaw > 0 && (float) $notaNumRaw / $notaMaxRaw > 1) {
                return _('Hay una nota mayor que el máximo') . "\n";
            }
            $oMatricula->setNota_num($notaNumRaw !== '' ? (float) $nn : null);
            $oMatricula->setNota_max($notaMaxRaw > 0 ? (int) $notaMaxRaw : null);
            $oMatricula->setActa(self::scalarString($Qacta[$n] ?? ''));

            $actaInt = self::scalarInt($Qacta[$n] ?? 0);
            $notaNumFloat = self::scalarFloat($Qnota_num[$n] ?? 0);

            if ($preceptor === false) {
                if ($actaInt === 2) {
                    $oMatricula->setId_situacion(2);
                    if ($notaNumFloat > 1) {
                        $oMatricula->setId_situacion(12);
                    }
                } elseif ($notaNumFloat > 1) {
                    if ($notaMaxRaw > 0 && $notaNumFloat / $notaMaxRaw < $nota_corte) {
                        $oMatricula->setId_situacion(12);
                    } else {
                        $oMatricula->setId_situacion(10);
                    }
                }
            } else {
                if ($actaInt === NotaSituacion::CURSADA) {
                    return _('no se puede definir cursada con preceptor') . "\n";
                }
                if ($notaNumRaw === '') {
                    $oMatricula->setId_situacion(0);
                } else {
                    $oMatricula->setId_situacion(10);
                }
            }
            if ($this->matriculaRepository->Guardar($oMatricula) === false) {
                return _('hay un error, no se ha guardado') . "\n" . $this->matriculaRepository->getErrorTxt();
            }
        }
        return '';
    }

    private static function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    private static function scalarInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private static function scalarFloat(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
