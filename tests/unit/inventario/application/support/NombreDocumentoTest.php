<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application\support;

use PHPUnit\Framework\TestCase;
use src\inventario\application\support\NombreDocumento;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\TipoDoc;

final class NombreDocumentoTest extends TestCase
{
    private static function tipo(?bool $llave, ?string $nom): TipoDoc
    {
        $t = new TipoDoc();
        $t->setId_tipo_doc(1);
        $t->setSigla('B');
        $t->setNom_doc($nom);
        $t->setBajo_llave($llave);
        return $t;
    }

    private static function doc(?int $reg = null, ?int $ej = null, ?int $ini = null, ?int $fin = null): Documento
    {
        $d = new Documento();
        $d->setId_doc(1);
        $d->setId_tipo_doc(1);
        $d->setId_ubi(1);
        $d->setNum_reg($reg);
        $d->setNum_ejemplares($ej);
        $d->setNum_ini($ini);
        $d->setNum_fin($fin);
        return $d;
    }

    public function test_partes_del_nombre(): void
    {
        $this->assertSame('B', NombreDocumento::compuesto(self::tipo(null, null), self::doc()));
        $this->assertSame('* B Carta, 413 (3 ej.)', NombreDocumento::compuesto(self::tipo(true, 'Carta'), self::doc(413, 3)));
        $this->assertSame('B Carta', NombreDocumento::compuesto(self::tipo(false, 'Carta'), self::doc(null, 1)), 'un ejemplar no se indica');
        $this->assertSame('B Carta desde el 5-Actual', NombreDocumento::compuesto(self::tipo(false, 'Carta'), self::doc(null, null, 5)));
        $this->assertSame('B Carta 5- 9', NombreDocumento::compuesto(self::tipo(false, 'Carta'), self::doc(null, null, 5, 9), ' '));
    }
}
