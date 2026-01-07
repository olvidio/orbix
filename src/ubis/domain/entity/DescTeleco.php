<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use function core\is_true;
use src\ubis\domain\value_objects\{TipoTelecoCode, DescTelecoOrder, DescTelecoText};


class DescTeleco
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    
    private int $id_item;
    
    private ?DescTelecoOrder $orden = null;
    
    private TipoTelecoCode $id_tipo_teleco;
    
    private ?DescTelecoText $desc_teleco = null;
    
    private bool|null $ubi = null;
    
    private bool|null $persona = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    // -------- VO API --------
    public function getOrdenVo(): ?DescTelecoOrder
    {
        return $this->orden;
    }

    public function setOrdenVo(DescTelecoOrder|int|null $valor = null): void
    {
        $this->orden = $valor instanceof DescTelecoOrder
            ? $valor
            : DescTelecoOrder::fromNullable($valor);
    }

    public function getIdTipoTelecoVo(): TipoTelecoCode
    {
        return $this->id_tipo_teleco;
    }

    public function setIdTipoTelecoVo(TipoTelecoCode $codigo): void
    {
        $this->id_tipo_teleco = $codigo;
    }

    public function getDescTelecoVo(): ?DescTelecoText
    {
        return $this->desc_teleco;
    }

    public function setDescTelecoVo(DescTelecoText|string|null $texto = null): void
    {
        $this->desc_teleco = $texto instanceof DescTelecoText
            ? $texto
            : DescTelecoText::fromNullableString($texto);
    }


    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getOrden(): ?int
    {
        return $this->orden?->value();
    }


    public function setOrden(?int $orden = null): void
    {
        $this->orden = DescTelecoOrder::fromNullable($orden);
    }

    public function getIdTipoteleco(): int
    {
        return $this->id_tipo_teleco?->value();
    }

    public function setIdTipoteleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = new TipoTelecoCode($id_tipo_teleco);
    }


    public function getDesc_teleco(): ?string
    {
        return $this->desc_teleco?->value();
    }


    public function setDesc_teleco(?string $desc_teleco = null): void
    {
        $this->desc_teleco = DescTelecoText::fromNullableString($desc_teleco);
    }


    public function isUbi(): ?bool
    {
        return $this->ubi;
    }


    public function setUbi(?bool $ubi = null): void
    {
        $this->ubi = $ubi;
    }


    public function isPersona(): ?bool
    {
        return $this->persona;
    }


    public function setPersona(?bool $persona = null): void
    {
        $this->persona = $persona;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    public function getDatosCampos(): array
    {
        $oDescTelecoSet = new Set();

        $oDescTelecoSet->add($this->getDatosOrden());
        $oDescTelecoSet->add($this->getDatosId_tipo_teleco());
        $oDescTelecoSet->add($this->getDatosDesc_teleco());
        $oDescTelecoSet->add($this->getDatosUbi());
        $oDescTelecoSet->add($this->getDatosPersona());
        return $oDescTelecoSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo orden de DescTeleco
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosOrden(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden');
        $oDatosCampo->setMetodoSet('setOrden');
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(2);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo_teleco de DescTeleco
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_tipo_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getIdTipoTelecoVo');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoTelecoRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombreTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposTeleco'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo desc_teleco de DescTeleco
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosDesc_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_desc_teleco');
        $oDatosCampo->setMetodoGet('getDesc_teleco');
        $oDatosCampo->setMetodoSet('setDesc_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(20);
        return $oDatosCampo;
    }

    private function getDatosUbi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ubi');
        $oDatosCampo->setMetodoGet('isUbi');
        $oDatosCampo->setMetodoSet('setUbi');
        $oDatosCampo->setEtiqueta(_("ubi"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosPersona(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('persona');
        $oDatosCampo->setMetodoGet('isPersona');
        $oDatosCampo->setMetodoSet('setPersona');
        $oDatosCampo->setEtiqueta(_("persona"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}