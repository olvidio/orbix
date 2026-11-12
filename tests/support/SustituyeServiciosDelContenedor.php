<?php

declare(strict_types=1);

namespace Tests\support;

/**
 * Para casos de uso que resuelven servicios con localizadores estáticos (`Persona::findPersonaEnGlobal()`,
 * `Ubi::NewUbi()`, `DependencyResolver::make()`): sustituye el servicio del contenedor por un doble y lo repone al
 * acabar el test.
 *
 * - `sustituirServicio(X::class, $doble)`: para lo que se obtiene con `get()`.
 * - `sustituirDefinicion(X::class, $doble)`: para lo que se crea con `make()` (p. ej. repositorios que necesitan un
 *   parámetro como el esquema): se sustituye la definición y al terminar se repone la del `dependencies.php` del
 *   módulo.
 *
 * Llamar a `reponerServicios()` en `tearDown()`.
 */
trait SustituyeServiciosDelContenedor
{
    /** @var array<class-string, object> */
    private array $serviciosOriginales = [];

    /** @var array<class-string, mixed> */
    private array $definicionesOriginales = [];

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

    /**
     * @param class-string $id
     */
    private function sustituirDefinicion(string $id, object $doble): void
    {
        if (!array_key_exists($id, $this->definicionesOriginales)) {
            $this->definicionesOriginales[$id] = self::definicionDeModulo($id);
        }
        $GLOBALS['container']->set($id, \DI\factory(static fn () => $doble));
    }

    private function reponerServicios(): void
    {
        foreach ($this->serviciosOriginales as $id => $original) {
            $GLOBALS['container']->set($id, $original);
        }
        foreach ($this->definicionesOriginales as $id => $definicion) {
            $GLOBALS['container']->set($id, $definicion);
        }
        $this->serviciosOriginales = [];
        $this->definicionesOriginales = [];
    }

    /**
     * @param class-string $id
     */
    private static function definicionDeModulo(string $id): mixed
    {
        foreach (glob(__DIR__ . '/../../src/*/config/dependencies.php') ?: [] as $fichero) {
            $definiciones = require $fichero;
            if (is_array($definiciones) && array_key_exists($id, $definiciones)) {
                return $definiciones[$id];
            }
        }

        throw new \RuntimeException("No hay definición de $id en ningún dependencies.php");
    }
}
