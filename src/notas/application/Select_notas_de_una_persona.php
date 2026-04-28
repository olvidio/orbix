<?php

namespace src\notas\application;

use frontend\notas\helpers\SelectNotasDeUnaPersonaUrlSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Widget "select_notas_de_una_persona": listado de notas (`PersonaNota`)
 * de una persona dentro del dossier 1011. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver}
 * desde `frontend/dossiers/controller/dossiers_ver.php` usando el codigo
 * `notas_de_una_persona` de `d_tipos_dossiers`.
 *
 * Sucesor de `apps/notas/model/Select1011.php` (eliminado).
 *
 *   - La logica de datos vive en {@see NotasDeUnaPersonaData::getTabla()}
 *     (retorna `['aValores' => [...], 'aviso' => string]`).
 *   - Este widget arma `web\\Lista` + `web\\Hash`, firma el link "nuevo" via
 *     `frontend\\notas\\helpers\\SelectNotasDeUnaPersonaUrlSigning` y rendera
 *     `frontend/notas/view/select_notas_de_una_persona.phtml`.
 *
 * Esta clase es puramente "frontend renderer": no toca BD (eso lo hace
 * `NotasDeUnaPersonaData`) y emite HTML a traves del ViewNewPhtml.
 */
class Select_notas_de_una_persona
{
    /** @var array<int|string, mixed> */
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
    /** @var array{path: string, query: array<string, mixed>}|null */
    private ?array $link_insert_spec = null;

    /** Mensaje al usuario (matriculas pendientes, ...). */
    private string $aviso = '';

    /** Permiso minimo para poder anadir una nueva nota. */
    private const PERMISO_INSERTAR = 3;
    /** `id_dossier` de este widget (literal historico). */
    private const ID_DOSSIER = '1011';

    /** @return array<int, string|array{name: string, class: string}> */
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

    /** @return array<int, array{txt: string, click: string}> */
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
        $datos = NotasDeUnaPersonaData::getTabla($this->id_pau, $this->permiso);
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

        $oHashSelect = new HashFront();
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => self::ID_DOSSIER,
            'permiso' => $this->permiso,
        ]);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select_notas_de_una_persona');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->a_valores);

        $this->setLinksInsert();

        $signed = SelectNotasDeUnaPersonaUrlSigning::sign(['link_insert_spec' => $this->link_insert_spec]);
        $url_persona_nota_eliminar = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/src/notas/persona_nota_eliminar';

        $a_campos = [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $signed['link_insert'],
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'aviso' => $this->aviso,
            'url_persona_nota_eliminar' => $url_persona_nota_eliminar,
        ];

        (new ViewNewPhtml('frontend\\notas\\controller'))->renderizar('select_notas_de_una_persona.phtml', $a_campos);
    }

    public function setLinksInsert(): void
    {
        $this->link_insert_spec = null;
        if ($this->permiso !== self::PERMISO_INSERTAR) {
            return;
        }
        $aQuery = [
            'mod' => 'nuevo',
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'id_dossier' => $this->id_dossier,
        ];
        array_walk($aQuery, 'core\\poner_empty_on_null');
        $this->link_insert_spec = [
            'path' => 'frontend/notas/controller/form_notas_de_una_persona.php',
            'query' => $aQuery,
        ];
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
