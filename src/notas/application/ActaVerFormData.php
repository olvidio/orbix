<?php

declare(strict_types=1);

namespace src\notas\application;


use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\urlsafe_b64decode;

/**
 * Estado del formulario `acta_ver` (sin HashFront ni vistas).
 */
final class ActaVerFormData
{

    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly ActaTribunalRepositoryInterface $actaTribunalRepository,
        private readonly ActaTribunalDlRepositoryInterface $actaTribunalDlRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $in POST + `scope_notas`, `scope_permiso`, opcional `acta_notas_a_actas_json`,
     *        opcional `id_activ_scope` e `id_asignatura_scope` (variables del include `acta_notas`).
     * @return array<string, mixed>
     */
    public function execute(array $in): array
    {
        $notas = input_string($in, 'scope_notas');
        $permiso = input_int($in, 'scope_permiso', 3);
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $permiso = 0;
        }

        $a_sel = isset($in['sel']) && is_array($in['sel']) ? $in['sel'] : [];
        $Qmod = input_string($in, 'mod');
        $Qsa_actas = input_string($in, 'sa_actas');
        $Qa_actas = $Qsa_actas !== '' ? json_decode(urlsafe_b64decode($Qsa_actas)) : null;
        $Qacta = input_string($in, 'acta');
        $Qnotas = input_string($in, 'notas');

        $any = date('y');
        $mi_dele = ConfigGlobal::mi_delef();
        $dl = $mi_dele;

        $ActaRepository = $this->actaRepository;
        $ult_lib = '';
        $ult_pag = '';
        $ult_lin = '';
        $ult_acta = $ActaRepository->getUltimaActa($any, $dl);
        $acta_new = '';
        $pdf = null;

        $acta_actual = '';
        $a_actas = [];

        if ($notas === '' && $Qnotas === '') {
            if ($a_sel !== [] && is_string($a_sel[0])) {
                $token = strtok($a_sel[0], '#');
                $acta_actual = urldecode(is_string($token) ? $token : '');
            } else {
                $acta_actual = urldecode($Qacta);
            }
            $a_actas = [$acta_actual];
        } else {
            $actasJson = input_string($in, 'acta_notas_a_actas_json');
            if ($actasJson !== '') {
                $decoded = json_decode($actasJson, true);
                $a_actas = is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
                $acta_actual = $a_actas[0] ?? '';
            } elseif (!empty($Qa_actas)) {
                $a_actas = is_array($Qa_actas) ? array_values(array_filter($Qa_actas, 'is_string')) : [];
                $acta_actual = $Qacta;
                $notas = $Qnotas;
            } else {
                $a_actas = [];
                $acta_actual = '';
            }
        }

        $f_acta = '';
        $libro = '';
        $pagina = '';
        $linea = '';
        $lugar = '';
        $observ = '';
        $id_activ = 0;
        $id_asignatura_actual = null;
        $examinador_pral = '';

        if ($notas !== 'nuevo' && $Qmod !== 'nueva' && $acta_actual !== '') {
            if ($Qacta !== '' && $notas !== '') {
                $idAsig = input_int($in, 'id_asignatura_actual', input_int($in, 'id_asignatura'));
                $id_asignatura_actual = $idAsig !== 0 ? $idAsig : null;
                $id_activ = input_int($in, 'id_activ');
                $f_acta = input_string($in, 'f_acta');
                $libro = input_string($in, 'libro');
                $pagina = (string) input_int($in, 'pagina');
                $linea = (string) input_int($in, 'linea');
                $lugar = input_string($in, 'lugar');
                $observ = input_string($in, 'observ');
                $permiso = input_int($in, 'permiso', $permiso);
            } else {
                $oActa = $ActaRepository->findById($acta_actual);
                if ($oActa instanceof Acta) {
                    $id_asignatura = $oActa->getId_asignatura();
                    $id_activ = $oActa->getId_activ();
                    $f_acta = $oActa->getF_acta()?->getFromLocal() ?? '';
                    $libro = $oActa->getLibro();
                    $pagina = $oActa->getPagina();
                    $linea = $oActa->getLinea();
                    $lugar = $oActa->getLugar();
                    $observ = $oActa->getObserv();
                    $id_asignatura_actual = $id_asignatura;
                    $pdf = $oActa->getpdf();
                }
            }
        } else {
            $num_acta = $ult_acta + 1;
            $ult_acta = "$dl {$ult_acta}/{$any}";
            $acta_new = "$dl {$num_acta}/{$any}";

            if ($notas === 'nuevo') {
                $Qid_activ = input_int($in, 'id_activ');
                $id_scope = input_int($in, 'id_activ_scope');
                $id_activ = $id_scope !== 0 ? $id_scope : $Qid_activ;
                $Qid_asignatura = input_int($in, 'id_asignatura');
                $id_asig_scope = input_int($in, 'id_asignatura_scope');
                $id_asignatura_actual = $id_asig_scope !== 0 ? $id_asig_scope : ($Qid_asignatura !== 0 ? $Qid_asignatura : null);

                if ($id_activ > 0 && $id_asignatura_actual !== null) {
                    $oActividadAsignatura = $this->actividadAsignaturaDlRepository->findById($id_activ, $id_asignatura_actual);
                    if ($oActividadAsignatura !== null) {
                        $id_profesor = $oActividadAsignatura->getId_profesor();
                        if ($id_profesor !== null) {
                            $oPersonaDl = $this->personaDlRepository->findById($id_profesor);
                            $examinador_pral = $oPersonaDl === null
                                ? _('No encuentro el profesor.')
                                : $oPersonaDl->getTituloNombre();
                        }
                    }
                }
            } else {
                if ($a_sel !== [] && $notas !== '' && is_string($a_sel[0])) {
                    $parts = explode('#', $a_sel[0], 2);
                    $id_activ = is_numeric($parts[0]) ? (int) $parts[0] : 0;
                    $id_asignatura = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 0;
                    $cActas = $ActaRepository->getActas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
                    $oActa = $cActas[0] ?? null;
                    if ($oActa instanceof Acta) {
                        $id_asignatura = $oActa->getId_asignatura();
                        $id_activ = $oActa->getId_activ();
                        $f_acta = $oActa->getF_acta()?->getFromLocal() ?? '';
                        $libro = $oActa->getLibro();
                        $pagina = $oActa->getPagina();
                        $linea = $oActa->getLinea();
                        $lugar = $oActa->getLugar();
                        $observ = $oActa->getObserv();
                        $id_asignatura_actual = $id_asignatura;
                    }
                } else {
                    $id_asignatura_actual = null;
                }
            }
        }
        if (!empty($ult_acta)) {
            $ult_acta = sprintf(_('(última= %s)'), $ult_acta);
        }

        $cTribunal = [];
        if ($acta_actual !== '') {
            if (ConfigGlobal::mi_ambito() === 'rstgr') {
                $repoActaTribunal = $this->actaTribunalRepository;
            } else {
                $repoActaTribunal = $this->actaTribunalDlRepository;
            }
            $cTribunal = $repoActaTribunal->getActasTribunales(['acta' => $acta_actual, '_ordre' => 'orden']);
        }

        $nombre_asignatura = '';
        if ($id_asignatura_actual) {
            $AsignaturaRepository = $this->asignaturaRepository;
            $cAsignatura = $AsignaturaRepository->getAsignaturas(['id_asignatura' => $id_asignatura_actual]);
            if ($cAsignatura !== []) {
                $oAsignatura = $cAsignatura[0];
                $nombre_asignatura = (string)$oAsignatura->getNombre_asignatura();
            }
        }

        $examinadores = [];
        if ($cTribunal !== []) {
            foreach ($cTribunal as $oActaTribunal) {
                $examinadores[] = $oActaTribunal->getExaminador();
            }
        } else {
            $examinadores[] = $examinador_pral;
        }

        $warn_no_id_activ = ($Qmod === 'nueva' || $notas === 'nuevo') && empty($id_activ);

        return [
            'notas' => $notas,
            'permiso' => $permiso,
            'mod' => $Qmod,
            'acta_actual' => $acta_actual,
            'acta_new' => $acta_new,
            'ult_acta' => $ult_acta,
            'f_acta' => $f_acta,
            'libro' => $libro,
            'ult_lib' => $ult_lib,
            'pagina' => $pagina,
            'ult_pag' => $ult_pag,
            'linea' => $linea,
            'ult_lin' => $ult_lin,
            'lugar' => $lugar,
            'observ' => $observ,
            'id_activ' => $id_activ,
            'id_asignatura_actual' => $id_asignatura_actual,
            'nombre_asignatura' => $nombre_asignatura,
            'examinadores' => $examinadores,
            'a_actas' => $a_actas,
            'has_pdf' => $pdf !== null,
            'warn_no_id_activ' => $warn_no_id_activ,
        ];
    }
}
