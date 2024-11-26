<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use ubis\model\entity\GestorDelegacion;
use usuarios\model\entity\Local;
use web\Hash;
use web\Lista;
use function core\curso_est;
use function core\is_true;

class CertificadoSelect
{

    /**
     * @param string $Qcertificado
     * @param mixed $Qid_sel
     * @param mixed $Qscroll_id
     * @return array
     */
    public static function getCamposVista(string $Qcertificado, mixed $Qid_sel, mixed $Qscroll_id, $inicurs_ca, $fincurs_ca): array
    {
        $gesDelegacion = new GestorDelegacion();
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
            $CertificadoRepository = new CertificadoRepository();
            $cCertificados = $CertificadoRepository->getCertificados($aWhere, $aOperador);
        } else {
            $aWhere['f_certificado'] = "'$inicurs_ca','$fincurs_ca'";
            $aOperador['f_certificado'] = 'BETWEEN';
            $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';

            // solamente los de mi región que no estén enviados
            $esquema_emisor = ConfigGlobal::mi_region_dl();
            $aWhere['esquema_emisor'] = $esquema_emisor;
            $aWhere['f_enviado'] = 'x';
            $aOperador['f_enviado'] = 'IS NULL';

            $CertificadoRepository = new CertificadoRepository();
            $cCertificados = $CertificadoRepository->getCertificados($aWhere, $aOperador);
        }

        $botones = 0; // para 'añadir certificado'
        $a_botones = [];
        // Si soy region del stgr
        if ($gesDelegacion->soy_region_stgr()) {
            $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("subir pdf firmado"), 'click' => "fnjs_upload_certificado(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("enviar"), 'click' => "fnjs_enviar_certificado(\"#seleccionados\")");
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
            _("enviado"),
        ];

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
            $fecha = $oCertificado->getF_enviado()->getFromLocal();

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

        $oHashDown = new Hash();
        $oHashDown->setUrl('apps/certificados/controller/certificado_pdf_download.php');
        $oHashDown->setCamposForm('key');
        $h_download = $oHashDown->linkConVal();

        $oTabla = new Lista();
        $oTabla->setId_tabla('certificado_select');
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setBotones($a_botones);
        $oTabla->setDatos($a_valores);

        $a_campos = [
            'oHash' => $oHash,
            'oHash1' => $oHash1,
            'oTabla' => $oTabla,
            'h_download' => $h_download,
        ];
        return $a_campos;
    }
}