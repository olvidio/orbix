<?php

namespace src\asignaturas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\value_objects\{DepartamentoId, DepartamentoName};

/**
 * Clase que implementa la entidad xe_departamentos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class Departamento
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id del Departamento
     */
    private DepartamentoId $idDepartamento;
    /**
     * Nombre del Departamento
     */
    private DepartamentoName $nombreDepartamento;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Departamento
     */
    public function setAllAttributes(array $aDatos): Departamento
    {
        if (array_key_exists('id_departamento', $aDatos)) {
            $this->setIdDepartamentoVo(isset($aDatos['id_departamento']) ? new DepartamentoId((int)$aDatos['id_departamento']) : null);
        }
        if (array_key_exists('departamento', $aDatos)) {
            $this->setNombreDepartamentoVo(DepartamentoName::fromNullableString($aDatos['departamento'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getIdDepartamentoVo(): DepartamentoId
    {
        return $this->idDepartamento;
    }

    public function setIdDepartamentoVo(DepartamentoId $id): void
    {
        $this->idDepartamento = $id;
    }

    /**
     *
     * @return int $iid_departamento
     */
    public function getId_departamento(): int
    {
        return $this->idDepartamento?->value() ?? 0;
    }

    /**
     *
     * @param int $iid_departamento
     */
    public function setId_departamento(int $iid_departamento): void
    {
        $this->idDepartamento = new DepartamentoId($iid_departamento);
    }

    // VO API
    public function getNombreDepartamentoVo(): DepartamentoName
    {
        return $this->nombreDepartamento;
    }

    public function setNombreDepartamentoVo(DepartamentoName $nombre): void
    {
        $this->nombreDepartamento = $nombre;
    }

    /**
     *
     * @return string $sdepartamento
     */
    public function getDepartamento(): string
    {
        return $this->nombreDepartamento?->value() ?? '';
    }

    /**
     *
     * @param string $sdepartamento
     */
    public function setDepartamento(string $sdepartamento): void
    {
        $this->nombreDepartamento = DepartamentoName::fromString($sdepartamento);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_departamento';
    }

    function getDatosCampos()
    {
        $oDepartamentoSet = new Set();

        $oDepartamentoSet->add($this->getDatosDepartamento());
        return $oDepartamentoSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sdepartamento de Departamento
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDepartamento()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('departamento');
        $oDatosCampo->setMetodoGet('getDepartamento');
        $oDatosCampo->setMetodoSet('setDepartamento');;
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }
}