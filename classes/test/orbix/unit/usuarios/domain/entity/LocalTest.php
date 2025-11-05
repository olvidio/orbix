<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\Local;
use src\usuarios\domain\value_objects\IdLocale;
use src\usuarios\domain\value_objects\NombreLocale;
use src\usuarios\domain\value_objects\Idioma;
use src\usuarios\domain\value_objects\NombreIdioma;
use Tests\myTest;

class LocalTest extends myTest
{
    private Local $local;

    public function setUp(): void
    {
        parent::setUp();
        $this->local = new Local();
        $this->local->setId_locale(new IdLocale('en_US'));
    }

    public function test_get_id_locale()
    {
        $this->assertInstanceOf(IdLocale::class, $this->local->getId_locale());
        $this->assertEquals('en_US', $this->local->getId_localeAsString());
    }

    public function test_set_and_get_nom_locale()
    {
        $nombreLocale = new NombreLocale('English (United States)');
        $this->local->setNom_locale($nombreLocale);
        $this->assertInstanceOf(NombreLocale::class, $this->local->getNom_locale());
        $this->assertEquals('English (United States)', $this->local->getNom_localeAsString());
    }

    public function test_set_and_get_idioma()
    {
        $idioma = new Idioma('en');
        $this->local->setIdioma($idioma);
        $this->assertInstanceOf(Idioma::class, $this->local->getIdioma());
        $this->assertEquals('en', $this->local->getIdiomaAsString());
    }

    public function test_set_and_get_nom_idioma()
    {
        $nombreIdioma = new NombreIdioma('English');
        $this->local->setNom_idioma($nombreIdioma);
        $this->assertInstanceOf(NombreIdioma::class, $this->local->getNom_idioma());
        $this->assertEquals('English', $this->local->getNom_idiomaAsString());
    }

    public function test_set_and_get_activo()
    {
        $this->local->setActivo(true);
        $this->assertTrue($this->local->isActivo());
    }

    public function test_set_all_attributes()
    {
        $local = new Local();
        $attributes = [
            'id_locale' => new IdLocale('en_US'),
            'nom_locale' => new NombreLocale('English (United States)'),
            'idioma' => new Idioma('en'),
            'nom_idioma' => new NombreIdioma('English'),
            'activo' => true
        ];
        $local->setAllAttributes($attributes);

        $this->assertEquals('en_US', $local->getId_localeAsString());
        $this->assertEquals('English (United States)', $local->getNom_localeAsString());
        $this->assertEquals('en', $local->getIdiomaAsString());
        $this->assertEquals('English', $local->getNom_idiomaAsString());
        $this->assertTrue($local->isActivo());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $local = new Local();
        $attributes = [
            'id_locale' => 'en_US',
            'nom_locale' => 'English (United States)',
            'idioma' => 'en',
            'nom_idioma' => 'English',
            'activo' => true
        ];
        $local->setAllAttributes($attributes);

        $this->assertEquals('en_US', $local->getId_localeAsString());
        $this->assertEquals('English (United States)', $local->getNom_localeAsString());
        $this->assertEquals('en', $local->getIdiomaAsString());
        $this->assertEquals('English', $local->getNom_idiomaAsString());
        $this->assertTrue($local->isActivo());
    }
}