<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use personas\model\entity\Persona;
use src\certificados\application\repositories\CertificadoRecibidoRepository;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\is_true;

/**
 * Gestiona el dossier 1301: Actividades a las que asiste una persona.
 *
 *
 * @package    orbix
 * @subpackage    asistencias
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select1010
{
    // --------- Variables internas de la clase.
    /**
     * array con los permisos (si o no) para asignar las actividades (según el tipo: nº)
     * según el tipo de persona de que se trate y quién seamos nosotros.
     * @var array $ref_perm
     */
    private array $ref_perm;
    /* @var $msg_err string */
    private string $msg_err;
    /* @var $a_valores array */
    private array $a_valores;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private string $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private string $bloque;

    // ---------- Variables requeridas
    /* @var $queSel integer */
    private string $queSel;
    /* @var $id_dossier integer */
    private int $id_dossier;
    /* @var $pau string */
    private string $pau;
    /* @var $obj_pau string */
    private string $obj_pau;
    /* @var $id_pau integer */
    private int $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer $permiso
     */
    private $permiso;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;
    private mixed $status;

    private function getBotones(): array
    {
        $a_botones[] = ['txt' => _("descargar pdf"), 'click' => "fnjs_descargar_pdf(\"#seleccionados\")"];
        $a_botones[] = array('txt' => _("modificar certificado"), 'click' => "fnjs_upload_certificado(\"#seleccionados\")");
        $a_botones[] = array('txt' => _("eliminar"), 'click' => "fnjs_eliminar_certificado(\"#seleccionados\")");
        return $a_botones;
    }

    private function getCabeceras(): array
    {
        return [
            _("certificado"),
            _("fecha certificado"),
            _("firmado digitalmente"),
            _("adjunto"),
            _("recibido"),
        ];
    }

    private function getValores(): array
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $oPersona = Persona::newPersona($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>$oPersona con id_nom: $this->id_pau en  " . __FILE__ . ": line " . __LINE__;
            exit($this->msg_err);
        }
        $aWhere = [
            'id_nom' => $this->id_pau,
            //'firmado' => true,           mejor que se vean todos
            '_ordre' => 'f_certificado'
        ];

        $certificadoRecibidoRepository = new CertificadoRecibidoRepository();
        $cCertificados = $certificadoRecibidoRepository->getCertificados($aWhere);

        $i = 0;
        $a_valores = [];
        foreach ($cCertificados as $oCertificado) {
            $i++;

            $id_item = $oCertificado->getId_item();
            $certificado = $oCertificado->getCertificado();
            $firmado = $oCertificado->isFirmado();
            $f_certificado = $oCertificado->getF_certificado()->getFromLocal();
            $f_recibido = $oCertificado->getF_recibido()->getFromLocal();
            $pdf = $oCertificado->getDocumento();

            $a_valores[$i]['sel'] = $id_item;
            $a_valores[$i][1] = $certificado;
            $a_valores[$i][2] = $f_certificado;
            $a_valores[$i][3] = is_true($firmado) ? _("Sí") : _("No");
            $a_valores[$i][4] = empty($pdf) ? '' : _("Sí");
            $a_valores[$i][5] = $f_recibido;

        }
        if (!empty($a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (!empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->a_valores = $a_valores;
    }

    public function getHtml()
    {
        $this->txt_eliminar = _("No tiene permisos para eliminar");
        // En el caso de actualizar la misma página (fnjs_actualizar) solo me quedo con la última (stack=0).
        $oPosicion = new Posicion();
        $stack = $oPosicion->getStack(0);

        $oHashSelect = new Hash();
        //$oHashSelect->setCamposForm('sel');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 1,
            'stack' => $stack,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new Lista();
        $oTabla->setId_tabla('select1010');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        $oHashDown = new Hash();
        $oHashDown->setUrl('frontend/certificados/controller/certificado_recibido_pdf_download.php');
        $oHashDown->setCamposForm('key');
        $h_download = $oHashDown->linkSinVal();

        $aQuery = ['nuevo' => 1, 'id_nom' => $this->id_pau];
        $url_nuevo = Hash::link(ConfigGlobal::getWeb() . '/frontend/certificados/controller/certificado_recibido_adjuntar.php?' . http_build_query($aQuery));

        $a_campos = [
            'oPosicion' => $oPosicion,
            'oTabla' => $oTabla,
            'url_nuevo' => $url_nuevo,
            'oHashSelect' => $oHashSelect,
            'h_download' => $h_download,
        ];

        $oView = new ViewNewPhtml('frontend\certificados\view');
        $oView->renderizar('select1010.phtml', $a_campos);
    }

    public function getId_dossier(): int
    {
        return $this->id_dossier;
    }

    public function getPau(): string
    {
        return $this->pau;
    }

    public function getObj_pau(): string
    {
        return $this->obj_pau;
    }

    public function getId_pau(): int
    {
        return $this->id_pau;
    }

    public function getPermiso(): int
    {
        return $this->permiso;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setId_dossier($Qid_dossier): void
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau): void
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau): void
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau): void
    {
        $this->id_pau = $Qid_pau;
    }

    public function setPermiso($Qpermiso): void
    {
        $this->permiso = $Qpermiso;
    }

    public function setStatus($Qstatus): void
    {
        $this->status = $Qstatus;
    }

    public function setQid_sel($Qid_sel): void
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id): void
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque): void
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel): void
    {
        $this->queSel = $queSel;
    }


}
