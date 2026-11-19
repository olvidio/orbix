<?php

declare(strict_types=1);

namespace src\shared\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\AyudaConsultaId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Una pregunta hecha en «Preguntar a la documentación», con el primer resultado que dio la ayuda
 * y la valoración de quien preguntó. Es anónima: guarda la delegación (`esquema`), no el usuario.
 */
class AyudaConsulta
{
    use Hydratable;

    private AyudaConsultaId $uuid_item;

    private ?DateTimeLocal $f_consulta = null;

    private string $pregunta = '';

    private ?string $esquema = null;

    private ?string $pantalla = null;

    private int $n_resultados = 0;

    private ?float $puntuacion = null;

    private ?string $fuente = null;

    private ?string $titulo = null;

    private ?bool $util = null;

    private ?string $comentario = null;

    private int $estado = AyudaConsultaEstado::PENDIENTE;

    public function getUuid_item(): AyudaConsultaId
    {
        return $this->uuid_item;
    }

    public function setUuid_item(AyudaConsultaId|string $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof AyudaConsultaId
            ? $uuid_item
            : AyudaConsultaId::fromString($uuid_item);
    }

    public function getF_consulta(): ?DateTimeLocal
    {
        return $this->f_consulta;
    }

    public function setF_consulta(?DateTimeLocal $f_consulta = null): void
    {
        $this->f_consulta = $f_consulta;
    }

    public function getPregunta(): string
    {
        return $this->pregunta;
    }

    public function setPregunta(string $pregunta): void
    {
        $this->pregunta = $pregunta;
    }

    public function getEsquema(): ?string
    {
        return $this->esquema;
    }

    public function setEsquema(?string $esquema = null): void
    {
        $this->esquema = $esquema;
    }

    public function getPantalla(): ?string
    {
        return $this->pantalla;
    }

    public function setPantalla(?string $pantalla = null): void
    {
        $this->pantalla = $pantalla;
    }

    public function getN_resultados(): int
    {
        return $this->n_resultados;
    }

    public function setN_resultados(int|string $n_resultados): void
    {
        $this->n_resultados = (int) $n_resultados;
    }

    public function getPuntuacion(): ?float
    {
        return $this->puntuacion;
    }

    /** La columna es `numeric`: PostgreSQL la devuelve como texto. */
    public function setPuntuacion(float|int|string|null $puntuacion = null): void
    {
        $this->puntuacion = ($puntuacion === null || $puntuacion === '') ? null : (float) $puntuacion;
    }

    public function getFuente(): ?string
    {
        return $this->fuente;
    }

    public function setFuente(?string $fuente = null): void
    {
        $this->fuente = $fuente;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo = null): void
    {
        $this->titulo = $titulo;
    }

    /** `null` sin valorar, `true` sirvió, `false` no sirvió. */
    public function getUtil(): ?bool
    {
        return $this->util;
    }

    public function setUtil(?bool $util = null): void
    {
        $this->util = $util;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario = null): void
    {
        $this->comentario = $comentario;
    }

    public function getEstado(): int
    {
        return $this->estado;
    }

    public function getEstadoVo(): AyudaConsultaEstado
    {
        return new AyudaConsultaEstado($this->estado);
    }

    public function setEstado(AyudaConsultaEstado|int|string $estado): void
    {
        $this->estado = $estado instanceof AyudaConsultaEstado
            ? $estado->value()
            : (new AyudaConsultaEstado((int) $estado))->value();
    }
}
