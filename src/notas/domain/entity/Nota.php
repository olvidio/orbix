<?php

namespace src\notas\domain\entity;

use function core\is_true;

/**
 * Clase que implementa la entidad e_notas_situacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class Nota
{
    // Al final de hecho deberían ser todo constantes, porque en demasiados sitios se tiene en
    // Cuenta que es.
    /*
    comun=# select * from e_notas_situacion order by id_situacion;
    id_situacion |   descripcion   | superada | breve
    --------------+-----------------+----------+-------
    0 | desconocido     | f        | ?
    1 | superada        | t        | s
    2 | cursada         | f        | c
    3 | Magna cum laude | t        | M
    4 | Summa cum laude | t        | S
    5 | convalidada     | t        | x
    6 | prevista ca     | f        | p
    7 | prevista inv    | f        | p
    8 | no hecha ca     | f        | n
    9 | no hecha inv    | f        | n
    10 | nota numérica   | t        | nm
    11 | Exento          | t        | e
    12 | examinado       | f        | ex
    13 | falta certificado | f      | fc
    */

    // tipo constantes.
    const DESCONOCIDO = 0;
    const SUPERADA = 1;
    const CURSADA = 2;
    const MAGNA = 3;
    const SUMMA = 4;
    const CONVALIDADA = 5;
    const PREVISTA_CA = 6;
    const PREVISTA_INV = 7;
    const NO_HECHA_CA = 8;
    const NO_HECHA_INV = 9;
    const NUMERICA = 10;
    const EXENTO = 11;
    const EXAMINADO = 12;
    const FALTA_CERTIFICADO = 13;
    //
    // Para que la variable stgr_posibles coja las traducciones, hay
    // que ejecutar la funcion 'traduccion_init()'. Cosa que se hace justo
    // al final de la definicion de la clase: Nota::traduccion_init();
    static $array_status_txt;

    static function traduccion_init()
    {
        self::$array_status_txt = [
            self::DESCONOCIDO => _("desconocido"),
            self::SUPERADA => _("superada"),
            self::CURSADA => _("cursada"),
            self::MAGNA => _("Magna cum laude"),
            self::SUMMA => _("Summa cum laude"),
            self::CONVALIDADA => _("convalidada"),
            self::PREVISTA_CA => _("prevista ca"),
            self::PREVISTA_INV => _("prevista inv"),
            self::NO_HECHA_CA => _("no hecha ca"),
            self::NO_HECHA_INV => _("no hecha inv"),
            self::NUMERICA => _("nota numérica"),
            self::EXENTO => _("Exento"),
            self::EXAMINADO => _("examinado"),
            self::FALTA_CERTIFICADO => _("falta certificado"),
        ];
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_situacion de Nota
     *
     * @var int
     */
    private int $iid_situacion;
    /**
     * Descripcion de Nota
     *
     * @var string
     */
    private string $sdescripcion;
    /**
     * Superada de Nota
     *
     * @var bool
     */
    private bool $bsuperada;
    /**
     * Breve de Nota
     *
     * @var string|null
     */
    private string|null $sbreve = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Nota
     */
    public function setAllAttributes(array $aDatos): Nota
    {
        if (array_key_exists('id_situacion', $aDatos)) {
            $this->setId_situacion($aDatos['id_situacion']);
        }
        if (array_key_exists('descripcion', $aDatos)) {
            $this->setDescripcion($aDatos['descripcion']);
        }
        if (array_key_exists('superada', $aDatos)) {
            $this->setSuperada(is_true($aDatos['superada']));
        }
        if (array_key_exists('breve', $aDatos)) {
            $this->setBreve($aDatos['breve']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_situacion
     */
    public function getId_situacion(): int
    {
        return $this->iid_situacion;
    }

    /**
     *
     * @param int $iid_situacion
     */
    public function setId_situacion(int $iid_situacion): void
    {
        $this->iid_situacion = $iid_situacion;
    }

    /**
     *
     * @return string $sdescripcion
     */
    public function getDescripcion(): string
    {
        return $this->sdescripcion;
    }

    /**
     *
     * @param string $sdescripcion
     */
    public function setDescripcion(string $sdescripcion): void
    {
        $this->sdescripcion = $sdescripcion;
    }

    /**
     *
     * @return bool $bsuperada
     */
    public function isSuperada(): bool
    {
        return $this->bsuperada;
    }

    /**
     *
     * @param bool $bsuperada
     */
    public function setSuperada(bool $bsuperada): void
    {
        $this->bsuperada = $bsuperada;
    }

    /**
     *
     * @return string|null $sbreve
     */
    public function getBreve(): ?string
    {
        return $this->sbreve;
    }

    /**
     *
     * @param string|null $sbreve
     */
    public function setBreve(?string $sbreve = null): void
    {
        $this->sbreve = $sbreve;
    }
}