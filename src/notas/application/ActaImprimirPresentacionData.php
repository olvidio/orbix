<?php

declare(strict_types=1);

namespace src\notas\application;


use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use frontend\shared\config\OrbixRuntime;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Datos compartidos por `acta_imprimir` y el HTML de `acta_imprimir_mpdf`.
 */
final class ActaImprimirPresentacionData
{

    public function __construct(
        private readonly ActaRepositoryInterface $actaRepository,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly AsignaturaTipoRepositoryInterface $asignaturaTipoRepository,
        private readonly ActaTribunalRepositoryInterface $actaTribunalRepository,
        private readonly ActaTribunalDlRepositoryInterface $actaTribunalDlRepository,
        private readonly DatosActa $datosActa,
    ) {
    }
    /**
     * @param 'imprimir'|'mpdf' $mode
     * @return array<string, mixed>
     */
    public function execute(string $acta, string $mode): array
    {
        if ($acta === '') {
            throw new \InvalidArgumentException(_('Falta el acta'));
        }

        $replace = OrbixRuntime::latinHtmlEntityReplaceMap();
        $oConfig = $_SESSION['oConfig'] ?? null;
        $region_latin = is_object($oConfig) && method_exists($oConfig, 'getNomRegionLatin')
            ? (string) $oConfig->getNomRegionLatin()
            : '';
        $nombre_prelatura = strtr('PRAELATURA SANCTAE CRUCIS ET OPERIS DEI', $replace);
        $reg_stgr = 'Stgr' . ConfigGlobal::mi_region();

        $ActaRepository = $this->actaRepository;
        $oActa = $ActaRepository->findById($acta);
        if ($oActa === null) {
            throw new \RuntimeException(sprintf(_('No se encuentra el acta: %s'), $acta));
        }
        $id_asignatura = $oActa->getId_asignatura();
        if ($id_asignatura === null) {
            throw new \RuntimeException(_('El acta no tiene asignatura asociada'));
        }
        $id_activ = $oActa->getId_activ();
        $oF_acta = $oActa->getF_acta();
        $libro = $oActa->getLibro();
        $pagina = $oActa->getPagina();
        $linea = $oActa->getLinea();
        $lugar = $oActa->getLugar();
        $observ = $oActa->getObserv();

        $AsignaturaRepository = $this->asignaturaRepository;
        $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
        if ($oAsignatura === null) {
            throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), $id_asignatura));
        }
        $nombre_corto = $oAsignatura->getNombre_corto();
        $nombre_asignatura = strtr((string)$oAsignatura->getNombre_asignatura(), $replace);
        $anyRaw = $oAsignatura->getYear();
        $anyInt = is_numeric($anyRaw) ? (int) $anyRaw : 0;
        $any_display = match ($anyInt) {
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            default => '',
        };

        $id_tipo = $oAsignatura->getId_tipo();
        if ($id_tipo === null) {
            throw new \RuntimeException(_('La asignatura no tiene tipo'));
        }
        $oAsignaturaTipo = $this->asignaturaTipoRepository->findById($id_tipo);
        if ($oAsignaturaTipo === null) {
            throw new \RuntimeException(sprintf(_('No se ha encontrado el tipo de asignatura con id: %s'), $id_tipo));
        }
        $curso = strtr((string) ($oAsignaturaTipo->getTipoLatinVo()?->value() ?? ''), $replace);

        $cPersonaNotas = $this->datosActa->getNotasActa($acta);
        $errores = '';
        $aPersonasNotas = [];
        foreach ($cPersonaNotas as $oPersonaNota) {
            $id_nom = $oPersonaNota->getId_nom();
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $errores .= '<br>' . sprintf(_('existe una nota de la que no se tiene acceso al nombre (id_nom = %s): es de otra dl o \'de paso\' borrado.'), $id_nom);
                $errores .= ' ' . _('no aparece en la lista');
                continue;
            }
            $nom = $oPersona->getApellidosUpperNombre();
            $nota = $oPersonaNota->getNota_txt();
            $aPersonasNotas[$nom] = $nota;
        }
        uksort($aPersonasNotas, 'src\\shared\\domain\\helpers\\strsinacentocmp');
        $num_alumnos = count($aPersonasNotas);

        $ambito = ConfigGlobal::mi_ambito();
        if ($mode === 'imprimir') {
            if ($ambito === 'rstgr') {
                $repoActaTribunal = $this->actaTribunalRepository;
            } else {
                $repoActaTribunal = $this->actaTribunalDlRepository;
            }
        } else {
            $repoActaTribunal = $this->actaTribunalRepository;
        }

        $cTribunal = $repoActaTribunal->getActasTribunales(['acta' => $acta, '_ordre' => 'orden']);
        $num_examinadores = count($cTribunal);
        $examinadores = [];
        foreach ($cTribunal as $oTribunal) {
            $examinadores[] = $oTribunal->getExaminador();
        }

        $lin_A4 = $mode === 'imprimir' ? 45 : 42;
        $lin_encabezado = 16;
        $lin_encabezado_tribunal = 4;
        $lin_tribunal = $lin_encabezado_tribunal + 2 * $num_examinadores;
        $lin_max_cara_A = $lin_A4 - $lin_encabezado - 2;
        if ($num_alumnos > $lin_max_cara_A) {
            $alum_cara_A = $lin_max_cara_A;
        } else {
            $alum_cara_A = $num_alumnos;
        }
        $alum_cara_B = $num_alumnos - $alum_cara_A;

        $lugar_fecha = $lugar . ',  ' . ($oF_acta instanceof DateTimeLocal ? $oF_acta->getFechaLatin() : '');

        $tribunal_html = "<div class=\"tribunal\">TRIBUNAL:</div>";
        foreach ($examinadores as $examinador) {
            $tribunal_html .= "<div class=\"examinador\">$examinador</div>";
        }
        $tribunal_html .= "<div class=\"fecha\">$lugar_fecha</div>";
        $tribunal_html .= "<div class=\"sello\">L.S.<br>Studii Generalis</div>";

        $aPersonasNotas_list = [];
        foreach ($aPersonasNotas as $nom => $nota) {
            $aPersonasNotas_list[] = ['nom' => $nom, 'nota' => $nota];
        }

        return [
            'acta' => $acta,
            'id_asignatura' => $id_asignatura,
            'id_activ' => $id_activ,
            'libro' => $libro,
            'pagina' => $pagina,
            'linea' => $linea,
            'lugar' => $lugar,
            'observ' => $observ,
            'nombre_corto' => $nombre_corto,
            'nombre_asignatura' => $nombre_asignatura,
            'curso' => $curso,
            'any' => $any_display,
            'region_latin' => $region_latin,
            'nombre_prelatura' => $nombre_prelatura,
            'reg_stgr' => $reg_stgr,
            'errores' => $errores,
            'aPersonasNotas_list' => $aPersonasNotas_list,
            'num_alumnos' => $num_alumnos,
            'lin_max_cara_A' => $lin_max_cara_A,
            'lin_tribunal' => $lin_tribunal,
            'alum_cara_A' => $alum_cara_A,
            'alum_cara_B' => $alum_cara_B,
            'examinadores' => $examinadores,
            'lugar_fecha' => $lugar_fecha,
            'tribunal_html' => $tribunal_html,
        ];
    }
}
