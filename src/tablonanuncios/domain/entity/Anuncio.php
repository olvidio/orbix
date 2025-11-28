<?php

namespace src\tablonanuncios\domain\entity;

use src\tablonanuncios\domain\value_objects\AnuncioId;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class Anuncio
{
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

    public function setAllAttributes(array $aDatos): Anuncio
    {
        if (array_key_exists('uuid_item', $aDatos)) {
            $uuid_item = new AnuncioId($aDatos['uuid_item']);
            $this->setUuid_item($uuid_item);
        }
        if (array_key_exists('usuario_creador', $aDatos)) {
            $this->setUsuarioCreador($aDatos['usuario_creador']);
        }
        if (array_key_exists('esquema_emisor', $aDatos)) {
            $this->setEsquemaEmisor($aDatos['esquema_emisor']);
        }
        if (array_key_exists('esquema_destino', $aDatos)) {
            $this->setEsquemaDestino($aDatos['esquema_destino']);
        }
        if (array_key_exists('texto_anuncio', $aDatos)) {
            $this->setTextoAnuncio($aDatos['texto_anuncio']);
        }
        if (array_key_exists('idioma', $aDatos)) {
            $this->setIdioma($aDatos['idioma']);
        }
        if (array_key_exists('tablon', $aDatos)) {
            $this->setTablon($aDatos['tablon']);
        }
        if (array_key_exists('tanotado', $aDatos)) {
            $tanotado = $aDatos['tanotado'] ?? new NullDateTimeLocal();
            $this->setTanotado($tanotado);
        }
        if (array_key_exists('teliminado', $aDatos)) {
            $teliminado = $aDatos['teliminado'] ?? new NullDateTimeLocal();
            $this->setTeliminado($teliminado);
        }
        if (array_key_exists('categoria', $aDatos)) {
            $this->setCategoria($aDatos['categoria']);
        }
        return $this;
    }

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