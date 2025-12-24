<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\FechaJuramento;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad d_profesor_juramento
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorJuramento
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorJuramento
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorJuramento
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * F_juramento de ProfesorJuramento
     *
     * @var DateTimeLocal
     */
    private DateTimeLocal $df_juramento;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorJuramento
     */
    public function setAllAttributes(array $aDatos): ProfesorJuramento
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('f_juramento', $aDatos)) {
            $this->setFechaJuramentoVo(FechaJuramento::fromNullable($aDatos['f_juramento']));
        }
        return $this;
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
     * @return DateTimeLocal|NullDateTimeLocal|null $df_juramento
     * @deprecated Usar getFechaJuramentoVo()->value()
     */
    public function getF_juramento(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_juramento ?? new NullDateTimeLocal;
    }

    /**
     * @param DateTimeLocal|null $df_juramento
     * @deprecated Usar setFechaJuramentoVo(FechaJuramento $vo)
     */
    public function setF_juramento(DateTimeLocal|null $df_juramento = null): void
    {
        $this->df_juramento = $df_juramento;
    }

    public function getFechaJuramentoVo(): ?FechaJuramento
    {
        return FechaJuramento::fromNullable($this->df_juramento);
    }

    public function setFechaJuramentoVo(?FechaJuramento $fecha): void
    {
        $this->df_juramento = $fecha?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
    {
        $oProfesorJuramentoSet = new Set();

        $oProfesorJuramentoSet->add($this->getDatosId_nom());
        $oProfesorJuramentoSet->add($this->getDatosF_juramento());
        return $oProfesorJuramentoSet->getTot();
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

    function getDatosF_juramento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_juramento');
        $oDatosCampo->setMetodoGet('getF_juramento');
        $oDatosCampo->setMetodoSet('setF_juramento');
        $oDatosCampo->setEtiqueta(_("fecha del juramento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}