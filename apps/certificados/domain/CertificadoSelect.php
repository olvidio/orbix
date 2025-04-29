<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
use src\usuarios\application\repositories\LocalRepository;
use ubis\model\entity\GestorDelegacion;
use web\Hash;
use web\Lista;
use function core\is_true;

class CertificadoSelect
{

    public static function getCamposVista(string $certificado, mixed $id_sel, mixed $scroll_id, string $inicurs_ca_iso, string $fincurs_ca_iso): array
    {
        $gesDelegacion = new GestorDelegacion();
        /*miro las condiciones. Si es la primera vez muestro las de este año */
        $aWhere = [];
        $aOperador = [];
        if (!empty($certificado)) {
            /* se cambia la lógica, por el cambio de nombre de la dl, no de las actas */
            $aWhere['certificado'] = $certificado;
            $aOperador['certificado'] = '~';
            $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';

            // solamente los de mi región que no estén enviados
            $esquema_emisor = ConfigGlobal::mi_region_dl();
            $aWhere['esquema_emisor'] = $esquema_emisor;

            // si es número busca en la región.
            $matches = [];
            preg_match("/^(\d*)(\/)?(\d*)/", $certificado, $matches);
            if (!empty($matches[1])) {
                $region = ConfigGlobal::mi_region();
                $Qcertificado_region = empty($matches[3]) ? "$region " . $matches[1] . '/' . date("y") : "$region $certificado";

                $aWhere['certificado'] = $Qcertificado_region;
            }
        } else {
            $aWhere['f_certificado'] = "'$inicurs_ca_iso','$fincurs_ca_iso'";
            $aOperador['f_certificado'] = 'BETWEEN';
            $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';

            // solamente los de mi región que no estén enviados
            $esquema_emisor = ConfigGlobal::mi_region_dl();
            $aWhere['esquema_emisor'] = $esquema_emisor;
            $aWhere['f_enviado'] = 'x';
            $aOperador['f_enviado'] = 'IS NULL';

        }
        $CertificadoRepository = new CertificadoRepository();
        $cCertificados = $CertificadoRepository->getCertificados($aWhere, $aOperador);

        $a_botones = [];
        // Si soy region del stgr
        if ($gesDelegacion->soy_region_stgr()) {
            $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("subir pdf firmado"), 'click' => "fnjs_upload_certificado(\"#seleccionados\")");
            $a_botones[] = array('txt' => _("enviar"), 'click' => "fnjs_enviar_certificado(\"#seleccionados\")");
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
        $a_valores = [];
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
                $LocalRepository = new LocalRepository();
                $oLocal = $LocalRepository->findById($idioma);
                $idioma = $oLocal->getNom_idioma();
            }

            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $nom_db = '';
            } else {
                $nom_db = $oPersona->getNombreApellidos();
            }
            $nom_alumno = empty($nom) ? $nom_db : $nom;

            $a_valores[$i]['sel'] = $id_item;
            $a_valores[$i][1] = $certificado;
            $a_valores[$i][2] = $f_certificado;
            $a_valores[$i][3] = $nom_alumno;
            $a_valores[$i][4] = is_true($firmado) ? _("Sí") : _("No");
            $a_valores[$i][5] = empty($pdf) ? '' : _("Sí");
            $a_valores[$i][6] = $idioma;
            $a_valores[$i][7] = $destino;
            $a_valores[$i][8] = $fecha;
        }
        if (!empty($id_sel)) {
            $a_valores['select'] = $id_sel;
        }
        if (!empty($scroll_id)) {
            $a_valores['scroll_id'] = $scroll_id;
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

        return [
            'oHash' => $oHash,
            'oHash1' => $oHash1,
            'oTabla' => $oTabla,
            'h_download' => $h_download,
        ];
    }
}