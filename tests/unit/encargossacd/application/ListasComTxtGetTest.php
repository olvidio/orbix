<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\ListasComTxtGet;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoTextoField;
use src\encargossacd\domain\entity\EncargoTexto;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\security\HashB;

final class ListasComTxtGetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session_id('test-session-listas-com-txt-get');
    }

    public function test_sin_filas_devuelve_texto_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(
                Criteria::create()
                    ->where(Filter::eq(EncargoTextoField::Clave, 'c1'))
                    ->andWhere(Filter::eq(EncargoTextoField::Idioma, 'es_ES.UTF-8'))
            ))
            ->willReturn([]);

        $useCase = new ListasComTxtGet($repo);
        $out = $useCase->execute('c1', 'es_ES.UTF-8');
        $this->assertSame('', $out['texto']);
        $this->assertSame(['clave' => 'c1', 'idioma' => 'es_ES.UTF-8'], HashB::open($out['ctx_guardar'], 'listas_com_txt_update'));
    }

    public function test_getEncargoTextos_false_trata_como_vacio(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        $useCase = new ListasComTxtGet($repo);
        $out = $useCase->execute('k', 'ca_ES.UTF-8');
        $this->assertSame('', $out['texto']);
        $this->assertSame(['clave' => 'k', 'idioma' => 'ca_ES.UTF-8'], HashB::open($out['ctx_guardar'], 'listas_com_txt_update'));
    }

    public function test_primera_fila(): void
    {
        $row = $this->createMock(EncargoTexto::class);
        $row->method('getTexto')->willReturn('Hola');

        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([$row]);

        $useCase = new ListasComTxtGet($repo);
        $out = $useCase->execute('x', 'es_ES.UTF-8');
        $this->assertSame('Hola', $out['texto']);
        $this->assertSame(['clave' => 'x', 'idioma' => 'es_ES.UTF-8'], HashB::open($out['ctx_guardar'], 'listas_com_txt_update'));
    }
}
