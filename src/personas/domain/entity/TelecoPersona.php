<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\value_objects\{NumTelecoText, ObservTelecoText, TipoTelecoCode};

/**
 * Clase que implementa la entidad d_teleco_personas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
class TelecoPersona
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_nom de TelecoPersona
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Id_item de TelecoPersona
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Tipo_teleco de TelecoPersona
     *
     * @var int
     */
    private int $id_tipo_teleco;
    /**
     * Num_teleco de TelecoPersona
     *
     * @var string
     */
    private string $snum_teleco;
    /**
     * Observ de TelecoPersona
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * id_desc_teleco de TelecoPersona
     *
     * @var int|null
     */
    private int|null $id_desc_teleco = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TelecoPersona
     */
    public function setAllAttributes(array $aDatos): TelecoPersona
    {
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom((int)$aDatos['id_nom']);
        }
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item((int)$aDatos['id_item']);
        }
        if (array_key_exists('id_tipo_teleco', $aDatos)) {
            $valor = $aDatos['id_tipo_teleco'];
            if ($valor instanceof TipoTelecoCode) {
                $this->setTipoTelecoVo($valor);
            } else {
                $this->setId_tipo_teleco((string)$valor);
            }
        }
        if (array_key_exists('num_teleco', $aDatos)) {
            $valor = $aDatos['num_teleco'];
            if ($valor instanceof NumTelecoText) {
                $this->setNumTelecoVo($valor);
            } else {
                $this->setNum_teleco((string)$valor);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $valor = $aDatos['observ'];
            if ($valor instanceof ObservTelecoText || $valor === null) {
                $this->setObservVo($valor);
            } else {
                $this->setObserv($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('id_desc_teleco', $aDatos)) {
            // En personas, id_desc_teleco es un id (int|null). Mantener como tal.
            $this->setId_desc_teleco($aDatos['id_desc_teleco'] !== null ? (int)$aDatos['id_desc_teleco'] : null);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    /**
     *
     * @return int $stipo_teleco
     */
    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco;
    }

    /**
     *
     * @param int $id_tipo_teleco
     */
    public function setId_tipo_teleco(int $itipo_teleco): void
    {
        $this->id_tipo_teleco = $itipo_teleco;
    }

    /**
     * API VO para id_tipo_teleco (código): TipoTelecoCode
     */
    public function getTipoTelecoVo(): TipoTelecoCode
    {
        return new TipoTelecoCode($this->id_tipo_teleco);
    }

    public function setTipoTelecoVo(TipoTelecoCode $code): void
    {
        $this->id_tipo_teleco = $code->value();
    }

    /**
     *
     * @return string $snum_teleco
     */
    public function getNum_teleco(): string
    {
        return $this->snum_teleco;
    }

    /**
     *
     * @param string $snum_teleco
     */
    public function setNum_teleco(string $snum_teleco): void
    {
        $this->snum_teleco = $snum_teleco;
    }

    public function getNumTelecoVo(): NumTelecoText
    {
        return new NumTelecoText($this->snum_teleco);
    }

    public function setNumTelecoVo(NumTelecoText $texto): void
    {
        $this->snum_teleco = $texto->value();
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    public function getObservVo(): ?ObservTelecoText
    {
        return ObservTelecoText::fromNullableString($this->sobserv);
    }

    public function setObservVo(?ObservTelecoText $texto = null): void
    {
        $this->sobserv = $texto?->value();
    }

    /**
     *
     * @return int|null $idesc_teleco
     */
    public function getId_desc_teleco(): ?int
    {
        return $this->id_desc_teleco;
    }

    /**
     *
     * @param int|null $id_desc_teleco
     */
    public function setId_desc_teleco(?int $id_desc_teleco = null): void
    {
        $this->id_desc_teleco = $id_desc_teleco;
    }

    // Nota: En TelecoPersona, id_desc_teleco es un id (int|null). Si en UI se maneja como texto,
    // la conversión a texto debe realizarse en la capa repositorio de descripciones.

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }

    public function getDatosCampos()
    {
        $oSet = new Set();
        $oSet->add($this->getDatosId_nom());
        $oSet->add($this->getDatosId_tipo_teleco());
        $oSet->add($this->getDatosId_desc_teleco());
        $oSet->add($this->getDatosNum_teleco());
        $oSet->add($this->getDatosObserv());
        return $oSet->getTot();
    }

    function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    public function getDatosId_tipo_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getId_tipo_teleco');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoTelecoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNombreTelecoVo');
        $oDatosCampo->setArgument3('getArrayTiposTelecoPersona');
        $oDatosCampo->setAccion('id_desc_teleco');
        return $oDatosCampo;
    }

    public function getDatosId_desc_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_desc_teleco');
        $oDatosCampo->setMetodoGet('getId_desc_teleco');
        $oDatosCampo->setMetodoSet('setId_desc_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument(DescTelecoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getDescTelecoVo');
        $oDatosCampo->setArgument3('getArrayDescTelecoPersonas');
        $oDatosCampo->setDepende('id_tipo_teleco');
        return $oDatosCampo;
    }

    public function getDatosNum_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_teleco');
        $oDatosCampo->setMetodoGet('getNum_teleco');
        $oDatosCampo->setMetodoSet('setNum_teleco');
        $oDatosCampo->setEtiqueta(_("número o siglas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    public function getDatosObserv()
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