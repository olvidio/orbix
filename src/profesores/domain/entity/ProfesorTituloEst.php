<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CentroDntName;
use src\profesores\domain\value_objects\TituloName;
use src\profesores\domain\value_objects\YearNumber;
use function core\is_true;

/**
 * Clase que implementa la entidad d_titulo_est
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorTituloEst
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorTituloEst
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorTituloEst
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Titulo de ProfesorTituloEst
     *
     * @var string
     */
    private string $stitulo;
    /**
     * Centro_dnt de ProfesorTituloEst
     *
     * @var string|null
     */
    private string|null $scentro_dnt = null;
    /**
     * Eclesiastico de ProfesorTituloEst
     *
     * @var bool|null
     */
    private bool|null $beclesiastico = null;
    /**
     * Year de ProfesorTituloEst
     *
     * @var int|null
     */
    private int|null $iyear = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorTituloEst
     */
    public function setAllAttributes(array $aDatos): ProfesorTituloEst
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('titulo', $aDatos)) {
            $this->setTituloVo(TituloName::fromNullable($aDatos['titulo']));
        }
        if (array_key_exists('centro_dnt', $aDatos)) {
            $this->setCentroDntVo(CentroDntName::fromNullable($aDatos['centro_dnt']));
        }
        if (array_key_exists('eclesiastico', $aDatos)) {
            $this->setEclesiastico(is_true($aDatos['eclesiastico']));
        }
        if (array_key_exists('year', $aDatos)) {
            $this->setYearVo(YearNumber::fromNullable($aDatos['year']));
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
     * @return string $stitulo
     * @deprecated Usar getTituloVo()->value()
     */
    public function getTitulo(): string
    {
        return $this->stitulo;
    }

    /**
     * @param string $stitulo
     * @deprecated Usar setTituloVo(TituloName $vo)
     */
    public function setTitulo(string $stitulo): void
    {
        $this->stitulo = $stitulo;
    }

    public function getTituloVo(): TituloName
    {
        return new TituloName($this->stitulo);
    }

    public function setTituloVo(?TituloName $titulo): void
    {
        if ($titulo !== null) {
            $this->stitulo = $titulo->value();
        }
    }

    /**
     * @return string|null $scentro_dnt
     * @deprecated Usar getCentroDntVo()->value()
     */
    public function getCentro_dnt(): ?string
    {
        return $this->scentro_dnt;
    }

    /**
     * @param string|null $scentro_dnt
     * @deprecated Usar setCentroDntVo(CentroDntName $vo)
     */
    public function setCentro_dnt(?string $scentro_dnt = null): void
    {
        $this->scentro_dnt = $scentro_dnt;
    }

    public function getCentroDntVo(): ?CentroDntName
    {
        return CentroDntName::fromNullable($this->scentro_dnt);
    }

    public function setCentroDntVo(?CentroDntName $centro): void
    {
        $this->scentro_dnt = $centro?->value();
    }

    /**
     *
     * @return bool|null $beclesiastico
     */
    public function isEclesiastico(): ?bool
    {
        return $this->beclesiastico;
    }

    /**
     *
     * @param bool|null $beclesiastico
     */
    public function setEclesiastico(?bool $beclesiastico = null): void
    {
        $this->beclesiastico = $beclesiastico;
    }

    /**
     * @return int|null $iyear
     * @deprecated Usar getYearVo()->value()
     */
    public function getYear(): ?int
    {
        return $this->iyear;
    }

    /**
     * @param int|null $iyear
     * @deprecated Usar setYearVo(YearNumber $vo)
     */
    public function setYear(?int $iyear = null): void
    {
        $this->iyear = $iyear;
    }

    public function getYearVo(): ?YearNumber
    {
        return YearNumber::fromNullable($this->iyear);
    }

    public function setYearVo(?YearNumber $year): void
    {
        $this->iyear = $year?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
    {
        $oProfesorTituloEstSet = new Set();

        $oProfesorTituloEstSet->add($this->getDatosId_nom());
        $oProfesorTituloEstSet->add($this->getDatosTitulo());
        $oProfesorTituloEstSet->add($this->getDatosCentro_dnt());
        $oProfesorTituloEstSet->add($this->getDatosEclesiastico());
        $oProfesorTituloEstSet->add($this->getDatosYear());
        return $oProfesorTituloEstSet->getTot();
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

    function getDatosTitulo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('titulo');
        $oDatosCampo->setMetodoGet('getTitulo');
        $oDatosCampo->setMetodoSet('setTitulo');
        $oDatosCampo->setEtiqueta(_("título"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    function getDatosCentro_dnt(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('centro_dnt');
        $oDatosCampo->setMetodoGet('getCentro_dnt');
        $oDatosCampo->setMetodoSet('setCentro_dnt');
        $oDatosCampo->setEtiqueta(_("centro docente"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    function getDatosEclesiastico(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('eclesiastico');
        $oDatosCampo->setMetodoGet('isEclesiastico');
        $oDatosCampo->setMetodoSet('setEclesiastico');
        $oDatosCampo->setEtiqueta(_("eclesiástico"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosYear(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('year');
        $oDatosCampo->setMetodoGet('getYear');
        $oDatosCampo->setMetodoSet('setYear');
        $oDatosCampo->setEtiqueta(_("año"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }
}