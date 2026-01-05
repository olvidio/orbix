<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\ubis\domain\value_objects\{TipoTelecoCode, TipoTelecoName};

class TipoTeleco
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ?TipoTelecoCode $tipo_teleco = null;

    private ?TipoTelecoName $nombre_teleco = null;

    private bool|null $ubi = null;

    private bool|null $persona = null;

    private int $id;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // -------- VO API --------
    public function getTipoTelecoVo(): ?TipoTelecoCode
    {
        return $this->tipo_teleco;
    }

    public function setTipoTelecoVo(?TipoTelecoCode $codigo = null): void
    {
        $this->tipo_teleco = $codigo;
    }

    public function getNombreTelecoVo(): ?TipoTelecoName
    {
        return $this->nombre_teleco;
    }

    public function setNombreTelecoVo(?TipoTelecoName $nombre = null): void
    {
        $this->nombre_teleco = $nombre;
    }


    /**
     * @deprecated Usar `getTipoTelecoVo(): ?TipoTelecoCode` en su lugar.
     */
    public function getTipo_teleco(): string
    {
        return $this->tipo_teleco?->value();
    }


    /**
     * @deprecated Usar `setTipoTelecoVo(?TipoTelecoCode $codigo): void` en su lugar.
     */
    public function setTipo_teleco(string $tipo_teleco): void
    {
        $tipo_teleco = trim($tipo_teleco);
        $this->tipo_teleco = $tipo_teleco !== '' ? new TipoTelecoCode($tipo_teleco) : null;
    }


    /**
     * @deprecated Usar `getNombreTelecoVo(): ?TipoTelecoName` en su lugar.
     */
    public function getNombre_teleco(): ?string
    {
        return $this->nombre_teleco?->value();
    }


    /**
     * @deprecated Usar `setNombreTelecoVo(?TipoTelecoName $nombre): void` en su lugar.
     */
    public function setNombre_teleco(?string $nombre_teleco = null): void
    {
        $this->nombre_teleco = TipoTelecoName::fromNullableString($nombre_teleco);
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


    public function getId(): int
    {
        return $this->id;
    }


    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id';
    }

    public function getDatosCampos(): array
    {
        $oAsignaturaSet = new Set();

        $oAsignaturaSet->add($this->getDatosTipo_teleco());
        $oAsignaturaSet->add($this->getDatosNombre_teleco());
        $oAsignaturaSet->add($this->getDatosUbi());
        $oAsignaturaSet->add($this->getDatosPersona());
        return $oAsignaturaSet->getTot();
    }

    private function getDatosTipo_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_teleco');
        $oDatosCampo->setMetodoGet('getTipo_teleco');
        $oDatosCampo->setMetodoSet('setTipo_teleco');
        $oDatosCampo->setEtiqueta(_("tipo teleco"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    private function getDatosNombre_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_teleco');
        $oDatosCampo->setMetodoGet('getNombre_teleco');
        $oDatosCampo->setMetodoSet('setNombre_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
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