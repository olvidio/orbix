<?php

declare(strict_types=1);

namespace Tests\unit\frontend\security;

use frontend\shared\security\HashF;
use PHPUnit\Framework\TestCase;

/**
 * La firma `h` de HashF la calcula solo el servidor (clave de la sesión, no el id de la cookie) y deja
 * fijados el modo de firma y las listas `hno` / `hchk` / `hhc`: el cliente no puede añadir campos a una
 * petición firmada ni presentar una firma de un modo como si fuera de otro.
 */
final class HashFFirmaTest extends TestCase
{
    /** Url de la pantalla de prueba, con el host que usa esta instalación al firmar. */
    private string $url = '';

    /** @var array<string, mixed> */
    private array $serverPrevio = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('hashf-firma-test');
        session_start();
        $this->serverPrevio = $_SERVER;
        // El host con el que se firma sale de la configuración: la petición simulada llega a ese mismo host.
        $_SERVER['REQUEST_SCHEME'] = 'http';
        $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'orbix.local';
        $_SERVER['SERVER_PORT'] = '';
        $this->url = (string) strtok(HashF::link('http://orbix.local/orbix/frontend/zz/controller/zz_lista.php'), '?');
        $partes = parse_url($this->url);
        $host = (string) ($partes['host'] ?? '');
        $puerto = isset($partes['port']) ? (string) $partes['port'] : '';
        $_SERVER['REQUEST_SCHEME'] = (string) ($partes['scheme'] ?? 'http');
        $_SERVER['HTTP_HOST'] = $host . ($puerto !== '' ? ':' . $puerto : '');
        $_SERVER['SERVER_NAME'] = $host;
        $_SERVER['SERVER_ADDR'] = '10.0.0.1';
        $_SERVER['SERVER_PORT'] = $puerto;
        $_SERVER['REQUEST_URI'] = (string) ($partes['path'] ?? '');
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverPrevio;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        parent::tearDown();
    }

    public function test_el_formulario_firmado_se_acepta(): void
    {
        $this->assertTrue((new HashF())->firmaValida($this->postDelFormulario()));
    }

    public function test_la_firma_no_se_puede_calcular_con_el_id_de_sesion(): void
    {
        $post = $this->postDelFormulario();

        // Antes: md5(lista de campos + id de sesión + sufijo fijo), que el usuario podía calcular.
        $this->assertNotSame(md5('id_nom=&nom=&obj_pau=&publico=' . session_id() . 'a+front+'), $post['h']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', (string) $post['h']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', (string) $post['hh']);
    }

    public function test_no_se_puede_anadir_un_campo_con_hno(): void
    {
        $post = $this->postDelFormulario();
        $post['id_usuario'] = '7';
        $post['hno'] = 'id_usuario';

        $this->assertFalse((new HashF())->firmaValida($post));
    }

    public function test_no_se_puede_sacar_un_oculto_de_la_firma_cambiando_hhc(): void
    {
        $post = $this->postDelFormulario();
        // El cliente recalcula nada: solo quita `id_nom` de la lista de ocultos y lo nombra en hno.
        $post['hhc'] = 'obj_pau';
        $post['id_nom'] = '999';

        $this->assertFalse((new HashF())->firmaValida($post));
    }

    public function test_no_se_puede_declarar_un_campo_como_casilla(): void
    {
        $post = $this->postDelFormulario();
        unset($post['nom']);
        $post['hchk'] = 'publico!nom';

        $this->assertFalse((new HashF())->firmaValida($post));
    }

    public function test_el_enlace_firmado_se_acepta_y_no_admite_campos_anadidos(): void
    {
        $query = (string) parse_url(HashF::link($this->url . '?id_nom=21&que=ver'), PHP_URL_QUERY);
        parse_str($query, $get);
        $this->assertTrue((new HashF())->firmaValida($get));

        $get['permiso'] = '3';
        $get['hno'] = 'permiso';
        $this->assertFalse((new HashF())->firmaValida($get));
    }

    public function test_una_firma_de_enlace_no_vale_como_firma_sin_valores(): void
    {
        // Firma con los valores (link): no puede presentarse como «solo nombres» para cambiar el id.
        $query = (string) parse_url(HashF::link($this->url . '?id_nom=21'), PHP_URL_QUERY);
        parse_str($query, $get);
        $oHash = new HashF();
        $oHash->setUrl($this->url);
        $oHash->setCamposForm('id_nom');
        parse_str(ltrim($oHash->linkSinVal(), '?&'), $sinVal);
        $this->assertTrue((new HashF())->firmaValida(['id_nom' => '5'] + $sinVal));

        $this->assertNotSame($get['h'], $sinVal['h']);
        $this->assertFalse((new HashF())->firmaValida(['id_nom' => '5', 'hnov' => '1', 'h' => $get['h']]));
    }

    public function test_la_vuelta_atras_firmada_se_acepta_y_no_admite_hno(): void
    {
        parse_str(HashF::add_hash(['id_nom' => '21'], $this->url), $post);
        $this->assertTrue((new HashF())->firmaValida($post));

        $post['otro'] = 'x';
        $post['hno'] = 'otro';
        $this->assertFalse((new HashF())->firmaValida($post));
    }

    public function test_la_firma_en_la_url_se_comprueba_con_los_datos_del_cuerpo(): void
    {
        // `$url . linkSinVal()` con los datos por POST: antes esa firma no se miraba.
        $oHash = new HashF();
        $oHash->setUrl($this->url);
        $oHash->setCamposForm('id_activ!seleccion');
        parse_str(ltrim($oHash->linkSinVal(), '?&'), $enLaUrl);
        $getPrevio = $_GET;
        $postPrevio = $_POST;
        try {
            $_GET = $enLaUrl + ['r' => '/src/zz/zz_lista', 'dir' => 'x'];
            $_POST = ['id_activ' => '3001', 'seleccion' => '2'];
            $this->assertTrue((new HashF())->firmaValida(HashF::datosDeLaPeticion()));

            $_POST['id_nom'] = '21';
            $this->assertFalse((new HashF())->firmaValida(HashF::datosDeLaPeticion()));
        } finally {
            $_GET = $getPrevio;
            $_POST = $postPrevio;
        }
    }

    public function test_el_formulario_publica_la_lista_de_campos_firmados(): void
    {
        // La usa la auditoría E2E de formularios, que ya no puede recalcular la firma.
        $this->assertMatchesRegularExpression(
            '/name="h"[^>]*data-hc="id_nom=&amp;nom=&amp;obj_pau=&amp;publico="/',
            $this->formulario()->getCamposHtml()
        );
    }

    private function formulario(): HashF
    {
        $oHash = new HashF();
        $oHash->setCamposForm('nom');
        $oHash->setCamposChk('publico');
        $oHash->setArrayCamposHidden(['obj_pau' => 'PersonaN', 'id_nom' => 21]);

        return $oHash;
    }

    /** @return array<string, mixed> el POST que enviaría el navegador con el formulario sin tocar */
    private function postDelFormulario(): array
    {
        preg_match_all('/<input[^>]*\bname="([^"]+)"[^>]*\bvalue="([^"]*)"/i', $this->formulario()->getCamposHtml(), $m, PREG_SET_ORDER);
        $post = ['nom' => 'Pere'];
        foreach ($m as $campo) {
            $post[$campo[1]] = html_entity_decode($campo[2], ENT_QUOTES, 'UTF-8');
        }

        return $post;
    }
}
