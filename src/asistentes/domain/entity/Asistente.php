<?php

namespace src\asistentes\domain\entity;

use core\ConfigGlobal;
use src\actividadplazas\domain\ResumenPlazas;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\value_objects\AsistenteEncargo;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\value_objects\AsistenteObservEst;
use src\asistentes\domain\value_objects\AsistentePropietario;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\shared\domain\contracts\AggregateRoot;
use src\shared\domain\entity\Entity;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\DelegacionCode;


class Asistente extends Entity implements AggregateRoot
{
    use Hydratable;

    /**
     * Saber si puedo modificar.
     * - true para asistentes de mi dl, y para los de paso que he puesto yo
     * - false para asistentes de otra dl, y para los de paso que NO he puesto yo
     *
     * @return boolean
     */
    public function perm_modificar()
    {
        return $this->getDl_responsable() === ConfigGlobal::mi_delef();
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_activ;

    private int $id_nom;

    private bool $propio;

    private bool $est_ok;

    private bool $cfi;

    private int|null $cfi_con = null;

    private bool $falta;

    private string|null $encargo = null;

    private string|null $dl_responsable = null;

    private string|null $observ = null;

    private string|null $id_tabla = null;

    private int|null $plaza = null;

    private string|null $propietario = null;

    private string|null $observ_est = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function isPropio(): bool
    {
        return $this->propio;
    }


    public function setPropio(bool $propio): void
    {
        $this->propio = $propio;
    }


    public function isEst_ok(): bool
    {
        return $this->est_ok;
    }


    public function setEst_ok(bool $est_ok): void
    {
        $this->est_ok = $est_ok;
    }


    public function isCfi(): bool
    {
        return $this->cfi;
    }


    public function setCfi(bool $cfi): void
    {
        $this->cfi = $cfi;
    }


    public function getCfi_con(): ?int
    {
        return $this->cfi_con;
    }


    public function setCfi_con(?int $cfi_con = null): void
    {
        $this->cfi_con = $cfi_con;
    }


    public function isFalta(): bool
    {
        return $this->falta;
    }


    public function setFalta(bool $falta): void
    {
        $this->falta = $falta;
    }

    /**
     * @deprecated usar getEncargoVo()
     */
    public function getEncargo(): ?string
    {
        return $this->encargo;
    }

    /**
     * @deprecated usar setEncargoVo()
     */
    public function setEncargo(?string $encargo = null): void
    {
        $this->encargo = $encargo;
    }

    /**
     * @return AsistenteEncargo|null
     */
    public function getEncargoVo(): ?AsistenteEncargo
    {
        return AsistenteEncargo::fromNullableString($this->encargo);
    }

    /**
     * @param AsistenteEncargo|null $oAsistenteEncargo
     */
    public function setEncargoVo(?AsistenteEncargo $oAsistenteEncargo = null): void
    {
        $this->encargo = $oAsistenteEncargo?->value();
    }

    /**
     * @deprecated usar getDlResponsableVo()
     */
    public function getDl_responsable(): ?string
    {
        return $this->dl_responsable;
    }

    /**
     * @deprecated usar setDlResponsableVo()
     */
    public function setDl_responsable(?string $dl_responsable = null): void
    {
        $this->dl_responsable = $dl_responsable;
    }

    /**
     * @return DelegacionCode|null
     */
    public function getDlResponsableVo(): ?DelegacionCode
    {
        return DelegacionCode::fromString($this->dl_responsable);
    }

    /**
     * @param DelegacionCode|null $oDelegacionCode
     */
    public function setDlResponsableVo(?DelegacionCode $oDelegacionCode = null): void
    {
        $this->dl_responsable = $oDelegacionCode?->value();
    }

    /**
     * @deprecated usar getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated usar setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    /**
     * @return AsistenteObserv|null
     */
    public function getObservVo(): ?AsistenteObserv
    {
        return AsistenteObserv::fromNullableString($this->observ);
    }

    /**
     * @param AsistenteObserv|null $oAsistenteObserv
     */
    public function setObservVo(?AsistenteObserv $oAsistenteObserv = null): void
    {
        $this->observ = $oAsistenteObserv?->value();
    }

    /**
     * @deprecated usar getIdTablaVo()
     */
    public function getId_tabla(): ?string
    {
        return $this->id_tabla;
    }

    /**
     * @deprecated usar setIdTablaVo()
     */
    public function setId_tabla(?string $id_tabla = null): void
    {
        $this->id_tabla = $id_tabla;
    }

    /**
     * @return PersonaTablaCode|null
     */
    public function getIdTablaVo(): ?PersonaTablaCode
    {
        return PersonaTablaCode::fromNullableString($this->id_tabla);
    }

    /**
     * @param PersonaTablaCode|null $oPersonaTablaCode
     */
    public function setIdTablaVo(?PersonaTablaCode $oPersonaTablaCode = null): void
    {
        $this->id_tabla = $oPersonaTablaCode?->value();
    }

    /**
     * @deprecated usar getPlazaVo()
     */
    public function getPlaza(): ?int
    {
        return $this->plaza;
    }

    /**
     * @deprecated usar setPlazaVo()
     */
    public function setPlaza(?int $plaza = null): void
    {
        $this->plaza = $plaza;
    }

    /**
     * @return PlazaId|null
     */
    public function getPlazaVo(): ?PlazaId
    {
        return $this->plaza !== null ? new PlazaId($this->plaza) : null;
    }

    /**
     * @param PlazaId|null $oPlazaId
     */
    public function setPlazaVo(?PlazaId $oPlazaId = null): void
    {
        $this->plaza = $oPlazaId?->value();
    }

    /**
     * No puede estar en setPlaza, porque cuando se hidrata con la DB entra en un bucle infinito
     * @deprecated usar setPlazaVoComprobando()
     */
    public function setPlazaComprobando(?int $plaza = null): void
    {
        // tipos de actividad para los que no hay que comprobar la plaza
        // 132500 => agd ca sem invierno
        //$aId_tipo_activ_no = [132500,00000];
        //$oActividad = new Actividad($this->iid_activ);
        //$id_tipo_activ = $oActividad->getId_tipo_activ();
        //if (in_array($id_tipo_activ, $aId_tipo_activ_no)) {
        //	return $this->setPlazaSinComprobar($iplaza);
        //}

        //hacer comprobaciones de plazas disponibles...
        $plaza_actual = $this->getPlaza();

        if ($plaza_actual < PlazaId::DENEGADA && $plaza > PlazaId::DENEGADA) {
            $this->plaza = $plaza;
            $gesActividadPlazasR = new ResumenPlazas();
            $gesActividadPlazasR->setId_activ($this->id_activ);
            if ($gesActividadPlazasR->getLibres() > 0) {
                //debe asignarse un propietario. Sólo si es asignada o confirmada
                $rta = $gesActividadPlazasR->getPropiedadPlazaLibre();
                if ($rta['success']) {
                    $propiedad = $rta['propiedad'];
                    if (empty($propiedad)) {
                        exit (_("no debería pasar. No puede haber una plaza libre sin propietario"));
                    } else {
                        $prop = key($propiedad);
                        $this->setPropietario($prop);
                    }
                } else {
                    $err_txt = $rta['mensaje'];
                    exit ($err_txt);
                }
            } else {
                $this->plaza = PlazaId::PEDIDA;
            }
        } else {
            $this->plaza = $plaza;
        }
    }

    /**
     * No puede estar en setPlaza, porque cuando se hidrata con la DB entra en un bucle infinito
     * @param PlazaId|null $oPlazaId
     */
    public function setPlazaVoComprobando(?PlazaId $oPlazaId = null): void
    {
        $iplaza = $oPlazaId?->value();

        //hacer comprobaciones de plazas disponibles...
        $plaza_actual = $this->getPlaza();

        if ($plaza_actual < PlazaId::DENEGADA && $iplaza > PlazaId::DENEGADA) {
            $this->plaza = $iplaza;
            $gesActividadPlazasR = new ResumenPlazas();
            $gesActividadPlazasR->setId_activ($this->id_activ);
            if ($gesActividadPlazasR->getLibres() > 0) {
                //debe asignarse un propietario. Sólo si es asignada o confirmada
                $rta = $gesActividadPlazasR->getPropiedadPlazaLibre();
                if ($rta['success']) {
                    $propiedad = $rta['propiedad'];
                    if (empty($propiedad)) {
                        exit (_("no debería pasar. No puede haber una plaza libre sin propietario"));
                    } else {
                        $prop = key($propiedad);
                        $this->setPropietario($prop);
                    }
                } else {
                    $err_txt = $rta['mensaje'];
                    exit ($err_txt);
                }
            } else {
                $this->plaza = PlazaId::PEDIDA;
            }
        } else {
            $this->plaza = $iplaza;
        }
    }

    /**
     * @deprecated usar getPropietarioVo()
     */
    public function getPropietario(): ?string
    {
        return $this->propietario;
    }

    /**
     * @deprecated usar setPropietarioVo()
     */
    public function setPropietario(?string $propietario = null): void
    {
        $this->propietario = $propietario;
    }

    /**
     * @return AsistentePropietario|null
     */
    public function getPropietarioVo(): ?AsistentePropietario
    {
        return AsistentePropietario::fromNullableString($this->propietario);
    }

    /**
     * @param AsistentePropietario|null $oAsistentePropietario
     */
    public function setPropietarioVo(?AsistentePropietario $oAsistentePropietario = null): void
    {
        $this->propietario = $oAsistentePropietario?->value();
    }

    /**
     * @deprecated usar getObservEstVo()
     */
    public function getObserv_est(): ?string
    {
        return $this->observ_est;
    }

    /**
     * @deprecated usar setObservEstVo()
     */
    public function setObserv_est(?string $observ_est = null): void
    {
        $this->observ_est = $observ_est;
    }

    /**
     * @return AsistenteObservEst|null
     */
    public function getObservEstVo(): ?AsistenteObservEst
    {
        return AsistenteObservEst::fromNullableString($this->observ_est);
    }

    /**
     * @param AsistenteObservEst|null $oAsistenteObservEst
     */
    public function setObservEstVo(?AsistenteObservEst $oAsistenteObservEst = null): void
    {
        $this->observ_est = $oAsistenteObservEst?->value();
    }
}