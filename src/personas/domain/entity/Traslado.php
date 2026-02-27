<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\personas\domain\value_objects\NombreCentroText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\TrasladoTipoCmbCode;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class Traslado
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private ?DateTimeLocal $f_traslado = null;

    private TrasladoTipoCmbCode $tipo_cmb;

    private ?int $id_ctr_origen = null;

    private ?NombreCentroText $ctr_origen = null;

    private ?int $id_ctr_destino = null;

    private NombreCentroText $ctr_destino;

    private ?ObservText $observ = null;

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


    public function getF_traslado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_traslado ?? new NullDateTimeLocal;
    }
    public function setF_traslado(DateTimeLocal|null $f_traslado = null): void
    {
        $this->f_traslado = $f_traslado;
    }

    /**
     * @deprecated use getTipoCmbVo()
     */
    public function getTipo_cmb(): string
    {
        return $this->tipo_cmb->value();
    }
    /**
     * @deprecated use setTipoCmbVo()
     */
    public function setTipo_cmb(string $tipo_cmb): void
    {
        $this->tipo_cmb =  TrasladoTipoCmbCode::fromString($tipo_cmb);
    }
    public function getTipoCmbVo(): TrasladoTipoCmbCode
    {
        return $this->tipo_cmb;
    }
    public function setTipoCmbVo(TrasladoTipoCmbCode|string|null $vo): void
    {
        $this->tipo_cmb = $vo instanceof TrasladoTipoCmbCode
            ? $vo
            : TrasladoTipoCmbCode::fromNullableString($vo);
    }


    public function getId_ctr_origen(): ?int
    {
        return $this->id_ctr_origen;
    }
    public function setId_ctr_origen(?int $id_ctr_origen = null): void
    {
        $this->id_ctr_origen = $id_ctr_origen;
    }

    /**
     * @deprecated use getCtrOrigenVo()
     */
    public function getCtr_origen(): ?string
    {
        return $this->ctr_origen?->value();
    }
    /**
     * @deprecated use setCtrOrigenVo()
     */
    public function setCtr_origen(?string $ctr_origen = null): void
    {
        $this->ctr_origen = NombreCentroText::fromNullableString($ctr_origen);
    }
    public function getCtrOrigenVo(): ?NombreCentroText
    {
        return $this->ctr_origen;
    }
    public function setCtrOrigenVo(NombreCentroText|string|null $vo = null): void
    {
        $this->ctr_origen = $vo instanceof NombreCentroText
            ? $vo
            : NombreCentroText::fromNullableString($vo);
    }


    public function getId_ctr_destino(): ?int
    {
        return $this->id_ctr_destino;
    }
    public function setId_ctr_destino(?int $id_ctr_destino = null): void
    {
        $this->id_ctr_destino = $id_ctr_destino;
    }

    /**
     * @deprecated use getCtrDestinoVo()
     */
    public function getCtr_destino(): string
    {
        return $this->ctr_destino->value();
    }
    /**
     * @deprecated use setCtrDestinoVo()
     */
    public function setCtr_destino(string $ctr_destino): void
    {
        $this->ctr_destino =  NombreCentroText::fromNullableString($ctr_destino);
    }
    public function getCtrDestinoVo(): NombreCentroText
    {
        return $this->ctr_destino;
    }
    public function setCtrDestinoVo(NombreCentroText|string|null $vo = null): void
    {
        $this->ctr_destino = $vo instanceof NombreCentroText
            ? $vo
            : NombreCentroText::fromNullableString($vo);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }
    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservText::fromNullableString($observ);
    }
    public function getObservVo(): ?ObservText
    {
        return $this->observ;
    }
    public function setObservVo(ObservText|string|null $vo = null): void
    {
        $this->observ = $vo instanceof ObservText
            ? $vo
            : ObservText::fromNullableString($vo);
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
        $oSet->add($this->getDatosF_traslado());
        $oSet->add($this->getDatosTipoCambio());
        $oSet->add($this->getDatosCentroOrigen());
        $oSet->add($this->getDatosIdCentroOrigen());
        $oSet->add($this->getDatosCentroDestino());
        $oSet->add($this->getDatosIdCentroDestino());
        $oSet->add($this->getDatosObserv());

        return $oSet->getTot();
    }

    private function getDatosF_traslado(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_traslado');
        $oDatosCampo->setMetodoGet('getF_traslado');
        $oDatosCampo->setMetodoSet('setF_traslado');
        $oDatosCampo->setEtiqueta(_("fecha traslado"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosTipoCambio(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_cmb');
        $oDatosCampo->setMetodoGet('getTipo_cmb');
        $oDatosCampo->setMetodoSet('setTipo_cmb');
        $oDatosCampo->setEtiqueta(_("tipo de cambio"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    private function getDatosCentroOrigen(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ctr_origen');
        $oDatosCampo->setMetodoGet('getCtr_origen');
        $oDatosCampo->setMetodoSet('setCtr_origen');
        $oDatosCampo->setEtiqueta(_("centro origen"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(35);
        return $oDatosCampo;
    }
    private function getDatosIdCentroOrigen(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ctr_origen');
        $oDatosCampo->setMetodoGet('getId_ctr_origen');
        $oDatosCampo->setMetodoSet('setId_ctr_origen');
        $oDatosCampo->setEtiqueta(_("id centro origen"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }
    private function getDatosCentroDestino(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ctr_destino');
        $oDatosCampo->setMetodoGet('getCtr_destino');
        $oDatosCampo->setMetodoSet('setCtr_destino');
        $oDatosCampo->setEtiqueta(_("centro destino"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(35);
        return $oDatosCampo;
    }
    private function getDatosIdCentroDestino(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ctr_destino');
        $oDatosCampo->setMetodoGet('getId_ctr_destino');
        $oDatosCampo->setMetodoSet('setId_ctr_destino');
        $oDatosCampo->setEtiqueta(_("id centro destino"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
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
        $oDatosCampo->setArgument(70);
        return $oDatosCampo;
    }
}