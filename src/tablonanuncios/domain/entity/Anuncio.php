<?php

namespace src\tablonanuncios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class Anuncio
{
    use Hydratable;

    public const CAT_ALERTA = 1;
    public const CAT_AVISO = 2;


    private AnuncioId $uuid_item;
    private string $usuario_creador;
    private string $esquema_emisor;
    private string $esquema_destino;
    private string $texto_anuncio;
    private ?string $idioma;
    private string $tablon;
    private DateTimeLocal|NullDateTimeLocal $tanotado;
    private DateTimeLocal|NullDateTimeLocal $teliminado;
    private int $categoria;

    public function getUuid_item(): AnuncioId
    {
        return $this->uuid_item;
    }

    public function setUuid_item(AnuncioId $uuid_item): void
    {
        $this->uuid_item = $uuid_item;
    }

    public function getUsuarioCreador(): string
    {
        return $this->usuario_creador;
    }

    public function setUsuarioCreador(string $usuario_creador): void
    {
        $this->usuario_creador = $usuario_creador;
    }

    public function getEsquemaEmisor(): string
    {
        return $this->esquema_emisor;
    }

    public function setEsquemaEmisor(string $esquema_emisor): void
    {
        $this->esquema_emisor = $esquema_emisor;
    }

    public function getEsquemaDestino(): string
    {
        return $this->esquema_destino;
    }

    public function setEsquemaDestino(string $esquema_destino): void
    {
        $this->esquema_destino = $esquema_destino;
    }

    public function getTextoAnuncio(): string
    {
        return $this->texto_anuncio;
    }

    public function setTextoAnuncio(string $texto_anuncio): void
    {
        $this->texto_anuncio = $texto_anuncio;
    }

    public function getIdioma(): ?string
    {
        return $this->idioma;
    }

    public function setIdioma(?string $idioma): void
    {
        $this->idioma = $idioma;
    }

    public function getTablon(): string
    {
        return $this->tablon;
    }

    public function setTablon(string $tablon): void
    {
        $this->tablon = $tablon;
    }

    public function getTanotado(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->tanotado ?? new NullDateTimeLocal();
    }

    public function setTanotado(DateTimeLocal|NullDateTimeLocal $tanotado): void
    {
        $this->tanotado = $tanotado;
    }

    public function getTeliminado(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->teliminado ?? new NullDateTimeLocal();
    }

    public function setTeliminado(DateTimeLocal|NullDateTimeLocal $teliminado): void
    {
        $this->teliminado = $teliminado;
    }

    public function getCategoria(): int
    {
        return $this->categoria;
    }

    public function setCategoria(int $categoria): void
    {
        $this->categoria = $categoria;
    }


}