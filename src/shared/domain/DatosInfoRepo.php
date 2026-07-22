<?php

namespace src\shared\domain;

use src\shared\domain\contracts\DatosFichaInterface;
use src\shared\domain\contracts\DatosLookupRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;

/* No vale el underscore en el nombre */

abstract class DatosInfoRepo
{
    protected ?string $accion = null;
    /** @var class-string<DatosFichaInterface>|string|null */
    protected ?string $obj = null;

    protected ?string $txt_explicacion = null;
    protected ?string $txt_titulo = null;
    protected ?string $txt_eliminar = null;
    protected ?string $txt_buscar = null;
    protected ?string $metodo_gestor = null;

    protected mixed $id_pau = null;
    protected ?string $mod = null;
    /** @var array<string, mixed>|mixed */
    protected mixed $a_pkey = null;

    protected string $k_buscar = '';
    protected bool $exacto = false;

    protected ?string $pau = null;
    /** @var class-string|null */
    protected ?string $repoInterface = null;

    /**
     * Ruta relativa al namespace
     * Para sobreescribir por la clase que si tenga
     */
    public function getBuscar_view(): string
    {
        return '';
    }

    public function getBuscar_namespace(): string
    {
        return '';
    }

    public function addCamposFormBuscar(): string
    {
        return '';
    }

    /**
     * @param array<string, mixed> $a_campos
     * @return array<string, mixed>
     */
    public function addCampos(array $a_campos = []): array
    {
        return $a_campos;
    }

    public function getMetodoGestor(): ?string
    {
        return $this->metodo_gestor;
    }

    public function setMetodoGestor(?string $metodo_gestor): void
    {
        $this->metodo_gestor = $metodo_gestor;
    }

    public function setId_pau(mixed $id_pau): void
    {
        $this->id_pau = $id_pau;
    }

    public function setMod(?string $mod): void
    {
        $this->mod = $mod;
    }

    public function setA_pkey(mixed $a_pkey): void
    {
        $this->a_pkey = $a_pkey;
    }

    public function setK_buscar(string $k_buscar): void
    {
        $this->k_buscar = $k_buscar;
    }

    public function setTxtExplicacion(string $txt_explicacion = ''): void
    {
        $this->txt_explicacion = $txt_explicacion;
    }

    public function setTxtTitulo(string $txt_titulo = ''): void
    {
        $this->txt_titulo = $txt_titulo;
    }

    public function setTxtEliminar(string $txt_eliminar = ''): void
    {
        $this->txt_eliminar = $txt_eliminar;
    }

    public function setTxtBuscar(string $txt_buscar = ''): void
    {
        $this->txt_buscar = $txt_buscar;
    }

    public function getTxtExplicacion(): string
    {
        return $this->txt_explicacion ?? '';
    }

    public function getTxtTitulo(): string
    {
        return $this->txt_titulo ?? '';
    }

    public function getTxtEliminar(): string
    {
        return $this->txt_eliminar ?? '';
    }

    public function getTxtBuscar(): string
    {
        return $this->txt_buscar ?? '';
    }

    /**
     * @param class-string|null $repoInterface
     */
    public function setRepositoryInterface(?string $repoInterface): void
    {
        $this->repoInterface = $repoInterface;
    }

    /**
     * @return class-string|null
     */
    public function getRepositoryInterface(): ?string
    {
        return $this->repoInterface;
    }

    /**
     * @param class-string<DatosFichaInterface>|string $obj
     */
    public function setClase(string $obj): void
    {
        $this->obj = $obj;
    }

    /**
     * @return class-string<DatosFichaInterface>|string|null
     */
    public function getClase(): ?string
    {
        return $this->obj;
    }

    public function setPau(?string $pau): void
    {
        $this->pau = $pau;
    }

    public function getPau(): ?string
    {
        return $this->pau;
    }

    /**
     * @return array<string, mixed>|string
     */
    public function getKeyCollection(): array|string
    {
        $key_collection = '';
        if (isset($this->pau)) {
            switch ($this->pau) {
                case 'p':
                    $key_collection = ['id_nom' => $this->id_pau];
                    break;
                case 'a':
                    $key_collection = ['id_activ' => $this->id_pau];
                    break;
                case 'u':
                    $key_collection = ['id_ubi' => $this->id_pau];
                    break;
            }
        }

        return $key_collection;
    }

    /**
     * @return object|null
     */
    public function getFicha(): ?object
    {
        $obj = $this->obj;
        if ($obj === null || $obj === '') {
            return null;
        }

        $repositoryInterface = $this->getRepositoryInterface();
        if (empty($repositoryInterface)) {
            $repo = str_replace('domain\entity', 'domain\contracts', $obj);
            $repositoryInterface = $repo . 'RepositoryInterface';
        }
        if (!interface_exists($repositoryInterface) && !class_exists($repositoryInterface)) {
            return null;
        }

        $oFicha = null;
        /** @var DatosLookupRepositoryInterface $oRepository */
        $oRepository = DependencyResolver::get($repositoryInterface);

        switch ($this->mod) {
            case 'nuevo':
                /** @var DatosFichaInterface $oFicha */
                $oFicha = new $obj($this->getKeyCollection());
                break;
            case 'eliminar':
            case 'editar':
                $findByIdArgs = $this->resolveFindByIdArguments($this->a_pkey);
                if ($findByIdArgs !== []) {
                    /** @var DatosFichaInterface|null $oFicha */
                    $oFicha = $oRepository->findById(...$findByIdArgs);
                }
                break;
        }

        return $oFicha;
    }

    /**
     * Convierte a_pkey (escalar, array o stdClass desde json_decode) en argumentos para findById().
     *
     * @return list<mixed>
     */
    private function resolveFindByIdArguments(mixed $a_pkey): array
    {
        $a_pkey = $this->normalizeA_pkey($a_pkey);
        if ($a_pkey === null || $a_pkey === '' || $a_pkey === []) {
            return [];
        }
        if (is_object($a_pkey)) {
            $a_pkey = (array) $a_pkey;
        }
        if (is_array($a_pkey)) {
            if (array_key_exists('id_asignatura', $a_pkey)) {
                $args = [$this->normalizePkeyScalar($a_pkey['id_asignatura'])];
                if (array_key_exists('plan_estudios', $a_pkey)) {
                    $args[] = $this->normalizePlanEstudiosPkey($a_pkey['plan_estudios']);
                }

                return $args;
            }

            return array_map(
                fn (mixed $value): mixed => $this->normalizePkeyScalar($value),
                array_values($a_pkey)
            );
        }

        return [$this->normalizePkeyScalar($a_pkey)];
    }

    private function normalizeA_pkey(mixed $a_pkey): mixed
    {
        if (is_string($a_pkey) && $a_pkey !== '') {
            $decoded = json_decode($a_pkey, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $a_pkey;
    }

    /**
     * @return list<int>|int
     */
    private function normalizePlanEstudiosPkey(mixed $plan): array|int
    {
        if (is_array($plan)) {
            $normalized = array_values(array_map(
                static fn (mixed $item): int => is_numeric($item) ? (int) $item : 0,
                $plan
            ));
            sort($normalized);

            return $normalized;
        }
        if (is_string($plan) && str_starts_with($plan, '{')) {
            $parsed = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($plan);
            sort($parsed);

            return $parsed;
        }

        $scalar = $this->normalizePkeyScalar($plan);
        if (is_int($scalar)) {
            return $scalar;
        }
        if (is_array($scalar)) {
            /** @var list<int> $scalar */
            return $scalar;
        }

        return is_numeric($scalar) ? (int) $scalar : 0;
    }

    private function normalizePkeyScalar(mixed $value): mixed
    {
        if (is_object($value) && method_exists($value, 'value')) {
            $value = $value->value();
        }
        if (is_string($value) && preg_match('/^-?\d+$/', $value) === 1) {
            return (int) $value;
        }

        return $value;
    }

    public function getOpcionesParaCondicion(
        mixed $pKeyRepository,
        mixed $valor_depende,
        mixed $opcion_sel = null
    ): ?string {
        return null;
    }

    /**
     * @return array<string, string>
     */
    public function getArrayCamposDepende(): array
    {
        return [];
    }

    /**
     * Colección de filas para tablaDB (lista / buscar).
     *
     * @return iterable<DatosFichaInterface>
     */
    abstract public function getColeccion(): iterable;
}
