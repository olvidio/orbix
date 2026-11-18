<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\infrastructure\persistence;

use PHPUnit\Framework\TestCase;
use src\usuarios\infrastructure\persistence\FicheroLoginCliTicketRepository;

final class FicheroLoginCliTicketRepositoryTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/orbix_test_login_cli_' . bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/*') ?: [] as $f) {
            unlink($f);
        }
        if (is_dir($this->dir)) {
            rmdir($this->dir);
        }
    }

    public function test_un_ticket_se_consume_una_sola_vez(): void
    {
        $repo = new FicheroLoginCliTicketRepository($this->dir);

        $ticket = $repo->emitir('usuario1', 'H-dlbv');

        $this->assertSame(['username' => 'usuario1', 'esquema' => 'H-dlbv'], $repo->consumir($ticket));
        $this->assertNull($repo->consumir($ticket));
        $this->assertSame([], glob($this->dir . '/*'), 'no queda el fichero del ticket');
    }

    public function test_un_ticket_caducado_no_sirve(): void
    {
        $ticket = (new FicheroLoginCliTicketRepository($this->dir, -1))->emitir('usuario1', 'H-dlbv');

        $this->assertNull((new FicheroLoginCliTicketRepository($this->dir))->consumir($ticket));
    }

    /** @return iterable<string, array{string}> */
    public static function ticketsNoValidos(): iterable
    {
        yield 'vacío' => [''];
        yield 'desconocido' => [str_repeat('a', 64)];
        yield 'con ruta' => ['../../etc/passwd'];
        yield 'con mayúsculas' => [str_repeat('A', 64)];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('ticketsNoValidos')]
    public function test_un_ticket_no_valido_no_sirve(string $ticket): void
    {
        $this->assertNull((new FicheroLoginCliTicketRepository($this->dir))->consumir($ticket));
    }

    public function test_el_fichero_solo_lo_lee_el_usuario_del_servidor_y_no_lleva_el_ticket_en_el_nombre(): void
    {
        $ticket = (new FicheroLoginCliTicketRepository($this->dir))->emitir('usuario1', 'H-dlbv');

        $ficheros = glob($this->dir . '/*') ?: [];
        $this->assertCount(1, $ficheros);
        $this->assertSame(0600, fileperms($ficheros[0]) & 0777);
        $this->assertStringNotContainsString($ticket, $ficheros[0]);
        $this->assertStringNotContainsString($ticket, (string) file_get_contents($ficheros[0]));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $ticket);
    }

    public function test_al_emitir_se_borran_los_tickets_caducados(): void
    {
        (new FicheroLoginCliTicketRepository($this->dir, -1))->emitir('viejo', 'H-dlbv');
        touch((glob($this->dir . '/*') ?: [''])[0], time() - 3600);

        (new FicheroLoginCliTicketRepository($this->dir))->emitir('nuevo', 'H-dlbv');

        $this->assertCount(1, glob($this->dir . '/*') ?: []);
    }
}
