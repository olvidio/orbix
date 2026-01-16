<?php

namespace src\tablonanuncios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use src\tablonanuncios\domain\value_objects\Categoria;
use src\tablonanuncios\domain\value_objects\EsquemaDestino;
use src\tablonanuncios\domain\value_objects\EsquemaEmisor;
use src\tablonanuncios\domain\value_objects\Idioma;
use src\tablonanuncios\domain\value_objects\Tablon;
use src\tablonanuncios\domain\value_objects\TextoAnuncio;
use src\tablonanuncios\domain\value_objects\UsuarioCreador;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class Anuncio
{
    use Hydratable;

    private AnuncioId $uuid_item;
    private UsuarioCreador $usuario_creador;
    private EsquemaEmisor $esquema_emisor;
    private EsquemaDestino $esquema_destino;
    private TextoAnuncio $texto_anuncio;
    private ?Idioma $idioma;
    private Tablon $tablon;
    private DateTimeLocal|NullDateTimeLocal|null $t_anotado;
    private DateTimeLocal|NullDateTimeLocal|null $t_eliminado;
    private Categoria $categoria;

    public function getUuid_item(): AnuncioId
    {
        return $this->uuid_item;
    }

    public function setUuid_item(AnuncioId|string $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof AnuncioId
            ? $uuid_item
            : AnuncioId::fromString($uuid_item);
    }

    public function getUsuarioCreadorVo(): UsuarioCreador
    {
        return $this->usuario_creador;
    }

    public function setUsuarioCreadorVo(UsuarioCreador|string|null $usuario_creador): void
    {
        $this->usuario_creador = $usuario_creador instanceof UsuarioCreador
            ? $usuario_creador
            : UsuarioCreador::fromNullableString($usuario_creador);
    }

    public function getEsquemaEmisorVo(): EsquemaEmisor
    {
        return $this->esquema_emisor;
    }

    public function setEsquemaEmisorVo(EsquemaEmisor|string|null $esquema_emisor): void
    {
        $this->esquema_emisor = $esquema_emisor instanceof EsquemaEmisor
            ? $esquema_emisor
            : EsquemaEmisor::fromNullableString($esquema_emisor);
    }

    public function getEsquemaDestinoVo(): EsquemaDestino
    {
        return $this->esquema_destino;
    }

    public function setEsquemaDestinoVo(EsquemaDestino|string|null $esquema_destino): void
    {
        $this->esquema_destino = $esquema_destino instanceof EsquemaDestino
            ? $esquema_destino
            : EsquemaDestino::fromNullableString($esquema_destino);
    }

    public function getTextoAnuncioVo(): TextoAnuncio
    {
        return $this->texto_anuncio;
    }

    public function setTextoAnuncioVo(TextoAnuncio|string|null $texto_anuncio): void
    {
        $this->texto_anuncio = $texto_anuncio instanceof TextoAnuncio
            ? $texto_anuncio
            : TextoAnuncio::fromNullableString($texto_anuncio);
    }

    public function getIdiomaVo(): ?Idioma
    {
        return $this->idioma;
    }

    public function setIdiomaVo(Idioma|string|null $idioma): void
    {
        $this->idioma = $idioma instanceof Idioma
            ? $idioma
            : Idioma::fromNullableString($idioma);
    }

    public function getTablonVo(): Tablon
    {
        return $this->tablon;
    }

    public function setTablonVo(Tablon|string|null $tablon): void
    {
        $this->tablon = $tablon instanceof Tablon
            ? $tablon
            : Tablon::fromNullableString($tablon);
    }

    public function getT_anotado(): DateTimeLocal|NullDateTimeLocal
    {
        return $this->t_anotado ?? new NullDateTimeLocal();
    }

    public function setT_anotado(DateTimeLocal|NullDateTimeLocal|null $t_anotado= null): void
    {
        $this->t_anotado = $t_anotado;
    }

    public function getT_eliminado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->t_eliminado ?? new NullDateTimeLocal();
    }

    public function setT_eliminado(DateTimeLocal|NullDateTimeLocal|null $t_eliminado = null): void
    {
        $this->t_eliminado = $t_eliminado;
    }

    public function getCategoriaVo(): Categoria
    {
        return $this->categoria;
    }

    public function setCategoriaVo(Categoria|int|null $categoria): void
    {
        $this->categoria = $categoria instanceof Categoria
            ? $categoria
            : Categoria::fromNullableInt($categoria);
    }


}