<?php

declare(strict_types=1);

namespace src\ubis\application;

use PDO;
use PDOException;
use RuntimeException;
use src\shared\application\copias\ReconciliadorCopia;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\ubis\domain\CuCentrosFila;
use src\ubis\infrastructure\persistence\postgresql\CuCentrosContexto;

/**
 * Parte común de las dos reconciliaciones de centros: leer `u_centros_dl` del
 * esquema de origen y quedarse con los centros del lado que toca.
 *
 * Hay dos copias porque hay dos orígenes —los centros de sv viven en la base sv
 * y los de sf en la base sf— y cada instalación sólo es dueña de los suyos. Todo
 * lo que las diferencia (base de origen, sufijo del esquema, tabla destino) sale
 * de {@see esCopiaDeSf()}; las subclases sólo declaran de qué lado son, y así el
 * contenedor puede inyectar a cada una su writer.
 */
abstract class ResincronizarCuCentros extends ReconciliadorCopia
{
    /** SQLSTATE de «la tabla no existe» / «el esquema no existe». */
    private const SQLSTATE_NO_EXISTE = ['42P01', '3F000'];

    /** ¿Reconcilia la copia de los centros de sf (`cu_centros_dlf`)? */
    abstract protected function esCopiaDeSf(): bool;

    protected function claveEsquemaOrigen(): string
    {
        return $this->esCopiaDeSf() ? 'publicf' : 'publicv';
    }

    protected function nombreBaseOrigen(): string
    {
        return $this->esCopiaDeSf() ? 'sf' : 'sv';
    }

    /** Los esquemas de sv acaban en `v` y los de sf en `f`: `H-dlb` → `H-dlbv` / `H-dlbf`. */
    protected function esquemaOrigenDe(string $esquemaComun): string
    {
        return $esquemaComun . ($this->esCopiaDeSf() ? 'f' : 'v');
    }

    /** Esta copia nunca omite un esquema por el contexto: no depende de la dl. */
    protected function contextoDe(PDO $pdoComun, string $esquema, int $id_schema): ContextoCopia
    {
        return $this->esCopiaDeSf()
            ? CuCentrosContexto::paraSf($pdoComun, $esquema, $id_schema)
            : CuCentrosContexto::paraSv($pdoComun, $esquema, $id_schema);
    }

    /**
     * Centros que **deberían** estar en esta copia para este esquema.
     *
     * El origen de cada instalación sólo tiene sus propios centros, pero el
     * filtro por prefijo de `id_ubi` se aplica igualmente: si en un esquema
     * apareciera un centro del otro lado, iría a la otra copia, no a esta.
     *
     * @return array<int, array<string, mixed>> id_ubi => fila
     */
    protected function leerOrigen(PDO $pdoOrigen, string $esquemaOrigen, ContextoCopia $contexto): array
    {
        $filas = [];
        foreach ($this->consultarOrigen($pdoOrigen, $esquemaOrigen) as $registro) {
            $fila = CuCentrosFila::desdeRegistro($registro);
            if (CuCentrosFila::debeCopiarse($fila, $this->esCopiaDeSf())) {
                $filas[CuCentrosFila::idUbi($fila)] = $fila;
            }
        }

        return $filas;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function consultarOrigen(PDO $pdo, string $esquema): array
    {
        $sql = 'SELECT ' . implode(',', CuCentrosFila::COLUMNAS)
            . ' FROM ' . ContextoCopia::comillas($esquema)
            . '.' . ContextoCopia::comillas(CuCentrosFila::TABLA_ORIGEN);

        try {
            $stmt = $pdo->query($sql);
            if ($stmt === false) {
                throw new RuntimeException('no se ha podido leer ' . CuCentrosFila::TABLA_ORIGEN);
            }

            $registros = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                if (!is_array($registro)) {
                    continue;
                }
                $normalizado = [];
                foreach ($registro as $clave => $valor) {
                    $normalizado[(string) $clave] = $valor;
                }
                $registros[] = $normalizado;
            }

            return $registros;
        } catch (PDOException $e) {
            // Tabla o esquema inexistentes: marcar el esquema como error (no
            // tratarlo como origen vacío, que en --aplicar borraría la copia entera).
            if (in_array((string) $e->getCode(), self::SQLSTATE_NO_EXISTE, true)) {
                throw new RuntimeException(
                    sprintf(
                        'no existe %s.%s en %s',
                        $esquema,
                        CuCentrosFila::TABLA_ORIGEN,
                        $this->nombreBaseOrigen(),
                    ),
                    0,
                    $e,
                );
            }
            throw $e;
        }
    }
}
