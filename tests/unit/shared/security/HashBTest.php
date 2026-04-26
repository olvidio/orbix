<?php

namespace Tests\unit\shared\security;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/**
 * Tests unitarios de {@see HashB}.
 *
 * No extiende {@see \Tests\myTest} porque no necesita DB, DI ni permisos:
 * solo manipula `session_id()` para simular diferentes sesiones.
 */
class HashBTest extends TestCase
{
    /** Igual que el salt privado de HashB. */
    private const SALT = 'b+b+b+';

    protected function setUp(): void
    {
        // Fijamos session_id conocido para todos los tests. PHP permite
        // session_id(...) siempre que no haya session_start() activa.
        session_id('test-session-alpha');
    }

    /* ---------------------------------------------------------------
     * Roundtrip
     * --------------------------------------------------------------- */

    public function test_sign_and_open_roundtrip_returns_original_context(): void
    {
        $capsule = HashB::sign('tarifa_ubi_eliminar', ['id_item' => 5, 'id_ubi' => 2, 'year' => 2026]);
        $context = HashB::open($capsule, 'tarifa_ubi_eliminar');

        $this->assertSame(['id_item' => 5, 'id_ubi' => 2, 'year' => 2026], $context);
    }

    public function test_context_with_complex_types_preserved(): void
    {
        $original = [
            'id'      => 0,
            'nombre'  => "María O'Brien <script>",
            'activo'  => true,
            'tags'    => ['a', 'b', 'c'],
            'anidado' => ['x' => 1, 'y' => ['z' => 2]],
            'nulo'    => null,
            'flotant' => 3.14,
        ];

        $capsule = HashB::sign('foo', $original);
        $context = HashB::open($capsule, 'foo');

        $this->assertSame($original, $context);
    }

    public function test_empty_context_allowed(): void
    {
        $capsule = HashB::sign('crear_nuevo');
        $this->assertSame([], HashB::open($capsule, 'crear_nuevo'));
    }

    public function test_default_ttl_applied_when_null(): void
    {
        $before = time();
        $capsule = HashB::sign('x', []);
        $peek = HashB::peekUnsafe($capsule);

        $this->assertNotNull($peek);
        $this->assertSame('x', $peek['a']);
        $this->assertGreaterThanOrEqual($before + HashB::DEFAULT_TTL, $peek['x']);
        $this->assertLessThanOrEqual(time() + HashB::DEFAULT_TTL, $peek['x']);
    }

    public function test_ttl_zero_means_no_expiration(): void
    {
        $capsule = HashB::sign('x', ['id' => 1], 0);
        $peek = HashB::peekUnsafe($capsule);

        $this->assertNotNull($peek);
        $this->assertSame(0, $peek['x']);
        $this->assertSame(['id' => 1], HashB::open($capsule, 'x'));
    }

    public function test_negative_ttl_means_no_expiration(): void
    {
        $capsule = HashB::sign('x', ['id' => 1], -100);
        $peek = HashB::peekUnsafe($capsule);

        $this->assertNotNull($peek);
        $this->assertSame(0, $peek['x']);
    }

    public function test_two_signs_with_same_input_produce_same_capsule_when_ttl_zero(): void
    {
        // Determinismo útil: cuando exp=0 y session y context son iguales,
        // la cápsula es idéntica. Útil para caché / idempotencia.
        $a = HashB::sign('x', ['id' => 1], 0);
        $b = HashB::sign('x', ['id' => 1], 0);
        $this->assertSame($a, $b);
    }

    /* ---------------------------------------------------------------
     * Validación de acción
     * --------------------------------------------------------------- */

    public function test_open_with_wrong_action_throws_action_mismatch(): void
    {
        $capsule = HashB::sign('tarifa_ubi_eliminar', ['id_item' => 5]);

        try {
            HashB::open($capsule, 'tarifa_ubi_update');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::ACTION_MISMATCH, $e->getReason());
        }
    }

    public function test_sign_with_empty_action_throws_invalid_argument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        HashB::sign('', ['id' => 1]);
    }

    /* ---------------------------------------------------------------
     * Validación de firma
     * --------------------------------------------------------------- */

    public function test_open_with_tampered_payload_throws_signature_mismatch(): void
    {
        $capsule = HashB::sign('foo', ['id' => 5]);
        [$encoded, $sig] = explode('.', $capsule, 2);

        // Construyo un payload alternativo con id=999 y lo pego con la firma original.
        $forgedPayload = $this->base64UrlEncode(json_encode(
            ['a' => 'foo', 'c' => ['id' => 999], 's' => session_id(), 'x' => time() + 3600],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));
        $tampered = $forgedPayload . '.' . $sig;

        try {
            HashB::open($tampered, 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::SIGNATURE_MISMATCH, $e->getReason());
        }
    }

    public function test_open_with_tampered_signature_throws_signature_mismatch(): void
    {
        $capsule = HashB::sign('foo', ['id' => 5]);
        [$encoded, ] = explode('.', $capsule, 2);
        $tampered = $encoded . '.' . str_repeat('0', 32);

        try {
            HashB::open($tampered, 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::SIGNATURE_MISMATCH, $e->getReason());
        }
    }

    public function test_open_with_different_session_fails(): void
    {
        $capsule = HashB::sign('foo', ['id' => 5]);

        // Cambio de sesión: la firma (que incluye session_id()) deja de coincidir.
        session_id('test-session-beta');

        try {
            HashB::open($capsule, 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            // Concretamente, hoy debería fallar por firma (la firma depende
            // de session_id()). Lo importante para el contrato de
            // seguridad es que alguna validación haya impedido abrirla.
            $this->assertTrue(in_array(
                $e->getReason(),
                [HashBInvalidException::SIGNATURE_MISMATCH, HashBInvalidException::SESSION_MISMATCH],
                true
            ), "got reason: {$e->getReason()}");
        }
    }

    public function test_open_with_session_mismatch_payload_but_valid_signature_throws_session_mismatch(): void
    {
        // Para probar SESSION_MISMATCH aisladamente: construyo manualmente
        // una cápsula cuya firma sea válida con la sesión actual pero que
        // contenga una session_id distinta en el payload. Esto no puede
        // producirse con la API pública; es para cubrir el camino.
        $payload = [
            'a' => 'foo',
            'c' => ['id' => 5],
            's' => 'another-session',
            'x' => time() + 3600,
        ];
        $encoded = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $sig = md5($encoded . session_id() . self::SALT);
        $capsule = $encoded . '.' . $sig;

        try {
            HashB::open($capsule, 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::SESSION_MISMATCH, $e->getReason());
        }
    }

    /* ---------------------------------------------------------------
     * Caducidad
     * --------------------------------------------------------------- */

    public function test_open_with_expired_capsule_throws_expired(): void
    {
        // Fabrico una cápsula ya caducada manualmente (la API pública no
        // permite firmar algo caducado; TTL<=0 significa "sin caducidad").
        $payload = [
            'a' => 'foo',
            'c' => ['id' => 5],
            's' => session_id(),
            'x' => time() - 10,
        ];
        $encoded = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $sig = md5($encoded . session_id() . self::SALT);
        $capsule = $encoded . '.' . $sig;

        try {
            HashB::open($capsule, 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::EXPIRED, $e->getReason());
        }
    }

    /* ---------------------------------------------------------------
     * Formato
     * --------------------------------------------------------------- */

    public function test_open_with_empty_capsule_throws_malformed(): void
    {
        $this->assertThrowsReason(fn() => HashB::open('', 'foo'), HashBInvalidException::MALFORMED);
    }

    public function test_open_with_no_dot_separator_throws_malformed(): void
    {
        $this->assertThrowsReason(fn() => HashB::open('abcdef', 'foo'), HashBInvalidException::MALFORMED);
    }

    public function test_open_with_extra_dots_throws_malformed(): void
    {
        $this->assertThrowsReason(fn() => HashB::open('a.b.c', 'foo'), HashBInvalidException::MALFORMED);
    }

    public function test_open_with_empty_payload_part_throws_malformed(): void
    {
        $this->assertThrowsReason(fn() => HashB::open('.abcdef', 'foo'), HashBInvalidException::MALFORMED);
    }

    public function test_open_with_empty_sig_part_throws_malformed(): void
    {
        $this->assertThrowsReason(fn() => HashB::open('abcdef.', 'foo'), HashBInvalidException::MALFORMED);
    }

    public function test_open_with_invalid_base64_payload_throws_signature_mismatch(): void
    {
        // Un payload inválido en base64 primero falla la firma (md5 de
        // un string sin sentido no coincide con nuestra firma). La
        // validación de firma es la primera barrera.
        try {
            HashB::open('@@@@@.' . str_repeat('0', 32), 'foo');
            $this->fail('expected HashBInvalidException');
        } catch (HashBInvalidException $e) {
            $this->assertSame(HashBInvalidException::SIGNATURE_MISMATCH, $e->getReason());
        }
    }

    public function test_open_with_non_array_payload_throws_malformed(): void
    {
        // Payload que decodifica a JSON válido pero no es un array asociativo.
        $encoded = $this->base64UrlEncode('"just-a-string"');
        $sig = md5($encoded . session_id() . self::SALT);
        $this->assertThrowsReason(
            fn() => HashB::open($encoded . '.' . $sig, 'foo'),
            HashBInvalidException::MALFORMED
        );
    }

    public function test_open_with_missing_payload_fields_throws_malformed(): void
    {
        $encoded = $this->base64UrlEncode(json_encode(['a' => 'foo'])); // faltan c, s, x
        $sig = md5($encoded . session_id() . self::SALT);
        $this->assertThrowsReason(
            fn() => HashB::open($encoded . '.' . $sig, 'foo'),
            HashBInvalidException::MALFORMED
        );
    }

    /* ---------------------------------------------------------------
     * peekUnsafe
     * --------------------------------------------------------------- */

    public function test_peek_unsafe_returns_structure(): void
    {
        $capsule = HashB::sign('foo', ['id' => 5], 123);
        $peek = HashB::peekUnsafe($capsule);

        $this->assertNotNull($peek);
        $this->assertSame('foo', $peek['a']);
        $this->assertSame(['id' => 5], $peek['c']);
        $this->assertSame('test-session-alpha', $peek['s']);
        $this->assertIsInt($peek['x']);
    }

    public function test_peek_unsafe_returns_null_for_malformed(): void
    {
        $this->assertNull(HashB::peekUnsafe(''));
        $this->assertNull(HashB::peekUnsafe('nodot'));
        $this->assertNull(HashB::peekUnsafe('@@@.sig'));
    }

    public function test_peek_unsafe_does_not_validate_signature(): void
    {
        // Cambio sesión para invalidar la firma; peek debe devolver
        // los datos igualmente porque es solo inspección.
        $capsule = HashB::sign('foo', ['id' => 5]);
        session_id('another-session');

        $peek = HashB::peekUnsafe($capsule);
        $this->assertNotNull($peek);
        $this->assertSame('foo', $peek['a']);
    }

    /* ---------------------------------------------------------------
     * Helpers
     * --------------------------------------------------------------- */

    private function assertThrowsReason(callable $fn, string $expectedReason): void
    {
        try {
            $fn();
            $this->fail('expected HashBInvalidException with reason ' . $expectedReason);
        } catch (HashBInvalidException $e) {
            $this->assertSame($expectedReason, $e->getReason(), "message was: {$e->getMessage()}");
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
