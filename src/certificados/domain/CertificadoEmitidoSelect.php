<?php

namespace src\certificados\domain;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use function src\shared\domain\helpers\is_true;

class CertificadoEmitidoSelect
{
    public function __construct(
        private readonly CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly LocalRepositoryInterface $localRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getCamposVista(string $certificado, string $inicurs_ca_iso, string $fincurs_ca_iso): array
    {
        $aWhere = [];
        $aOperador = [];
        if ($certificado !== '') {
            $aWhere['certificado'] = $certificado;
            $aOperador['certificado'] = '~';
            $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';
            $aWhere['esquema_emisor'] = ConfigGlobal::mi_region_dl();

            $matches = [];
            preg_match('/^(\d*)(\/)?(\d*)/', $certificado, $matches);
            if (($matches[1] ?? '') !== '') {
                $region = ConfigGlobal::mi_region();
                $Qcertificado_region = $matches[3] !== ''
                    ? "$region $certificado"
                    : "$region " . $matches[1] . '/' . date('y');
                $aWhere['certificado'] = $Qcertificado_region;
            }
        } else {
            $aWhere['f_certificado'] = "'$inicurs_ca_iso','$fincurs_ca_iso'";
            $aOperador['f_certificado'] = 'BETWEEN';
            $aWhere['_ordre'] = 'f_certificado DESC, certificado DESC';
            $aWhere['esquema_emisor'] = ConfigGlobal::mi_region_dl();
            $aWhere['f_enviado'] = 'x';
            $aOperador['f_enviado'] = 'IS NULL';
        }

        $cCertificados = $this->certificadoEmitidoRepository->getCertificados($aWhere, $aOperador);

        $a_botones = [];
        if ($this->delegacionRepository->soy_region_stgr()) {
            $a_botones[] = ['txt' => _('eliminar'), 'click' => 'fnjs_eliminar("#seleccionados")'];
            $a_botones[] = ['txt' => _('modificar'), 'click' => 'fnjs_modificar("#seleccionados")'];
            $a_botones[] = ['txt' => _('subir pdf firmado'), 'click' => 'fnjs_upload_certificado("#seleccionados")'];
            $a_botones[] = ['txt' => _('enviar'), 'click' => 'fnjs_enviar_certificado("#seleccionados")'];
        }
        $a_botones[] = ['txt' => _('descargar pdf'), 'click' => 'fnjs_descargar_pdf("#seleccionados")'];

        $a_cabeceras = [
            ['name' => ucfirst(_('certificado')), 'formatter' => 'clickFormatter'],
            ['name' => ucfirst(_('fecha')), 'class' => 'fecha'],
            _('alumno'),
            _('firmado digitalmente'),
            _('adjunto'),
            _('idioma'),
            _('destino'),
            _('enviado'),
        ];

        $a_valores = [];
        $i = 0;
        foreach ($cCertificados as $oCertificado) {
            $i++;
            $id_item = $oCertificado->getId_item();
            $certificadoTxt = $oCertificado->getCertificado();
            $f_certificado = $oCertificado->getF_certificado()->getFromLocal();
            $id_nom = (int) ($oCertificado->getId_nom() ?? 0);
            $firmado = $oCertificado->isFirmado();
            $nom = $oCertificado->getNom();
            $idioma = $oCertificado->getIdiomaVo()?->value();
            $destino = $oCertificado->getDestino();
            $pdf = $oCertificado->getDocumento();
            $fecha = $oCertificado->getF_enviado()->getFromLocal();

            if ($idioma !== null && $idioma !== '') {
                $oLocal = $this->localRepository->findById($idioma);
                if ($oLocal !== null) {
                    $idioma = $oLocal->getNomIdiomaAsString();
                }
            }

            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            $nom_db = $oPersona !== null ? $oPersona->getNombreApellidos() : '';
            $nom_alumno = ($nom === null || $nom === '') ? $nom_db : $nom;

            $a_valores[$i]['sel'] = $id_item;
            $a_valores[$i][1] = $certificadoTxt;
            $a_valores[$i][2] = $f_certificado;
            $a_valores[$i][3] = $nom_alumno;
            $a_valores[$i][4] = is_true($firmado) ? _('Sí') : _('No');
            $a_valores[$i][5] = ($pdf === null || $pdf === '') ? '' : _('Sí');
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
