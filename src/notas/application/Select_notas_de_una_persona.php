<?php

namespace src\notas\application;

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
 *   - Render: {@see \frontend\notas\helpers\SelectNotasDeUnaPersonaRender}.
 */
class Select_notas_de_una_persona
{
    public function __construct(
        private readonly NotasDeUnaPersonaData $notasDeUnaPersonaData,
    ) {
    }

    /** @var array<int|string, mixed> */
    private array $a_valores = [];

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
        $datos = $this->notasDeUnaPersonaData->getTabla($this->id_pau, $this->permiso);
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

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->loadValores();
        $this->setLinksInsert();

        return [
            'segment_tipo' => 'select_notas_de_una_persona',
            'txt_eliminar' => _("¿Está seguro que desea borrar la nota de esta asignatura?"),
            'bloque' => $this->bloque,
            'aviso' => $this->aviso,
            'hash_main' => [
                'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => self::ID_DOSSIER,
                    'permiso' => $this->permiso,
                ],
            ],
            'tabla' => [
                'id_tabla' => 'select_notas_de_una_persona',
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->a_valores,
            ],
            'link_insert_spec' => $this->link_insert_spec,
            'paths' => [
                'persona_nota_eliminar' => 'src/notas/persona_nota_eliminar',
            ],
            'id_sel_value' => (string) ($this->Qid_sel ?? ''),
        ];
    }

    private function setLinksInsert(): void
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
        array_walk($aQuery, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
        $this->link_insert_spec = [
            'path' => 'frontend/notas/controller/form_notas_de_una_persona.php',
            'query' => $aQuery,
        ];
    }

    public function setId_dossier(int|string $id_dossier): void { $this->id_dossier = $id_dossier; }
    public function setPau(string $pau): void { $this->pau = $pau; }
    public function setObj_pau(string $obj_pau): void { $this->obj_pau = $obj_pau; }
    public function setId_pau(int|string $id_pau): void { $this->id_pau = (int) $id_pau; }
    public function setPermiso(int|string $permiso): void { $this->permiso = (int) $permiso; }
    public function setQid_sel(int|string|null $Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id(int|string|null $Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque(string $bloque): void { $this->bloque = $bloque; }
    public function setQueSel(string $queSel): void { $this->queSel = $queSel; }
}
