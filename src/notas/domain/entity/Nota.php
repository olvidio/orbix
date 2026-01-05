<?php

namespace src\notas\domain\entity;

use src\notas\domain\value_objects\Descripcion;
use src\notas\domain\value_objects\Breve;
use src\shared\domain\traits\Hydratable;
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
    use Hydratable;

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
    /**
     * Devuelve el array de textos de estados traducidos
     *
     * @return array
     */
    public static function getArrayStatusTxt(): array
    {
        return [
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

    /**
     * Devuelve el texto traducido de un estado específico
     *
     * @param int $id_situacion
     * @return string
     */
    public static function getStatusTxt(int $id_situacion): string
    {
        $array = self::getArrayStatusTxt();
        return $array[$id_situacion] ?? _("desconocido");
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_situacion;

    private string $descripcion;

    private bool $superada;

    private string|null $breve = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_situacion(): int
    {
        return $this->id_situacion;
    }


    public function setId_situacion(int $id_situacion): void
    {
        $this->id_situacion = $id_situacion;
    }


    public function getDescripcionVo(): ?Descripcion
    {
        return Descripcion::fromNullable($this->descripcion);
    }


    public function setDescripcionVo(?Descripcion $oDescripcion): void
    {
        $this->descripcion = $oDescripcion?->value();
    }

    /**
     * @deprecated use getDescripcionVo()
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * @deprecated use setDescripcionVo()
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }


    public function isSuperada(): bool
    {
        return $this->superada;
    }


    public function setSuperada(bool $superada): void
    {
        $this->superada = $superada;
    }


    public function getBreveVo(): ?Breve
    {
        return Breve::fromNullable($this->breve);
    }


    public function setBreveVo(?Breve $oBreve): void
    {
        $this->breve = $oBreve?->value();
    }

    /**
     * @deprecated use getBreveVo()
     */
    public function getBreve(): ?string
    {
        return $this->breve;
    }

    /**
     * @deprecated use setBreveVo()
     */
    public function setBreve(?string $breve = null): void
    {
        $this->breve = $breve;
    }
}