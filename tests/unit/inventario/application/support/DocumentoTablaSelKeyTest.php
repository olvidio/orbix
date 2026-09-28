<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application\support;

use PHPUnit\Framework\TestCase;
use src\inventario\application\support\DocumentoTablaSelKey;
use src\shared\domain\helpers\FuncTablasSupport;

final class DocumentoTablaSelKeyTest extends TestCase
{
    public function test_id_doc_desde_clave_urlsafe_con_json_escalar_como_tablaDB(): void
    {
        $key = FuncTablasSupport::urlsafeB64encode(json_encode(19, JSON_THROW_ON_ERROR));

        $this->assertSame(19, DocumentoTablaSelKey::idDocFromUrlsafeKey($key));
        $this->assertSame(19, DocumentoTablaSelKey::idDocFromUrlsafeKey('MTk.'));
    }

    public function test_id_doc_desde_clave_urlsafe_con_mapa_id_doc(): void
    {
        $key = FuncTablasSupport::urlsafeB64encode(json_encode(['id_doc' => 42], JSON_THROW_ON_ERROR));

        $this->assertSame(42, DocumentoTablaSelKey::idDocFromUrlsafeKey($key));
    }

    public function test_id_doc_desde_clave_urlsafe_con_array_indexado(): void
    {
        $key = FuncTablasSupport::urlsafeB64encode(json_encode([99], JSON_THROW_ON_ERROR));

        $this->assertSame(99, DocumentoTablaSelKey::idDocFromUrlsafeKey($key));
    }

    public function test_clave_vacia_o_no_decodable_devuelve_null(): void
    {
        $this->assertNull(DocumentoTablaSelKey::idDocFromUrlsafeKey(''));
        $this->assertNull(DocumentoTablaSelKey::idDocFromUrlsafeKey('***no-es-clave-tabla***'));
    }

    public function test_json_invalido_devuelve_null(): void
    {
        $key = FuncTablasSupport::urlsafeB64encode('not-json');

        $this->assertNull(DocumentoTablaSelKey::idDocFromUrlsafeKey($key));
    }
}
