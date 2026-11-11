<?php

declare(strict_types=1);

namespace Tests\support;

/**
 * Para casos de uso que resuelven servicios con localizadores estáticos (`Persona::findPersonaEnGlobal()`,
 * `Ubi::NewUbi()`): sustituye el servicio del contenedor por un doble y lo repone al acabar el test.
 *
 * Uso: `$this->sustituirServicio(X::class, $doble)` en el test y `$this->reponerServicios()` en `tearDown()`.
 */
trait SustituyeServiciosDelContenedor
{
    /** @var array<class-string, object> */
    private array $serviciosOriginales = [];

    /**
     * @param class-string $id
     */
    private function sustituirServicio(string $id, object $doble): void
    {
        if (!array_key_exists($id, $this->serviciosOriginales)) {
            $this->serviciosOriginales[$id] = $GLOBALS['container']->get($id);
        }
        $GLOBALS['container']->set($id, $doble);
    }

    private function reponerServicios(): void
    {
        foreach ($this->serviciosOriginales as $id => $original) {
            $GLOBALS['container']->set($id, $original);
        }
        $this->serviciosOriginales = [];
    }
}
