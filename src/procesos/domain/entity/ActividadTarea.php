<?php

namespace src\procesos\domain\entity;

use core\DatosCampo;
use core\Set;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\TareaId;
use src\shared\domain\traits\Hydratable;


class ActividadTarea
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private FaseId $id_fase;

    private TareaId $id_tarea;

    private ?string $desc_tarea = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getIdFaseVo(): FaseId
    {
        return $this->id_fase;
    }


    public function setIdFaseVo(FaseId|int|null $id_fase): void
    {
        $this->id_fase = $id_fase instanceof FaseId
            ? $id_fase
            : FaseId::fromNullable($id_fase);
    }

    /**
     * @deprecated use getIdFaseVo()
     */
    public function getId_fase(): int
    {
        return $this->id_fase->value();
    }

    /**
     * @deprecated use setIdFaseVo()
     */
    public function setId_fase(int $id_fase): void
    {
        $this->id_fase = FaseId::fromNullable($id_fase);
    }


    public function getIdTareaVo(): TareaId
    {
        return $this->id_tarea;
    }


    public function setIdTareaVo(TareaId|int|null $id_tarea): void
    {
        $this->id_tarea = $id_tarea instanceof TareaId
            ? $id_tarea
            : TareaId::fromNullable($id_tarea);
    }

    /**
     * @deprecated use getIdTareaVo()
     */
    public function getId_tarea(): int
    {
        return $this->id_tarea->value();
    }

    /**
     * @deprecated use setIdTareaVo()
     */
    public function setId_tarea(int $id_tarea): void
    {
        $this->id_tarea = new TareaId($id_tarea);
    }


    public function getDesc_tarea(): ?string
    {
        return $this->desc_tarea;
    }


    public function setDesc_tarea(?string $desc_tarea = null): void
    {
        $this->desc_tarea = $desc_tarea;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tarea';
    }

    public function getDatosCampos(): array
    {
        $oActividadFaseSet = new Set();
        $oActividadFaseSet->add($this->getDatosId_fase());
        $oActividadFaseSet->add($this->getDatosDesc_tarea());
        return $oActividadFaseSet->getTot();
    }


    /**
     * Recupera las propiedades del atributo id_fase de ActividadTarea
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_fase(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_fase');
        $oDatosCampo->setMetodoGet('getId_fase');
        $oDatosCampo->setMetodoSet('setId_fase');
        $oDatosCampo->setEtiqueta(_("fase a la que pertenece"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ActividadFaseRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDesc_fase'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo desc_tarea de ActividadTarea
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosDesc_tarea(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_tarea');
        $oDatosCampo->setMetodoGet('getDesc_tarea');
        $oDatosCampo->setMetodoSet('setDesc_tarea');
        $oDatosCampo->setEtiqueta(_("descripción de la tarea"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }
}