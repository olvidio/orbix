<?php

namespace Tests\unit\tablonanuncios\domain\entity;

use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use src\tablonanuncios\domain\value_objects\Categoria;
use src\tablonanuncios\domain\value_objects\EsquemaDestino;
use src\tablonanuncios\domain\value_objects\EsquemaEmisor;
use src\tablonanuncios\domain\value_objects\Idioma;
use src\tablonanuncios\domain\value_objects\Tablon;
use src\tablonanuncios\domain\value_objects\TextoAnuncio;
use src\tablonanuncios\domain\value_objects\UsuarioCreador;
use Tests\myTest;

class AnuncioTest extends myTest
{
    private Anuncio $Anuncio;

    public function setUp(): void
    {
        parent::setUp();
        $this->Anuncio = new Anuncio();
        $this->Anuncio->setUuid_item(new AnuncioId('550e8400-e29b-41d4-a716-446655440000'));
        $this->Anuncio->setUsuarioCreadorVo(new UsuarioCreador('test'));
    }

    public function test_set_and_get_uuid_item()
    {
        $uuid_itemVo = new AnuncioId('550e8400-e29b-41d4-a716-446655440000');
        $this->Anuncio->setUuid_item($uuid_itemVo);
        $this->assertInstanceOf(AnuncioId::class, $this->Anuncio->getUuid_item());
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $this->Anuncio->getUuid_item()->value());
    }

    public function test_set_and_get_usuario_creador()
    {
        $usuario_creadorVo = new UsuarioCreador('test');
        $this->Anuncio->setUsuarioCreadorVo($usuario_creadorVo);
        $this->assertInstanceOf(UsuarioCreador::class, $this->Anuncio->getUsuarioCreadorVo());
        $this->assertEquals('test', $this->Anuncio->getUsuarioCreadorVo()->value());
    }

    public function test_set_and_get_esquema_emisor()
    {
        $esquema_emisorVo = new EsquemaEmisor('test');
        $this->Anuncio->setEsquemaEmisorVo($esquema_emisorVo);
        $this->assertInstanceOf(EsquemaEmisor::class, $this->Anuncio->getEsquemaEmisorVo());
        $this->assertEquals('test', $this->Anuncio->getEsquemaEmisorVo()->value());
    }

    public function test_set_and_get_esquema_destino()
    {
        $esquema_destinoVo = new EsquemaDestino('test');
        $this->Anuncio->setEsquemaDestinoVo($esquema_destinoVo);
        $this->assertInstanceOf(EsquemaDestino::class, $this->Anuncio->getEsquemaDestinoVo());
        $this->assertEquals('test', $this->Anuncio->getEsquemaDestinoVo()->value());
    }

    public function test_set_and_get_texto_anuncio()
    {
        $texto_anuncioVo = new TextoAnuncio('Test');
        $this->Anuncio->setTextoAnuncioVo($texto_anuncioVo);
        $this->assertInstanceOf(TextoAnuncio::class, $this->Anuncio->getTextoAnuncioVo());
        $this->assertEquals('Test', $this->Anuncio->getTextoAnuncioVo()->value());
    }

    public function test_set_and_get_idioma()
    {
        $idiomaVo = new Idioma(1);
        $this->Anuncio->setIdiomaVo($idiomaVo);
        $this->assertInstanceOf(Idioma::class, $this->Anuncio->getIdiomaVo());
        $this->assertEquals(1, $this->Anuncio->getIdiomaVo()->value());
    }

    public function test_set_and_get_tablon()
    {
        $tablonVo = new Tablon('test');
        $this->Anuncio->setTablonVo($tablonVo);
        $this->assertInstanceOf(Tablon::class, $this->Anuncio->getTablonVo());
        $this->assertEquals('test', $this->Anuncio->getTablonVo()->value());
    }

    public function test_set_and_get_categoria()
    {
        $categoriaVo = new Categoria(1);
        $this->Anuncio->setCategoriaVo($categoriaVo);
        $this->assertInstanceOf(Categoria::class, $this->Anuncio->getCategoriaVo());
        $this->assertEquals(1, $this->Anuncio->getCategoriaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $anuncio = new Anuncio();
        $attributes = [
            'uuid_item' => new AnuncioId('550e8400-e29b-41d4-a716-446655440000'),
            'usuario_creador' => new UsuarioCreador('test'),
            'esquema_emisor' => new EsquemaEmisor('test'),
            'esquema_destino' => new EsquemaDestino('test'),
            'texto_anuncio' => new TextoAnuncio('Test'),
            'idioma' => new Idioma(1),
            'tablon' => new Tablon('test'),
            'categoria' => new Categoria(1),
        ];
        $anuncio->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $anuncio->getUuid_item()->value());
        $this->assertEquals('test', $anuncio->getUsuarioCreadorVo()->value());
        $this->assertEquals('test', $anuncio->getEsquemaEmisorVo()->value());
        $this->assertEquals('test', $anuncio->getEsquemaDestinoVo()->value());
        $this->assertEquals('Test', $anuncio->getTextoAnuncioVo()->value());
        $this->assertEquals(1, $anuncio->getIdiomaVo()->value());
        $this->assertEquals('test', $anuncio->getTablonVo()->value());
        $this->assertEquals(1, $anuncio->getCategoriaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $anuncio = new Anuncio();
        $attributes = [
            'uuid_item' => '550e8400-e29b-41d4-a716-446655440000',
            'usuario_creador' => 'test',
            'esquema_emisor' => 'test',
            'esquema_destino' => 'test',
            'texto_anuncio' => 'Test',
            'idioma' => 1,
            'tablon' => 'test',
            'categoria' => 1,
        ];
        $anuncio->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $anuncio->getUuid_item()->value());
        $this->assertEquals('test', $anuncio->getUsuarioCreadorVo()->value());
        $this->assertEquals('test', $anuncio->getEsquemaEmisorVo()->value());
        $this->assertEquals('test', $anuncio->getEsquemaDestinoVo()->value());
        $this->assertEquals('Test', $anuncio->getTextoAnuncioVo()->value());
        $this->assertEquals(1, $anuncio->getIdiomaVo()->value());
        $this->assertEquals('test', $anuncio->getTablonVo()->value());
        $this->assertEquals(1, $anuncio->getCategoriaVo()->value());
    }
}
