<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\value_objects\{EquipajeCabecera,
    EquipajeCabecerab,
    EquipajeId,
    EquipajeIdsActiv,
    EquipajeLugar,
    EquipajeNom,
    EquipajePie};
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;


class Equipaje
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private EquipajeId $id_equipaje;

    private ?EquipajeIdsActiv $ids_activ = null;

    private ?EquipajeLugar $lugar = null;

    private ?DateTimeLocal $f_ini = null;

    private ?DateTimeLocal $f_fin = null;

    private ?int $id_ubi_activ = null;

    private ?EquipajeNom $nom_equipaje = null;

    private ?EquipajeCabecera $cabecera = null;

    private ?EquipajePie $pie = null;

    private ?EquipajeCabecerab $cabecerab = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_equipaje(): int
    {
        return $this->id_equipaje->value();
    }


    public function setId_equipaje(int $id_equipaje): void
    {
        $this->id_equipaje = EquipajeId::fromNullableInt($id_equipaje);
    }


    public function getIds_activ(): ?string
    {
        return $this->ids_activ?->value();
    }


    public function setIds_activ(?string $ids_activ = null): void
    {
        $this->ids_activ = EquipajeIdsActiv::fromNullableString($ids_activ);
    }


    public function getLugar(): ?string
    {
        return $this->lugar?->value();
    }


    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = EquipajeLugar::fromNullableString($lugar);
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

    public function setId_ubi_activ(?int $id = null): void
    {
        $this->id_ubi_activ = $id;
    }




    public function getNom_equipaje(): ?string
    {
        return $this->nom_equipaje->value();
    }


    public function setNom_equipaje(?string $nom_equipaje = null): void
    {
        $this->nom_equipaje = EquipajeNom::fromNullableString($nom_equipaje);
    }


    public function getCabecera(): ?string
    {
        return $this->cabecera->value();
    }


    public function setCabecera(?string $cabecera = null): void
    {
        $this->cabecera = EquipajeCabecera::fromNullableString($cabecera);
    }


    public function getPie(): ?string
    {
        return $this->pie->value();
    }


    public function setPie(?string $pie = null): void
    {
        $this->pie = EquipajePie::fromNullableString($pie);
    }


    public function getCabecerab(): ?string
    {
        return $this->cabecerab->value();
    }


    public function setCabecerab(?string $cabecerab = null): void
    {
        $this->cabecerab = EquipajeCabecerab::fromNullableString($cabecerab);
    }

    // Value Object API (duplicada con legacy)
    public function getIdEquipajeVo(): EquipajeId
    {
        return $this->id_equipaje;
    }

    public function setIdEquipajeVo(EquipajeId|int|null $id = null): void
    {
        $this->id_equipaje = $id instanceof EquipajeId
            ? $id
            : EquipajeId::fromNullableInt($id);
    }

    public function getIdsActivVo(): ?EquipajeIdsActiv
    {
        return $this->ids_activ;
    }

    public function setIdsActivVo(EquipajeIdsActiv|string|null $ids = null): void
    {
        $this->ids_activ = $ids instanceof EquipajeIdsActiv
            ? $ids
            : EquipajeIdsActiv::fromNullableString($ids);
    }

    public function getLugarVo(): ?EquipajeLugar
    {
        return $this->lugar;
    }

    public function setLugarVo(EquipajeLugar|string|null $lugar = null): void
    {
        $this->lugar = $lugar instanceof EquipajeLugar
            ? $lugar
            : EquipajeLugar::fromNullableString($lugar);
    }

    public function getNomEquipajeVo(): ?EquipajeNom
    {
        return $this->nom_equipaje;
    }

    public function setNomEquipajeVo(EquipajeNom|string|null $nom = null): void
    {
        $this->nom_equipaje = $nom instanceof EquipajeNom
            ? $nom
            : EquipajeNom::fromNullableString($nom);
    }

    public function getCabeceraVo(): ?EquipajeCabecera
    {
        return $this->cabecera;
    }

    public function setCabeceraVo(EquipajeCabecera|string|null $cabecera = null): void
    {
        $this->cabecera = $cabecera instanceof EquipajeCabecera
            ? $cabecera
            : EquipajeCabecera::fromNullableString($cabecera);
    }

    public function getCabecerabVo(): ?EquipajeCabecerab
    {
        return $this->cabecerab;
    }

    public function setCabecerabVo(EquipajeCabecerab|string|null $cabecerab = null): void
    {
        $this->cabecerab = $cabecerab instanceof EquipajeCabecerab
            ? $cabecerab
            : EquipajeCabecerab::fromNullableString($cabecerab);
    }

    public function getPieVo(): ?EquipajePie
    {
        return $this->pie;
    }

    public function setPieVo(EquipajePie|string|null $pie = null): void
    {
        $this->pie = $pie instanceof EquipajePie
            ? $pie
            : EquipajePie::fromNullableString($pie);
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