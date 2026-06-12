<?php

namespace src\personas\application\support;

use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;

/**
 * Helper transversal para resolver el repositorio de una persona
 * a partir de su `obj_pau` (PersonaN, PersonaAgd, ...) o de su
 * `id_tabla` (n, a, s, sssc, pn, pa, x, cp_sss, dl, cp).
 */
final class PersonaRepositoryResolver
{
    public function __construct(
        private PersonaNRepositoryInterface $personaNRepository,
        private PersonaAgdRepositoryInterface $personaAgdRepository,
        private PersonaNaxRepositoryInterface $personaNaxRepository,
        private PersonaSRepositoryInterface $personaSRepository,
        private PersonaSSSCRepositoryInterface $personaSSSCRepository,
        private PersonaExRepositoryInterface $personaExRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
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
            'dl' => 'PersonaDl',
            'cp' => 'PersonaSacd',
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
            'PersonaDl' => 'dl',
            'PersonaSacd' => 'cp',
        ];
    }

    /**
     * @throws \InvalidArgumentException si `$obj_pau` no es una persona conocida.
     */
    public function repositorio(string $obj_pau): PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface
    {
        return match ($obj_pau) {
            'PersonaN' => $this->personaNRepository,
            'PersonaAgd' => $this->personaAgdRepository,
            'PersonaNax' => $this->personaNaxRepository,
            'PersonaS' => $this->personaSRepository,
            'PersonaSSSC' => $this->personaSSSCRepository,
            'PersonaEx' => $this->personaExRepository,
            'PersonaDl' => $this->personaDlRepository,
            'PersonaSacd' => $this->personaSacdRepository,
            default => throw new \InvalidArgumentException("obj_pau '$obj_pau' no reconocido"),
        };
    }

    public function personaNRepository(): PersonaNRepositoryInterface
    {
        return $this->personaNRepository;
    }

    public function personaAgdRepository(): PersonaAgdRepositoryInterface
    {
        return $this->personaAgdRepository;
    }

    public function personaNaxRepository(): PersonaNaxRepositoryInterface
    {
        return $this->personaNaxRepository;
    }

    public function personaSRepository(): PersonaSRepositoryInterface
    {
        return $this->personaSRepository;
    }

    public function personaSSSCRepository(): PersonaSSSCRepositoryInterface
    {
        return $this->personaSSSCRepository;
    }

    public function personaExRepository(): PersonaExRepositoryInterface
    {
        return $this->personaExRepository;
    }

    public function personaDlRepository(): PersonaDlRepositoryInterface
    {
        return $this->personaDlRepository;
    }

    public function personaSacdRepository(): PersonaSacdRepositoryInterface
    {
        return $this->personaSacdRepository;
    }

    /**
     * Resuelve el repositorio a partir del `id_tabla`.
     *
     * @throws \InvalidArgumentException si `$id_tabla` no es reconocido.
     */
    public function repositorioPorIdTabla(string $id_tabla): PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|PersonaExRepositoryInterface|PersonaDlRepositoryInterface|PersonaSacdRepositoryInterface
    {
        $map = self::entityTypeByIdTabla();
        if (!isset($map[$id_tabla])) {
            throw new \InvalidArgumentException("id_tabla '$id_tabla' no reconocido");
        }
        return $this->repositorio($map[$id_tabla]);
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
