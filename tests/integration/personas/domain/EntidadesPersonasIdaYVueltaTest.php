<?php

declare(strict_types=1);

namespace Tests\integration\personas\domain;

use PHPUnit\Framework\Attributes\DataProvider;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;

/**
 * Ida y vuelta de los campos de las entidades de personas con una fila real de
 * cada tabla: para cada par setXVo()/getXVo(), volver a poner el valor como VO
 * y como texto no lo cambia, y cada getX() simple devuelve el mismo valor que
 * getXVo()->value(). Detecta setters que convierten el texto en otro VO o
 * getters que leen otro campo. Solo lee de la BD.
 */
final class EntidadesPersonasIdaYVueltaTest extends myTest
{
    public static function providerRepositorios(): \Generator
    {
        yield 'numerario' => [PersonaNRepositoryInterface::class];
        yield 'agregado' => [PersonaAgdRepositoryInterface::class];
        yield 'sacd' => [PersonaSacdRepositoryInterface::class];
        yield 'de paso' => [PersonaExRepositoryInterface::class];
        yield 'publicada' => [PersonaPubRepositoryInterface::class];
        yield 'teleco' => [TelecoPersonaDlRepositoryInterface::class];
        yield 'traslado' => [TrasladoRepositoryInterface::class];
        yield 'situación' => [SituacionRepositoryInterface::class];
        yield 'última asistencia' => [UltimaAsistenciaRepositoryInterface::class];
    }

    #[DataProvider('providerRepositorios')]
    public function test_ida_y_vuelta_de_los_value_objects(string $repoId): void
    {
        $filas = $GLOBALS['container']->get($repoId)->matching(Criteria::create()->limit(3));
        if ($filas === []) {
            $this->markTestSkipped("La BD de desarrollo no tiene filas para $repoId");
        }
        $comprobados = 0;
        foreach ($filas as $entidad) {
            $comprobados += $this->comprobar($entidad);
        }
        $this->assertGreaterThan(0, $comprobados, 'la entidad tiene pares setXVo/getXVo');
    }

    private function comprobar(object $entidad): int
    {
        $ref = new \ReflectionObject($entidad);
        $n = 0;
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $set) {
            if (!preg_match('/^set(\w+)Vo$/', $set->getName(), $m) || $set->getNumberOfParameters() !== 1) {
                continue;
            }
            $getName = 'get' . $m[1] . 'Vo';
            if (!$ref->hasMethod($getName) || $ref->getMethod($getName)->getNumberOfRequiredParameters() > 0) {
                continue;
            }
            $campo = get_class($entidad) . '::' . $m[1];
            try {
                $vo = $entidad->$getName();
            } catch (\Error) {
                continue; // propiedad sin inicializar en esta fila
            }
            $valor = (is_object($vo) && method_exists($vo, 'value')) ? $vo->value() : $vo;

            $entidad->{$set->getName()}($vo);
            $this->assertEquals($valor, self::valorDe($entidad->$getName()), "$campo: con el VO");

            if (is_string($valor) || is_int($valor)) {
                $tipo = $set->getParameters()[0]->getType();
                $admite = $tipo === null || str_contains((string) $tipo, is_int($valor) ? 'int' : 'string');
                if ($admite) {
                    $entidad->{$set->getName()}($valor);
                    $this->assertEquals($valor, self::valorDe($entidad->$getName()), "$campo: con el valor «{$valor}»");
                }
            }

            $simple = 'get' . $m[1];
            if ($ref->hasMethod($simple) && $ref->getMethod($simple)->getNumberOfRequiredParameters() === 0 && (is_scalar($valor) || $valor === null)) {
                $this->assertEquals($valor, $entidad->$simple(), "$campo: getX() y getXVo() coinciden");
            }
            $n++;
        }
        return $n;
    }

    private static function valorDe(mixed $vo): mixed
    {
        return (is_object($vo) && method_exists($vo, 'value')) ? $vo->value() : $vo;
    }
}
