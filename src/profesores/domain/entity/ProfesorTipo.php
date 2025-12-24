<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\{ProfesorTipoId, ProfesorTipoName};

/**
 * Clase que implementa la entidad xe_tipo_profesor_stgr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class ProfesorTipo
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id del Tipo de Profesor
     */
    private ProfesorTipoId $idTipoProfesor;
    /**
     * Nombre/Tipo del Profesor
     */
    private ?ProfesorTipoName $tipoProfesor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorTipo
     */
    public function setAllAttributes(array $aDatos): ProfesorTipo
    {
        if (array_key_exists('id_tipo_profesor', $aDatos)) {
            $this->setIdTipoProfesorVo(new ProfesorTipoId((int)$aDatos['id_tipo_profesor']));
        }
        if (array_key_exists('tipo_profesor', $aDatos)) {
            $this->setTipoProfesorVo(ProfesorTipoName::fromNullableString($aDatos['tipo_profesor'] ?? null));
        }
        return $this;
    }

    // -------- VO API --------
    public function getIdTipoProfesorVo(): ProfesorTipoId
    {
        return $this->idTipoProfesor;
    }

    public function setIdTipoProfesorVo(ProfesorTipoId $id): void
    {
        $this->idTipoProfesor = $id;
    }

    public function getTipoProfesorVo(): ?ProfesorTipoName
    {
        return $this->tipoProfesor;
    }

    public function setTipoProfesorVo(?ProfesorTipoName $nombre = null): void
    {
        $this->tipoProfesor = $nombre;
    }

    /**
     *
     * @return int $iid_tipo_profesor
     */
    public function getId_tipo_profesor(): int
    {
        return $this->idTipoProfesor->value();
    }

    /**
     *
     * @param int $iid_tipo_profesor
     */
    public function setId_tipo_profesor(int $iid_tipo_profesor): void
    {
        $this->idTipoProfesor = new ProfesorTipoId($iid_tipo_profesor);
    }

    /**
     *
     * @return string|null $stipo_profesor
     */
    public function getTipo_profesor(): ?string
    {
        return $this->tipoProfesor?->value();
    }

    /**
     *
     * @param string|null $stipo_profesor
     */
    public function setTipo_profesor(?string $stipo_profesor = null): void
    {
        $this->tipoProfesor = ProfesorTipoName::fromNullableString($stipo_profesor);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_profesor';
    }

    function getDatosCampos(): array
    {
        $oTipoCentroSet = new Set();

        $oTipoCentroSet->add($this->getDatosId_nom());
        $oTipoCentroSet->add($this->getDatosTipo_profesor());
        return $oTipoCentroSet->getTot();
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

    function getDatosTipo_profesor()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_profesor');
        $oDatosCampo->setMetodoGet('getTipo_profesor');
        $oDatosCampo->setMetodoSet('setTipo_profesor');
        $oDatosCampo->setEtiqueta(_("tipo de profesor"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}