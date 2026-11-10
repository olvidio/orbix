<?php

declare(strict_types=1);

namespace Tests\unit\ubis\support;

use PHPUnit\Framework\TestCase;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;

/** Construye un `UbiRepositoryResolver` (final) con repositorios simulados; se sustituye el que interese. */
final class UbiResolverFactory
{
    /**
     * @param array<class-string, object> $repos interfaz => repositorio que sustituye al simulado por defecto
     */
    public static function resolver(TestCase $test, array $repos = []): UbiRepositoryResolver
    {
        $dame = static function (string $interfaz) use ($test, $repos): object {
            return $repos[$interfaz] ?? (new \ReflectionMethod($test, 'createMock'))->invoke($test, $interfaz);
        };

        return new UbiRepositoryResolver(
            $dame(CentroRepositoryInterface::class),
            $dame(CentroDlRepositoryInterface::class),
            $dame(CentroExRepositoryInterface::class),
            $dame(CasaRepositoryInterface::class),
            $dame(CasaDlRepositoryInterface::class),
            $dame(CasaExRepositoryInterface::class),
            $dame(TelecoCtrRepositoryInterface::class),
            $dame(TelecoCtrDlRepositoryInterface::class),
            $dame(TelecoCtrExRepositoryInterface::class),
            $dame(TelecoCdcRepositoryInterface::class),
            $dame(TelecoCdcDlRepositoryInterface::class),
            $dame(TelecoCdcExRepositoryInterface::class),
            $dame(DireccionCentroRepositoryInterface::class),
            $dame(DireccionCentroDlRepositoryInterface::class),
            $dame(DireccionCentroExRepositoryInterface::class),
            $dame(DireccionCasaRepositoryInterface::class),
            $dame(DireccionCasaDlRepositoryInterface::class),
            $dame(DireccionCasaExRepositoryInterface::class),
            $dame(RelacionCentroDireccionRepositoryInterface::class),
            $dame(RelacionCentroDlDireccionRepositoryInterface::class),
            $dame(RelacionCentroExDireccionRepositoryInterface::class),
            $dame(RelacionCasaDireccionRepositoryInterface::class),
            $dame(RelacionCasaDlDireccionRepositoryInterface::class),
            $dame(RelacionCasaExDireccionRepositoryInterface::class),
        );
    }
}
