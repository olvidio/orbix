<?php
/**
 * Esta página muestra una tabla con los certificados.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        25/2/23.
 *
 */

use certificados\domain\repositories\CertificadoDlRepository;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;
use usuarios\model\entity\Local;
use web\Hash;
use web\Lista;
use function core\curso_est;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$mi_dele = ConfigGlobal::mi_delef();
$mi_region = ConfigGlobal::mi_region();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$gesDelegeacion = new GestorDelegacion();
/*
if (!$gesDelegeacion->soy_region_stgr()) {
    $msg = _("Este menú es sólo para las regiones del stgr.");
    $msg .= "<br>";
    $msg .= _("Para ver los certificados de una persona, debe ir a través de los dossiers");
    exit ($msg);
}
*/

$Qtitulo = (string)filter_input(INPUT_POST, 'titulo');
$Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
$Qid_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');

$local = empty($Qid_dossier)? FALSE: TRUE;

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array(
    'titulo' => $Qtitulo,
    'certificado' => $Qcertificado);
$oPosicion->setParametros($aGoBack, 1);

/*miro las condiciones. Si es la primera vez muestro las de este año */
$aWhere = array();
$aOperador = array();
if (!empty($Qcertificado)) {
    /* se cambia la lógica, por el cambio de nombre de la dl, no de las actas */
    $aWhere['certificado'] = $Qcertificado;
    $aOperador['certificado'] = '~';
    $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';

    // solamente los de mi región que no estén enviados
    $esquema_emisor = ConfigGlobal::mi_region_dl();
    $aWhere['esquema_emisor'] = $esquema_emisor;

    // si es número busca en la región.
    $matches = [];
    preg_match("/^(\d*)(\/)?(\d*)/", $Qcertificado, $matches);
    if (!empty($matches[1])) {
        $region = ConfigGlobal::mi_region();
        $Qcertificado_region = empty($matches[3]) ? "$region " . $matches[1] . '/' . date("y") : "$region $Qcertificado";

        $aWhere['certificado'] = $Qcertificado_region;
    }
    if ($local) {
        $CertificadoRepository = new CertificadoDlRepository();
    } else {
        $CertificadoRepository = new CertificadoRepository();
    }
    $cCertificados = $CertificadoRepository->getCertificados($aWhere, $aOperador);
    $titulo = $Qtitulo;
} else {
    if ($local) {
        // selecciono todos
        $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';
        $titulo = ucfirst(_("lista de certificados"));
        $CertificadoRepository = new CertificadoDlRepository();
    } else {
        $mes = date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        if ($mes > $fin_m) {
            $any = (int)date('Y') + 1;
        } else {
            $any = (int)date('Y');
        }
        $inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
        $fincurs_ca = curso_est("fin", $any)->format('Y-m-d');
        $txt_curso = "$inicurs_ca - $fincurs_ca";

        $aWhere['f_certificado'] = "'$inicurs_ca','$fincurs_ca'";
        $aOperador['f_certificado'] = 'BETWEEN';
        $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';

        // solamente los de mi región que no estén enviados
        $esquema_emisor = ConfigGlobal::mi_region_dl();
        $aWhere['esquema_emisor'] = $esquema_emisor;
        $aWhere['f_enviado'] = 'x';
        $aOperador['f_enviado'] = 'IS NULL';

        $titulo = ucfirst(sprintf(_("lista de certificados emitidos en el curso %s"), $txt_curso));
        $CertificadoRepository = new CertificadoRepository();
    }
    $cCertificados = $CertificadoRepository->getCertificados($aWhere, $aOperador);
}

$botones = 0; // para 'añadir certificado'
$a_botones = [];
// Si soy region del stgr
if ($gesDelegeacion->soy_region_stgr()) {
    $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
    $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
    $a_botones[] = array('txt' => _("subir pdf firmado"), 'click' => "fnjs_upload_certificado(\"#seleccionados\")");
    if (!$local) {
        $a_botones[] = array('txt' => _("enviar"), 'click' => "fnjs_enviar_certificado(\"#seleccionados\")");
    }
    $botones = 1; // para 'añadir certificado'
}

$a_botones[] = ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];

$a_cabeceras = [['name' => ucfirst(_("certificado")), 'formatter' => 'clickFormatter'],
    ['name' => ucfirst(_("fecha")), 'class' => 'fecha'],
    _("alumno"),
    _("firmado digitalmente"),
    _("adjunto"),
    _("idioma"),
    _("destino"),
];
if ($local) {
    $a_cabeceras[] = _("recibido");
} else {
    $a_cabeceras[] = _("enviado");
}

$i = 0;
$a_valores = array();
foreach ($cCertificados as $oCertificado) {
    $i++;
    $id_item = $oCertificado->getId_item();
    $certificado = $oCertificado->getCertificado();
    $f_certificado = $oCertificado->getF_certificado()->getFromLocal();
    $id_nom = $oCertificado->getId_nom();
    $firmado = $oCertificado->isFirmado();
    $nom = $oCertificado->getNom();
    $idioma = $oCertificado->getIdioma();
    $destino = $oCertificado->getDestino();
    $pdf = $oCertificado->getDocumento();
    if ($local) {
        $fecha = $oCertificado->getF_recibido()->getFromLocal();
    } else {
        $fecha = $oCertificado->getF_enviado()->getFromLocal();
    }

    if (!empty($idioma)) {
        $oLocal = new Local($idioma);
        $idioma = $oLocal->getNom_idioma();
    }

    $oPersona = Persona::NewPersona($id_nom);
    if (!is_object($oPersona)) {
        $nom_db = '';
    } else {
        $nom_db = $oPersona->getNombreApellidos();
    }
    $nom_alumno = empty($nom) ? $nom_db : $nom;

    $pagina = Hash::link('apps/certificados/controller/certificado_ver.php?' . http_build_query(array('certificado' => $certificado)));
    $a_valores[$i]['sel'] = $id_item;
    /*
    if ($_SESSION['oPerm']->have_perm_oficina('est')) {
        $a_valores[$i][1] = array('ira' => $pagina, 'valor' => $certificado);
    } else {
        $a_valores[$i][1] = $certificado;
    }
    */
    $a_valores[$i][1] = $certificado;
    $a_valores[$i][2] = $f_certificado;
    $a_valores[$i][3] = $nom_alumno;
    $a_valores[$i][4] = is_true($firmado) ? _("Sí") : _("No");
    $a_valores[$i][5] = empty($pdf) ? '' : _("Sí");
    $a_valores[$i][6] = $idioma;
    $a_valores[$i][7] = $destino;
    $a_valores[$i][8] = $fecha;
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oHash = new Hash();
$oHash->setCamposForm('certificado');

$oHash1 = new Hash();
$oHash1->setCamposForm('sel!mod');
$oHash1->setCamposNo('sel!scroll_id!mod!refresh');
$oHash1->setArrayCamposHidden(['id_dossier' => $Qid_dossier]);

$oHashDown = new Hash();
$oHashDown->setUrl('apps/certificados/controller/certificado_pdf_download.php');
// con los boolean hay problemas
$local_string = empty($local)? 'f' : 't';
$oHashDown->setArrayCamposHidden(['local' => $local_string, 'dani' => 'cosas']);
$oHashDown->setCamposForm('key');
$h_download = $oHashDown->linkConVal();

$oTabla = new Lista();
$oTabla->setId_tabla('certificado_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _("¿Está seguro que quiere eliminar el certificado?");

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'botones' => $botones,
    'txt_eliminar' => $txt_eliminar,
    'h_download' => $h_download,
    'local' => $local,
];

$oView = new core\View('certificados/controller');
$oView->renderizar('certificado_select.phtml', $a_campos);
