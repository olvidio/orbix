<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\value_objects\{NumTelecoText, ObservTelecoText, TelecoUbiId, TipoTelecoId};


class TelecoUbi
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private TelecoUbiId $id_ubi;

    private TipoTelecoId $id_tipo_teleco;

    private ?int $id_desc_teleco = null;

    private NumTelecoText $num_teleco;

    private ?ObservTelecoText $observ = null;

    private int $id_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // -------- API VO (nueva) ---------

    public function getIdUbiVo(): TelecoUbiId
    {
        return $this->id_ubi;
    }


    public function setIdUbiVo(TelecoUbiId|int|null $id): void
    {
        $this->id_ubi = $id instanceof TelecoUbiId
            ? $id
            : TelecoUbiId::fromNullableInt($id);
    }

    /**
     * @deprecated Usar `getIdUbiVo(): TelecoUbiId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->id_ubi->value();
    }

    /**
     * @deprecated Usar `setIdUbiVo(TelecoUbiId $id): void` en su lugar.
     */
    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = TelecoUbiId::fromNullableInt($id_ubi);
    }


    /**
     * @deprecated Usar `getIdTipoTelecoVo(): TipoTelecoId` en su lugar.
     */
    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco->value();
    }
    public function getIdTipoTelecoVo(): TipoTelecoId
    {
        return $this->id_tipo_teleco;
    }

    /**
     * @deprecated Usar `setIdTipoTelecoVo(TipoTelecoId $id): void` en su lugar.
     */
    public function setId_tipo_teleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = TipoTelecoId::fromNullableInt($id_tipo_teleco);
    }
    public function setIdTipoTelecoVo(TipoTelecoId|int|null $valor = null): void
    {
        $this->id_tipo_teleco = $valor instanceof TipoTelecoId
            ? $valor
            : TipoTelecoId::fromNullableInt($valor);
    }


    public function getId_desc_teleco(): ?int
    {
        return $this->id_desc_teleco;
    }

    public function setId_desc_teleco(?int $id = null): void
    {
        $this->id_desc_teleco = $id;
    }

    /**
     * @deprecated Usar `getNumTelecoVo(): NumTelecoText` en su lugar.
     */
    public function getNum_teleco(): string
    {
        return $this->num_teleco->value();
    }

    /**
     * @deprecated Usar `setNumTelecoVo(NumTelecoText $texto): void` en su lugar.
     */
    public function setNum_teleco(string $num_teleco): void
    {
        $this->num_teleco = NumTelecoText::fromNullableString($num_teleco);
    }

    public function getNumTelecoVo(): NumTelecoText
    {
        return $this->num_teleco;
    }

    public function setNumTelecoVo(NumTelecoText|string|null $texto): void
    {
        $this->num_teleco = $texto instanceof NumTelecoText
            ? $texto
            : NumTelecoText::fromNullableString($texto);
    }

    /**
     * @deprecated Usar `getObservVo(): ?ObservTelecoText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated Usar `setObservVo(?ObservTelecoText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservTelecoText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservTelecoText
    {
        return $this->observ;
    }

    public function setObservVo(ObservTelecoText|string|null $texto = null): void
    {
        $this->observ = $texto instanceof ObservTelecoText
            ? $texto
            : ObservTelecoText::fromNullableString($texto);
    }


    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
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