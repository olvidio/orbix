<?php

declare(strict_types=1);

namespace Tests\unit\frontend\personas;

use frontend\shared\security\HashF;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Guardar persona POSTea a `src_ajax.php`, que valida HashF antes de llegar
 * a `persona_update`. Los `ctx_*` HashB tienen que ir en campos hidden de
 * HashF; si viajan como inputs extra, el hash de nombres no coincide y
 * HashF redirige a `index.php` (HTML). jQuery `dataType:'json'` acaba en
 * `SyntaxError: JSON.parse: unexpected character at line 1 column 1`.
 */
final class PersonaFormHashFCtxTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        session_id('persona-form-hashf-ctx-test');
        session_start();
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        parent::tearDown();
    }

    public function test_getCamposHtml_incluye_ctx_update_y_ctx_eliminar_en_hhc(): void
    {
        $html = $this->hashFPersona(true)->getCamposHtml();

        $this->assertStringContainsString('name="ctx_update"', $html);
        $this->assertStringContainsString('name="ctx_eliminar"', $html);
        $this->assertMatchesRegularExpression('/name="hhc"[^>]*value="[^"]*ctx_update/', $html);
        $this->assertMatchesRegularExpression('/name="hhc"[^>]*value="[^"]*ctx_eliminar/', $html);
    }

    public function test_validatePost_acepta_el_serialize_con_ctx_en_hidden(): void
    {
        $post = $this->postComoSerialize($this->hashFPersona(true)->getCamposHtml());

        (new HashF())->validatePost($post);

        $this->assertSame('capsule-update', $post['ctx_update']);
        $this->assertSame('capsule-eliminar', $post['ctx_eliminar']);
    }

    public function test_ctx_fuera_de_hashf_cambia_el_hash_de_nombres_del_form(): void
    {
        $post = $this->postComoSerialize($this->hashFPersona(false)->getCamposHtml());
        $hFirmado = (string) $post['h'];

        $this->assertSame($hFirmado, $this->hashNombresComoValidatePost($post));

        $post['ctx_update'] = 'capsule-update';
        $post['ctx_eliminar'] = 'capsule-eliminar';

        $this->assertNotSame(
            $hFirmado,
            $this->hashNombresComoValidatePost($post),
            'Campos ctx_* extra deben romper HashF (redirect HTML, no JSON).'
        );
    }

    private function hashFPersona(bool $conCtx): HashF
    {
        $oHash = new HashF();
        $campos_chk = 'sacd';
        $oHash->setCamposForm(
            'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin'
        );
        $oHash->setCamposNo($campos_chk);
        $hidden = [
            'campos_chk' => $campos_chk,
            'obj_pau' => 'PersonaN',
            'id_nom' => 21,
        ];
        if ($conCtx) {
            $hidden['ctx_update'] = 'capsule-update';
            $hidden['ctx_eliminar'] = 'capsule-eliminar';
        }
        $oHash->setArrayCamposHidden($hidden);

        return $oHash;
    }

    /**
     * @return array<string, string>
     */
    private function postComoSerialize(string $html): array
    {
        $post = $this->parseHiddenInputs($html);
        foreach ($this->camposFormPersonaN() as $campo) {
            if (!isset($post[$campo])) {
                $post[$campo] = '';
            }
        }

        return $post;
    }

    /**
     * @return list<string>
     */
    private function camposFormPersonaN(): array
    {
        return explode(
            '!',
            'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin'
        );
    }

    /**
     * @return array<string, string>
     */
    private function parseHiddenInputs(string $html): array
    {
        preg_match_all('/<input[^>]*\bname="([^"]+)"[^>]*\bvalue="([^"]*)"/i', $html, $matches, PREG_SET_ORDER);
        $post = [];
        foreach ($matches as $match) {
            $post[$match[1]] = html_entity_decode($match[2], ENT_QUOTES, 'UTF-8');
        }

        return $post;
    }

    /**
     * Reproduce el segundo chequeo de HashF::validatePost (nombres del form).
     *
     * @param array<string, mixed> $aPOST
     */
    private function hashNombresComoValidatePost(array $aPOST): string
    {
        $hno = (string) ($aPOST['hno'] ?? '');
        if ($hno !== '') {
            foreach (explode('!', $hno) as $campo) {
                unset($aPOST[$campo]);
            }
        }
        unset(
            $aPOST['PHPSESSID'],
            $aPOST['atras'],
            $aPOST['h'],
            $aPOST['horig'],
            $aPOST['hh'],
            $aPOST['hhc'],
            $aPOST['hhorig'],
            $aPOST['hno'],
            $aPOST['hchk'],
            $aPOST['hnov'],
        );

        $method = new ReflectionMethod(HashF::class, 'getHashArray');
        /** @var array{hash: string, orig: string} $rta */
        $rta = $method->invoke(null, $aPOST, 1);

        return $rta['hash'];
    }
}
