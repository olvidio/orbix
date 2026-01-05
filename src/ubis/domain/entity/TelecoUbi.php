<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\ubis\domain\value_objects\{DescTelecoText};
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\value_objects\{TipoTelecoId, TelecoUbiId, TelecoUbiItemId, NumTelecoText, ObservTelecoText};


class TelecoUbi
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_ubi;

    private int $id_tipo_teleco;

    private string|null $desc_teleco = null;

    private string $num_teleco;

    private string|null $observ = null;

    private int $id_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // -------- API VO (nueva) ---------

    public function getIdUbiVo(): TelecoUbiId
    {
        return new TelecoUbiId($this->id_ubi);
    }


    public function setIdUbiVo(TelecoUbiId $id): void
    {
        $this->id_ubi = $id->value();
    }

    /**
     * @deprecated Usar `getIdUbiVo(): TelecoUbiId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }

    /**
     * @deprecated Usar `setIdUbiVo(TelecoUbiId $id): void` en su lugar.
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }

    /**
     * @deprecated Usar `getIdTipoTelecoVo(): TipoTelecoId` en su lugar.
     */
    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco;
    }

    /**
     * @deprecated Usar `setIdTipoTelecoVo(TipoTelecoId $id): void` en su lugar.
     */
    public function setId_tipo_teleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = $id_tipo_teleco;
    }

    public function getIdTipoTelecoVo(): TipoTelecoId
    {
        return new TipoTelecoId($this->id_tipo_teleco);
    }

    public function setIdTipoTelecoVo(TipoTelecoId $id): void
    {
        $this->id_tipo_teleco = $id->value();
    }

    /**
     * @deprecated Usar `getDescTelecoVo(): ?DescTelecoText` en su lugar.
     */
    public function getId_desc_teleco(): ?string
    {
        return $this->desc_teleco;
    }

    /**
     * @deprecated Usar `setDescTelecoVo(?DescTelecoText $texto = null): void` en su lugar.
     */
    public function setId_desc_teleco(?string $desc_teleco = null): void
    {
        $this->desc_teleco = $desc_teleco;
    }

    public function getIdDescTelecoVo(): ?DescTelecoText
    {
        return DescTelecoText::fromNullableString($this->desc_teleco);
    }

    public function setIdDescTelecoVo(?DescTelecoText $texto = null): void
    {
        $this->desc_teleco = $texto?->value();
    }

    /**
     * @deprecated Usar `getNumTelecoVo(): NumTelecoText` en su lugar.
     */
    public function getNum_teleco(): string
    {
        return $this->num_teleco;
    }

    /**
     * @deprecated Usar `setNumTelecoVo(NumTelecoText $texto): void` en su lugar.
     */
    public function setNum_teleco(string $num_teleco): void
    {
        $this->num_teleco = $num_teleco;
    }

    public function getNumTelecoVo(): NumTelecoText
    {
        return new NumTelecoText($this->num_teleco);
    }

    public function setNumTelecoVo(NumTelecoText $texto): void
    {
        $this->num_teleco = $texto->value();
    }

    /**
     * @deprecated Usar `getObservVo(): ?ObservTelecoText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated Usar `setObservVo(?ObservTelecoText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservTelecoText
    {
        return ObservTelecoText::fromNullableString($this->observ);
    }

    public function setObservVo(?ObservTelecoText $texto = null): void
    {
        $this->observ = $texto?->value();
    }

    /**
     * @deprecated Usar `getIdItemVo(): TelecoUbiItemId` en su lugar.
     */
    public function getId_item(): int
    {
        return $this->id_item;
    }

    /**
     * @deprecated Usar `setIdItemVo(TelecoUbiItemId $id): void` en su lugar.
     */
    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }

    public function getIdItemVo(): TelecoUbiItemId
    {
        return new TelecoUbiItemId($this->id_item);
    }

    public function setIdItemVo(TelecoUbiItemId $id): void
    {
        $this->id_item = $id->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }
    public function getDatosCampos(): array
    {
        $oTelecoUbiSet = new Set();

        $oTelecoUbiSet->add($this->getDatosTipo_teleco());
        $oTelecoUbiSet->add($this->getDatosDesc_teleco());
        $oTelecoUbiSet->add($this->getDatosNum_teleco());
        $oTelecoUbiSet->add($this->getDatosObserv());
        return $oTelecoUbiSet->getTot();
    }

    private function getDatosTipo_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getId_tipo_teleco');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoTelecoRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombreTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposTelecoUbi'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        $oDatosCampo->setAccion('id_desc_teleco'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    private function getDatosDesc_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_desc_teleco');
        $oDatosCampo->setMetodoGet('getId_desc_teleco');
        $oDatosCampo->setMetodoSet('setId_desc_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument(DescTelecoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getDescTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayDescTeleco');
        $oDatosCampo->setDepende('id_tipo_teleco');
        return $oDatosCampo;
    }

    private function getDatosNum_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_teleco');
        $oDatosCampo->setMetodoGet('getNum_teleco');
        $oDatosCampo->setMetodoSet('setNunm_teleco');
        $oDatosCampo->setEtiqueta(_("número o siglas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    private function getDatosObserv(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ');
        $oDatosCampo->setMetodoGet('getObserv');
        $oDatosCampo->setMetodoSet('setObserv');
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }
}