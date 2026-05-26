<?php

declare(strict_types=1);

namespace src\notas\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\urlsafe_b64decode;

/**
 * Estado del formulario `acta_ver` (sin HashFront ni vistas).
 */
final class ActaVerFormData
{
    /**
     * @param array<string, mixed> $in POST + `scope_notas`, `scope_permiso`, opcional `acta_notas_a_actas_json`,
     *        opcional `id_activ_scope` e `id_asignatura_scope` (variables del include `acta_notas`).
     * @return array<string, mixed>
     */
    public static function execute(array $in): array
    {
        $notas = (string)($in['scope_notas'] ?? '');
        $permiso = (int)($in['scope_permiso'] ?? 3);
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $permiso = 0;
        }

        $a_sel = isset($in['sel']) && is_array($in['sel']) ? $in['sel'] : [];
        $Qmod = (string)($in['mod'] ?? '');
        $Qsa_actas = (string)($in['sa_actas'] ?? '');
        $Qa_actas = $Qsa_actas !== '' ? json_decode(urlsafe_b64decode($Qsa_actas)) : null;
        $Qacta = (string)($in['acta'] ?? '');
        $Qnotas = (string)($in['notas'] ?? '');

        $any = date('y');
        $mi_dele = ConfigGlobal::mi_delef();
        $dl = $mi_dele;

        $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $ult_lib = '';
        $ult_pag = '';
        $ult_lin = '';
        $ult_acta = $ActaRepository->getUltimaActa($any, $dl);
        $acta_new = '';
        $pdf = null;

        $acta_actual = '';
        $a_actas = [];

        if ($notas === '' && $Qnotas === '') {
            if ($a_sel !== []) {
                $acta_actual = urldecode(strtok((string)$a_sel[0], '#'));
            } else {
                $acta_actual = urldecode($Qacta);
            }
            $a_actas = [$acta_actual];
        } else {
            if (!empty($in['acta_notas_a_actas_json'])) {
                $decoded = json_decode((string)$in['acta_notas_a_actas_json'], true);
                $a_actas = is_array($decoded) ? $decoded : [];
                $acta_actual = empty($a_actas[0]) ? '' : (string)$a_actas[0];
            } elseif (!empty($Qa_actas)) {
                $a_actas = is_array($Qa_actas) ? $Qa_actas : (array)$Qa_actas;
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
                $id_asignatura_actual = (int)($in['id_asignatura_actual'] ?? $in['id_asignatura'] ?? 0) ?: null;
                $id_activ = (int)($in['id_activ'] ?? 0);
                $f_acta = (string)($in['f_acta'] ?? '');
                $libro = (string)($in['libro'] ?? '');
                $pagina = (string)(int)($in['pagina'] ?? 0);
                $linea = (string)(int)($in['linea'] ?? 0);
                $lugar = (string)($in['lugar'] ?? '');
                $observ = (string)($in['observ'] ?? '');
                $permiso = (int)($in['permiso'] ?? $permiso);
            } else {
                $oActa = $ActaRepository->findById($acta_actual);
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
        } else {
            $num_acta = $ult_acta + 1;
            $ult_acta = "$dl {$ult_acta}/{$any}";
            $acta_new = "$dl {$num_acta}/{$any}";

            if ($notas === 'nuevo') {
                $Qid_activ = (int)($in['id_activ'] ?? 0);
                $id_scope = (int)($in['id_activ_scope'] ?? 0);
                $id_activ = $id_scope !== 0 ? $id_scope : $Qid_activ;
                $Qid_asignatura = (int)($in['id_asignatura'] ?? 0) ?: null;
                $id_asig_scope = (int)($in['id_asignatura_scope'] ?? 0) ?: null;
                $id_asignatura_actual = $id_asig_scope ?? $Qid_asignatura;

                $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
                $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($id_activ, $id_asignatura_actual);
                $id_profesor = $oActividadAsignatura->getId_profesor();
                $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
                $oPersonaDl = $PersonaDlRepository->findById($id_profesor);
                if ($oPersonaDl === null) {
                    $examinador_pral = _('No encuentro el profesor.');
                } else {
                    $examinador_pral = $oPersonaDl->getTituloNombre();
                }
            } else {
                if ($a_sel !== [] && $notas !== '') {
                    $parts = explode('#', (string)$a_sel[0], 2);
                    $id_activ = (int)($parts[0] ?? 0);
                    $id_asignatura = (int)($parts[1] ?? 0);
                    $cActas = $ActaRepository->getActas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
                    $oActa = $cActas[0];
                    $id_asignatura = $oActa->getId_asignatura();
                    $id_activ = $oActa->getId_activ();
                    $f_acta = $oActa->getF_acta()?->getFromLocal() ?? '';
                    $libro = $oActa->getLibro();
                    $pagina = $oActa->getPagina();
                    $linea = $oActa->getLinea();
                    $lugar = $oActa->getLugar();
                    $observ = $oActa->getObserv();
                    $id_asignatura_actual = $id_asignatura;
                } else {
                    $id_asignatura_actual = null;
                }
            }
        }

        if ($ult_lib !== '') {
            $ult_lib = sprintf(_('(último= %s)'), $ult_lib);
        }
        if ($ult_pag !== '') {
            $ult_pag = sprintf(_('(última= %s)'), $ult_pag);
        }
        if ($ult_lin !== '') {
            $ult_lin = sprintf(_('(última= %s)'), $ult_lin);
        }
        if (!empty($ult_acta)) {
            $ult_acta = sprintf(_('(última= %s)'), $ult_acta);
        }

        $cTribunal = [];
        if ($acta_actual !== '') {
            if (ConfigGlobal::mi_ambito() === 'rstgr') {
                $repoActaTribunal = $GLOBALS['container']->get(ActaTribunalRepositoryInterface::class);
            } else {
                $repoActaTribunal = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
            }
            $cTribunal = $repoActaTribunal->getActasTribunales(['acta' => $acta_actual, '_ordre' => 'orden']);
        }

        $nombre_asignatura = '';
        if ($id_asignatura_actual) {
            $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
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
