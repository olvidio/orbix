<?php

namespace src\ubiscamas\domain;

use src\shared\config\ConfigGlobal;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Widget dossier 2006 (codigo habitaciones_cdc): habitaciones de un centro.
 *
 * Render: {@see \frontend\ubiscamas\helpers\SelectHabitacionesCdcRender}.
 */
class Select_habitaciones_cdc
{
    public function __construct(
        private HabitacionDlRepositoryInterface $habitacionRepository,
    ) {
    }

    /** @var list<array{perm: mixed, obj: string, nom: string}> */
    private array $a_ref_perm = [];

    private string $msg_err = '';

    /** @var array<int, array<string, mixed>> */
    private array $a_valores = [];

    private string $bloque = '';

    private string $queSel = '';
    private int $id_dossier = 0;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 1;

    /** @var int|string|null */
    private $Qid_sel;
    /** @var int|string|null */
    private $Qscroll_id;

    /** @var list<array{label: string, spec: array{path: string, query: array<string, mixed>}}> */
    private array $a_links_dl_specs = [];

    /** @return list<array{txt: string, click: string}> */
    private function getBotones(): array
    {
        return [
            ['txt' => _("modificar habitación"), 'click' => "fnjs_mod_habitacion(this.form)"],
            ['txt' => _("borrar habitación"), 'click' => "fnjs_eliminar_habitacion(this.form)"],
        ];
    }

    /** @return list<string> */
    private function getCabeceras(): array
    {
        return [
            _("nombre"),
            _("planta"),
            _("adaptada"),
            _("sillon"),
            _("observaciones"),
            _("despacho"),
            _("tipoLavabo"),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function getValores(): array
    {
        if ($this->a_valores === []) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $c = 0;
        $a_valores = [];
        $cHabitaciones = $this->habitacionRepository->getHabitaciones([
            'id_ubi' => $this->id_pau,
            '_ordre' => 'orden, planta',
        ]);
        $tiposLavabo = TipoLavabo::getArrayTipoLavabo();
        foreach ($cHabitaciones as $oHabitacion) {
            $c++;
            $id_habitacion = $oHabitacion->getId_habitacion();
            $id_ubi = $oHabitacion->getId_ubi();
            $orden = $oHabitacion->getOrden();
            $nombre = $oHabitacion->getNombre();
            $planta = $oHabitacion->getPlanta();
            $sillon = $oHabitacion->isSillon();
            $adaptada = $oHabitacion->isAdaptada();
            $observaciones = $oHabitacion->getObservacionesVo()?->value() ?? '';
            $tipoLavabo = $oHabitacion->gettipoLavabo();
            $despacho = $oHabitacion->isDespacho();

            $sillon_txt = FuncTablasSupport::isTrueTxt($sillon);
            $adaptada_txt = FuncTablasSupport::isTrueTxt($adaptada);
            $despacho_txt = FuncTablasSupport::isTrueTxt($despacho);

            $tipoLavabo_txt = $tiposLavabo[$tipoLavabo ?? 0] ?? '';

            $a_valores[$c]['sel'] = "$id_habitacion#$id_ubi#$orden";
            $a_valores[$c][1] = $nombre;
            $a_valores[$c][2] = $planta;
            $a_valores[$c][3] = $adaptada_txt;
            $a_valores[$c][4] = $sillon_txt;
            $a_valores[$c][5] = $observaciones;
            $a_valores[$c][6] = $despacho_txt;
            $a_valores[$c][7] = $tipoLavabo_txt;
        }

        /** @var array<int, array<string, mixed>> $a_valores */
        $this->a_valores = $a_valores;
        if ($this->msg_err !== '') {
            echo $this->msg_err;
        }
    }

    /**
     * Datos puros para {@see \frontend\ubiscamas\helpers\SelectHabitacionesCdcRender}.
     *
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->setLinksInsert();

        $aQueryNuevo = ['nuevo' => 1, 'id_ubi' => $this->id_pau];

        return [
            'hash' => [
                'campos_form' => '',
                'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => $this->id_dossier,
                    'permiso' => 3,
                    'bloque' => $this->bloque,
                    'scroll_id' => $this->Qscroll_id,
                    'id_sel' => $this->Qid_sel,
                ],
            ],
            'tabla' => [
                'id_tabla' => 'select2006',
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->getValores(),
            ],
            'url_nuevo_spec' => [
                'path' => 'frontend/ubiscamas/controller/habitacion_form.php',
                'query' => $aQueryNuevo,
            ],
            'a_links_dl_specs' => $this->a_links_dl_specs,
        ];
    }

    private function setLinksInsert(): void
    {
        $this->a_links_dl_specs = [];
        if ($this->a_ref_perm === [] || ConfigGlobal::mi_ambito() === 'rstgr') {
            return;
        }
        foreach ($this->a_ref_perm as $val) {
            $perm = $val['perm'];
            $obj_pau = $val['obj'];
            $nom = $val['nom'];
            if (!empty($perm)) {
                $aQuery = [
                    'mod' => 'nuevo',
                    'pau' => $this->pau,
                    'obj_pau' => $obj_pau,
                    'id_pau' => $this->id_pau,
                ];
                array_walk($aQuery, FuncTablasSupport::ponerEmptyOnNull(...));
                $nom2 = sprintf(_("añadir %s"), $nom);
                $this->a_links_dl_specs[] = [
                    'label' => $nom2,
                    'spec' => [
                        'path' => 'frontend/ubiscamas/controller/habitacion_form.php',
                        'query' => $aQuery,
                    ],
                ];
            }
        }
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

    public function setId_dossier(int|string $Qid_dossier): void
    {
        $this->id_dossier = (int) $Qid_dossier;
    }

    public function setPau(string $Qpau): void
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau(string $Qobj_pau): void
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau(int|string $Qid_pau): void
    {
        $this->id_pau = (int) $Qid_pau;
    }

    public function setPermiso(int|string $Qpermiso): void
    {
        $this->permiso = (int) $Qpermiso;
    }

    public function setQid_sel(int|string|null $Qid_sel): void
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id(int|string|null $Qscroll_id): void
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque(string $bloque): void
    {
        $this->bloque = $bloque;
    }

    public function setQueSel(string $queSel): void
    {
        $this->queSel = $queSel;
    }
}
