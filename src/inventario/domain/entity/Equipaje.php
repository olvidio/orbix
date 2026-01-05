<?php

namespace src\inventario\domain\entity;
use src\inventario\domain\value_objects\{EquipajeId, UbiInventarioIdActiv,
    EquipajeIdsActiv, EquipajeLugar, EquipajeNom, EquipajeCabecera, EquipajeCabecerab, EquipajePie};

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;


class Equipaje
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_equipaje;

    private string|null $ids_activ = null;

    private string|null $lugar = null;

    private DateTimeLocal|null $f_ini = null;

    private DateTimeLocal|null $f_fin = null;

    private int|null $id_ubi_activ = null;

    private string|null $nom_equipaje = null;

    private string|null $cabecera = null;

    private string|null $pie = null;

    private string|null $cabecerab = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_equipaje(): int
    {
        return $this->id_equipaje;
    }


    public function setId_equipaje(int $id_equipaje): void
    {
        $this->id_equipaje = $id_equipaje;
    }


    public function getIds_activ(): ?string
    {
        return $this->ids_activ;
    }


    public function setIds_activ(?string $ids_activ = null): void
    {
        $this->ids_activ = $ids_activ;
    }


    public function getLugar(): ?string
    {
        return $this->lugar;
    }


    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = $lugar;
    }

    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }

    public function setF_ini(DateTimeLocal|NullDateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini instanceof NullDateTimeLocal ? null : $f_ini;
    }


    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin ?? new NullDateTimeLocal;
    }


    public function setF_fin(DateTimeLocal|NullDateTimeLocal|null $f_fin = null): void
    {
        $this->f_fin = $f_fin instanceof NullDateTimeLocal ? null : $f_fin;
    }


    public function getId_ubi_activ(): ?int
    {
        return $this->id_ubi_activ;
    }


    public function setId_ubi_activ(?int $id_ubi_activ = null): void
    {
        $this->id_ubi_activ = $id_ubi_activ;
    }


    public function getNom_equipaje(): ?string
    {
        return $this->nom_equipaje;
    }


    public function setNom_equipaje(?string $nom_equipaje = null): void
    {
        $this->nom_equipaje = $nom_equipaje;
    }


    public function getCabecera(): ?string
    {
        return $this->cabecera;
    }


    public function setCabecera(?string $cabecera = null): void
    {
        $this->cabecera = $cabecera;
    }


    public function getPie(): ?string
    {
        return $this->pie;
    }


    public function setPie(?string $pie = null): void
    {
        $this->pie = $pie;
    }


    public function getCabecerab(): ?string
    {
        return $this->cabecerab;
    }


    public function setCabecerab(?string $cabecerab = null): void
    {
        $this->cabecerab = $cabecerab;
    }

    // Value Object API (duplicada con legacy)
    public function getIdEquipajeVo(): EquipajeId
    {
        return new EquipajeId($this->id_equipaje);
    }

    public function setIdEquipajeVo(?EquipajeId $id = null): void
    {
        if ($id === null) { return; }
        $this->id_equipaje = $id->value();
    }

    public function getIdsActivVo(): ?EquipajeIdsActiv
    {
        return EquipajeIdsActiv::fromNullableString($this->ids_activ);
    }

    public function setIdsActivVo(?EquipajeIdsActiv $ids = null): void
    {
        $this->ids_activ = $ids?->value();
    }

    public function getLugarVo(): ?EquipajeLugar
    {
        return EquipajeLugar::fromNullableString($this->lugar);
    }

    public function setLugarVo(?EquipajeLugar $lugar = null): void
    {
        $this->lugar = $lugar?->value();
    }

    public function getIdUbiActivVo(): ?UbiInventarioIdActiv
    {
        return $this->id_ubi_activ !== null ? new UbiInventarioIdActiv($this->id_ubi_activ) : null;
    }

    public function setIdUbiActivVo(?UbiInventarioIdActiv $id = null): void
    {
        $this->id_ubi_activ = $id?->value();
    }

    public function getNomEquipajeVo(): ?EquipajeNom
    {
        return EquipajeNom::fromNullableString($this->nom_equipaje);
    }

    public function setNomEquipajeVo(?EquipajeNom $nom = null): void
    {
        $this->nom_equipaje = $nom?->value();
    }

    public function getCabeceraVo(): ?EquipajeCabecera
    {
        return EquipajeCabecera::fromNullableString($this->cabecera);
    }

    public function setCabeceraVo(?EquipajeCabecera $cabecera = null): void
    {
        $this->cabecera = $cabecera?->value();
    }

    public function getCabecerabVo(): ?EquipajeCabecerab
    {
        return EquipajeCabecerab::fromNullableString($this->cabecerab);
    }

    public function setCabecerabVo(?EquipajeCabecerab $cabecerab = null): void
    {
        $this->cabecerab = $cabecerab?->value();
    }

    public function getPieVo(): ?EquipajePie
    {
        return EquipajePie::fromNullableString($this->pie);
    }

    public function setPieVo(?EquipajePie $pie = null): void
    {
        $this->pie = $pie?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_equipaje';
    }

    public function getDatosCampos(): array
    {
        $oEquipajeSet = new Set();

        $oEquipajeSet->add($this->getDatosIds_activ());
        $oEquipajeSet->add($this->getDatosLugar());
        $oEquipajeSet->add($this->getDatosF_ini());
        $oEquipajeSet->add($this->getDatosF_fin());
        $oEquipajeSet->add($this->getDatosId_ubi_activ());
        $oEquipajeSet->add($this->getDatosNom_equipaje());
        $oEquipajeSet->add($this->getDatosCabecera());
        $oEquipajeSet->add($this->getDatosCabeceraB());
        $oEquipajeSet->add($this->getDatosPie());
        return $oEquipajeSet->getTot();
    }

    private function getDatosIds_activ(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ids_activ');
        $oDatosCampo->setMetodoGet('getIds_activ');
        $oDatosCampo->setMetodoSet('setIds_activ');
        $oDatosCampo->setEtiqueta(_("ids_activ"));
        return $oDatosCampo;
    }

    private function getDatosLugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('lugar');
        $oDatosCampo->setMetodoGet('getLugar');
        $oDatosCampo->setMetodoSet('setLugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        return $oDatosCampo;
    }

    private function getDatosF_ini(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ini');
        $oDatosCampo->setMetodoGet('getF_ini');
        $oDatosCampo->setMetodoSet('setF_ini');
        $oDatosCampo->setEtiqueta(_("f_ini"));
        return $oDatosCampo;
    }

    private function getDatosF_fin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_fin');
        $oDatosCampo->setMetodoGet('getF_fin');
        $oDatosCampo->setMetodoSet('setF_fin');
        $oDatosCampo->setEtiqueta(_("f_fin"));
        return $oDatosCampo;
    }

    private function getDatosId_ubi_activ(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi_activ');
        $oDatosCampo->setMetodoGet('getId_ubi_activ');
        $oDatosCampo->setMetodoSet('setId_ubi_activ');
        $oDatosCampo->setEtiqueta(_("id_ubi_activ"));
        return $oDatosCampo;
    }

    private function getDatosNom_equipaje(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_equipaje');
        $oDatosCampo->setMetodoGet('getNom_equipaje');
        $oDatosCampo->setMetodoSet('setNom_equipaje');
        $oDatosCampo->setEtiqueta(_("nom_equipaje"));
        return $oDatosCampo;
    }

    private function getDatosCabecera(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cabecera');
        $oDatosCampo->setMetodoGet('getCabecera');
        $oDatosCampo->setMetodoSet('setCabecera');
        $oDatosCampo->setEtiqueta(_("cabecera"));
        return $oDatosCampo;
    }

    private function getDatosCabeceraB(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cabecerab');
        $oDatosCampo->setMetodoGet('getCabecerab');
        $oDatosCampo->setMetodoSet('setCabecerab');
        $oDatosCampo->setEtiqueta(_("cabecera B"));
        return $oDatosCampo;
    }

    private function getDatosPie(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('pie');
        $oDatosCampo->setMetodoGet('getPie');
        $oDatosCampo->setMetodoSet('setPie');
        $oDatosCampo->setEtiqueta(_("pie"));
        return $oDatosCampo;
    }

}