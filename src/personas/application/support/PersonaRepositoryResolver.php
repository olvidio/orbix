<?php

namespace src\personas\application\support;

use src\shared\infrastructure\ProvidesRepositories;

/**
 * Helper transversal para resolver el repositorio de una persona
 * a partir de su `obj_pau` (PersonaN, PersonaAgd, ...) o de su
 * `id_tabla` (n, a, s, sssc, pn, pa, x, cp_sss).
 *
 * Centraliza la clase anonima `new class { use ProvidesRepositories; }`
 * que estaba copiada en 6 controllers del modulo (`home_persona`,
 * `personas_editar`, `personas_update`, `stgr_cambio`, `stgr_update`,
 * `traslado_update`) junto con el mapa `id_tabla -> obj_pau`.
 */
final class PersonaRepositoryResolver
{
    use ProvidesRepositories {
        getRepository as private getRepositoryFromTrait;
    }

    /**
     * Mapa `id_tabla` -> `obj_pau`.
     *
     * Mantiene todos los alias usados historicamente por el modulo
     * (incluyendo `cp_sss` como sinonimo de `sssc` y `pa`/`pn` como
     * dos caras de `PersonaEx`).
     *
     * @return array<string,string>
     */
    public static function entityTypeByIdTabla(): array
    {
        return [
            'n' => 'PersonaN',
            'x' => 'PersonaNax',
            'a' => 'PersonaAgd',
            's' => 'PersonaS',
            'sssc' => 'PersonaSSSC',
            'cp_sss' => 'PersonaSSSC',
            'pn' => 'PersonaEx',
            'pa' => 'PersonaEx',
        ];
    }

    /**
     * Mapa inverso `obj_pau` -> `id_tabla` canonico (no cubre los alias).
     *
     * @return array<string,string>
     */
    public static function idTablaByObjPau(): array
    {
        return [
            'PersonaN' => 'n',
            'PersonaNax' => 'x',
            'PersonaAgd' => 'a',
            'PersonaS' => 's',
            'PersonaSSSC' => 'sssc',
            'PersonaEx' => 'pn',
        ];
    }

    /**
     * @throws \InvalidArgumentException si `$obj_pau` no es una persona conocida.
     */
    public function repositorio(string $obj_pau): object
    {
        return $this->getRepositoryFromTrait($obj_pau);
    }

    /**
     * Resuelve el repositorio a partir del `id_tabla`.
     *
     * @throws \InvalidArgumentException si `$id_tabla` no es reconocido.
     */
    public function repositorioPorIdTabla(string $id_tabla): object
    {
        $map = self::entityTypeByIdTabla();
        if (!isset($map[$id_tabla])) {
            throw new \InvalidArgumentException("id_tabla '$id_tabla' no reconocido");
        }
        return $this->getRepositoryFromTrait($map[$id_tabla]);
    }

    /**
     * `id_tabla` canonico asociado a un `obj_pau`.
     *
     * @throws \InvalidArgumentException si `$obj_pau` no es una persona conocida.
     */
    public static function idTablaFor(string $obj_pau): string
    {
        $map = self::idTablaByObjPau();
        if (!isset($map[$obj_pau])) {
            throw new \InvalidArgumentException("obj_pau '$obj_pau' no reconocido");
        }
        return $map[$obj_pau];
    }
}
