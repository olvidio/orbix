<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoEmitidoRepository;
use src\usuarios\application\repositories\LocalRepository;
use src\ubis\application\repositories\DelegacionRepository;
use function core\is_true;

class CertificadoEmitidoSelect
{

    public static function getCamposVista(string $certificado, string $inicurs_ca_iso, string $fincurs_ca_iso): array
    {
        $gesDelegacion = new DelegacionRepository();
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
        $certificadoEmitidoRepository = new CertificadoEmitidoRepository();
        $cCertificados = $certificadoEmitidoRepository->getCertificados($aWhere, $aOperador);
        if ($cCertificados === false) {
            return [
                'success' => false,
                'error_txt' => $_SESSION['oGestorErrores']->leerErrorAppLastError(),
            ];
        }

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
                $idioma = $oLocal->getNom_idiomaAsString();
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


        return [
            'success' => true,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_botones' => $a_botones,
        ];
    }
}