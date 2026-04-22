<?php

namespace notas\model;

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\notas\application\Select1011Data;
use web\Hash;
use web\Lista;

/**
 * Tabla "select1011": listado de notas de una persona dentro del dossier
 * 1011. Instanciada dinamicamente por `DossierTipoFileSuffixResolver` en
 * `apps/dossiers/controller/dossiers_ver.php`.
 *
 * La logica de datos vive en `src\notas\application\Select1011Data`.
 * Esta clase solo se ocupa de encolar los datos en `web\Lista` y
 * renderizar el phtml.
 */
class Select1011
{
    /** @var array<int, array<int|string, mixed>> */
    private array $a_valores = [];

    private string $txt_eliminar = '';
    private string $bloque = '';

    private string $queSel = '';
    /** @var int|string */
    private $id_dossier;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 0;

    /** @var int|string|null */
    private $Qid_sel;
    /** @var int|string|null */
    private $Qscroll_id;
    private string $LinkInsert = '';

    /** @var string $aviso Mensaje al usuario (matriculas pendientes, ...) */
    private string $aviso = '';

    private function getCabeceras(): array
    {
        return [
            _("asignatura"),
            _("nota"),
            _("acta"),
            ['name' => ucfirst(_("fecha acta")), 'class' => 'fecha'],
            _("preceptor"),
            _("época"),
            _("detalle"),
            _("cursada en"),
        ];
    }

    private function getBotones(): array
    {
        return [
            ['txt' => _("modificar nota"), 'click' => 'fnjs_modificar(this.form)'],
            ['txt' => _("borrar asignatura"), 'click' => 'fnjs_borrar(this.form)'],
        ];
    }

    private function loadValores(): void
    {
        if (!empty($this->a_valores)) {
            return;
        }
        $datos = Select1011Data::getTabla($this->id_pau, $this->permiso);
        $a_valores = $datos['aValores'];
        $this->aviso = $datos['aviso'];

        if (!empty($a_valores)) {
            if (!empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }
        $this->a_valores = $a_valores;
    }

    public function getHtml(): void
    {
        $this->txt_eliminar = _("¿Está seguro que desea borrar la nota de esta asignatura?");
        $this->loadValores();

        $oHashSelect = new Hash();
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => '1011',
            'permiso' => $this->permiso,
        ]);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select1011');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->a_valores);

        $this->setLinksInsert();

        $a_campos = [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $this->LinkInsert,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'aviso' => $this->aviso,
        ];

        $oView = new ViewNewPhtml('frontend\\notas\\controller');
        $oView->renderizar('select1011.phtml', $a_campos);
    }

    public function setLinksInsert(): void
    {
        $this->LinkInsert = '';
        if ($this->permiso == 3) {
            $aQuery = [
                'mod' => 'nuevo',
                'pau' => $this->pau,
                'id_pau' => $this->id_pau,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
            ];
            array_walk($aQuery, 'core\\poner_empty_on_null');
            $this->LinkInsert = Hash::link(
                ConfigGlobal::getWeb() . '/frontend/notas/controller/form_1011.php?' . http_build_query($aQuery)
            );
        }
    }

    public function setId_dossier($id_dossier): void { $this->id_dossier = $id_dossier; }
    public function setPau($pau): void { $this->pau = (string)$pau; }
    public function setObj_pau($obj_pau): void { $this->obj_pau = (string)$obj_pau; }
    public function setId_pau($id_pau): void { $this->id_pau = (int)$id_pau; }
    public function setPermiso($permiso): void { $this->permiso = (int)$permiso; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = (string)$bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string)$queSel; }
}
