<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\personas\domain\value_objects\AsistenciaDescripcionText;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class UltimaAsistencia
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private ?ActividadTipoId $id_tipo_activ = null;

    private ?DateTimeLocal $f_ini = null;

    private ?AsistenciaDescripcionText $descripcion = null;

    private ?bool $cdr = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    public function getId_tipo_activ(): ?string
    {
        return $this->id_tipo_activ?->value();
    }

    public function setId_tipo_activ(?int $id_tipo_activ = null): void
    {
        $this->id_tipo_activ = ActividadTipoId::fromInt($id_tipo_activ);
    }

    public function getIdTipoActivVo(): ActividadTipoId
    {
        return $this->id_tipo_activ;
    }

    public function setIdTipoActivVo(ActividadTipoId|int|null $tipoActiv = null): void
    {
        $this->id_tipo_activ = $tipoActiv instanceof ActividadTipoId
            ? $tipoActiv
            : ActividadTipoId::fromInt($tipoActiv);
    }


    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }

    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }

    /**
     * @deprecated use getDescripcionVo()
     */
    public function getDescripcion(): ?string
    {
        return $this->descripcion?->value();
    }

    /**
     * @deprecated use setDescripcionVo()
     */
    public function setDescripcion(?string $description = null): void
    {
        $this->descripcion = AsistenciaDescripcionText::fromNullableString($description);
    }

    public function getDescripcionVo(): ?AsistenciaDescripcionText
    {
        return $this->descripcion;
    }

    public function setDescripcionVo(AsistenciaDescripcionText|string|null $vo = null): void
    {
        $this->descripcion = $vo instanceof AsistenciaDescripcionText
            ? $vo
            : AsistenciaDescripcionText::fromNullableString($vo);
    }


    public function isCdr(): ?bool
    {
        return $this->cdr;
    }

    public function setCdr(?bool $cdr = null): void
    {
        $this->cdr = $cdr;
    }


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }

    public function getDatosCampos(): array
    {
        $oSet = new Set();
        //$oSet->add($this->getDatosId_nom());
        $oSet->add($this->getDatosId_tipo_activ());
        $oSet->add($this->getDatosF_ini());
        $oSet->add($this->getDatosDescripcion());
        $oSet->add($this->getDatosCdr());
        return $oSet->getTot();
    }

    private function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        return $oDatosCampo;
    }

    private function getDatosId_tipo_activ(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_activ');
        $oDatosCampo->setMetodoGet('getId_tipo_activ');
        $oDatosCampo->setMetodoSet('setId_tipo_activ');
        $oDatosCampo->setEtiqueta(_("tipo de actividad"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoDeActividadRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposActividad'); // método con que crear la lista de opciones del Gestor objeto relacionado.

        return $oDatosCampo;
    }

    private function getDatosF_ini(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ini');
        $oDatosCampo->setMetodoGet('getF_ini');
        $oDatosCampo->setMetodoSet('setF_ini');
        $oDatosCampo->setEtiqueta(_("fecha inicio actividad"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosDescripcion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('descripcion');
        $oDatosCampo->setMetodoGet('getDescripcion');
        $oDatosCampo->setMetodoSet('setDescripcion');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(70);
        return $oDatosCampo;
    }

    private function getDatosCdr(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cre');
        $oDatosCampo->setMetodoGet('isCdr');
        $oDatosCampo->setMetodoSet('setCdr');
        $oDatosCampo->setEtiqueta(_("cdr"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}


