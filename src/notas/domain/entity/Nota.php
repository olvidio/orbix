<?php

namespace src\notas\domain\entity;

use src\notas\domain\value_objects\Descripcion;
use src\notas\domain\value_objects\Breve;
use src\notas\domain\value_objects\NotaSituacion;
use src\shared\domain\traits\Hydratable;

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

    /**
     * Devuelve el texto traducido de un estado específico
     *
     * @param int $id_situacion
     * @return string
     */
    public static function getStatusTxt(int $id_situacion): string
    {
        $array = NotaSituacion::getArraySituacionTxt();
        return $array[$id_situacion] ?? _("desconocido");
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private NotaSituacion $id_situacion;

    private Descripcion $descripcion;

    private bool $superada;

    private ?Breve $breve = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated use getIdSituacionVo()
     */
    public function getId_situacion(): int
    {
        return $this->id_situacion->value();
    }

    public function getIdSituacionVo(): NotaSituacion
    {
        return $this->id_situacion;
    }

    /**
     * @deprecated use setIdSituacionVo()
     */
    public function setId_situacion(int $id_situacion): void
    {
        $this->id_situacion = NotaSituacion::fromNullableInt( $id_situacion);
    }

    public function setIdSituacionVo(NotaSituacion|int|null $oIdSituacion): void
    {
        $this->id_situacion = $oIdSituacion instanceof NotaSituacion
            ? $oIdSituacion
            : NotaSituacion::fromNullableInt( $oIdSituacion);
    }

    public function getDescripcionVo(): ?Descripcion
    {
        return $this->descripcion;
    }

    public function setDescripcionVo(Descripcion|string|null $oDescripcion): void
    {
        $this->descripcion = $oDescripcion instanceof Descripcion
            ? $oDescripcion
            : Descripcion::fromNullableString($oDescripcion);
    }

    /**
     * @deprecated use getDescripcionVo()
     */
    public function getDescripcion(): string
    {
        return $this->descripcion->value();
    }

    /**
     * @deprecated use setDescripcionVo()
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = Descripcion::fromNullableString($descripcion);
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
        return $this->breve;
    }


    public function setBreveVo(Breve|string|null $texto = null): void
    {
        $this->breve = $texto instanceof Breve
            ? $texto
            : Breve::fromNullableString($texto);
    }

    /**
     * @deprecated use getBreveVo()
     */
    public function getBreve(): ?string
    {
        return $this->breve?->value();
    }

    /**
     * @deprecated use setBreveVo()
     */
    public function setBreve(?string $breve = null): void
    {
        $this->breve = Breve::fromNullableString($breve);
    }
}