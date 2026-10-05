<?php

namespace src\personas\application\services;

use src\shared\config\ConfigGlobal;
use PDO;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use src\shared\infrastructure\GlobalPdo;
use src\ubis\domain\RegionStgrAviso;

/**
 * Servicio de aplicación para búsqueda de personas en múltiples esquemas y repositorios.
 */
class PersonaFinderService
{
    private PersonaDlRepositoryFactoryInterface $personaDlRepositoryFactory;
    private PersonaPubRepositoryInterface $personaPubRepository;
    private PersonaExRepositoryInterface $personaExRepository;
    private PersonaAllRepositoryInterface $personaAllRepository;
    private ?PDO $oDB = null;
    private ?PDO $oDBR = null;

    public function __construct(
        PersonaDlRepositoryFactoryInterface $personaDlRepositoryFactory,
        PersonaPubRepositoryInterface $personaPubRepository,
        PersonaExRepositoryInterface $personaExRepository,
        PersonaAllRepositoryInterface $personaAllRepository,
    ) {
        $this->personaDlRepositoryFactory = $personaDlRepositoryFactory;
        $this->personaPubRepository = $personaPubRepository;
        $this->personaExRepository = $personaExRepository;
        $this->personaAllRepository = $personaAllRepository;
    }

    private function oDB(): PDO
    {
        return $this->oDB ??= GlobalPdo::get('oDB');
    }

    private function oDBR(): PDO
    {
        return $this->oDBR ??= GlobalPdo::get('oDBR');
    }

    /**
     * @param array<string, mixed> $aWhere
     */
    private function findFirstPersonaDl(array $aWhere): ?PersonaDl
    {
        $personaDlRepository = $this->personaDlRepositoryFactory->create();
        $cPersonas = $personaDlRepository->getPersonas($aWhere);

        return $cPersonas[0] ?? null;
    }

    /**
     * Busca una persona por id_nom en el esquema dl (local).
     */
    public function findPersonaEnDl(int $id_nom): PersonaDl|PersonaPub|null
    {
        return $this->findFirstPersonaDl(['id_nom' => $id_nom, 'situacion' => 'A']);
    }

    /**
     * Busca una persona por id_nom: local y, si no, global.personas (caso A, sin exigir publicación).
     *
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     */
    public function findPersonaEnGlobal(int $id_nom, array &$problemasRegionStgr = [], ?int $id_schema = null): PersonaDl|PersonaPub|null
    {
        $persona = $this->findFirstPersonaDl(['id_nom' => $id_nom, 'situacion' => 'A']);
        if ($persona !== null) {
            return $persona;
        }

        $persona = $this->personaAllRepository->findByIdNomParaLookup($id_nom, $id_schema);
        if ($persona === null) {
            return null;
        }
        if ($persona->getSituacion() !== 'A') {
            return null;
        }

        return $persona;
    }

    /**
     * Lookup canónico por `id_nom` cuando puede ser de paso (id negativo).
     * {@see Persona::findPersonaEnGlobal()} y los listados delegan aquí.
     *
     * `p_de_paso_ex` no hereda de `global.personas`, así que
     * {@see findPersonaEnGlobal()} no encuentra a las personas de paso.
     *
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     */
    public function findPersonaEnGlobalODePaso(
        int $id_nom,
        array &$problemasRegionStgr = [],
        ?int $id_schema = null,
    ): PersonaDl|PersonaPub|PersonaEx|null {
        if ($id_nom < 0) {
            return $this->personaExRepository->findById($id_nom);
        }

        return $this->findPersonaEnGlobal($id_nom, $problemasRegionStgr, $id_schema);
    }

    /**
     * Como {@see findPersonaEnGlobal()}, pero si no hay situación 'A' acepta cualquier
     * situación (p. ej. tessera histórica de alguien ya no activo).
     *
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     */
    public function findPersonaEnGlobalIncluyendoNoActivos(
        int $id_nom,
        array &$problemasRegionStgr = [],
        ?int $id_schema = null,
    ): PersonaDl|PersonaPub|PersonaEx|null {
        if ($id_nom < 0) {
            return $this->findPersonaEnGlobalODePaso($id_nom, $problemasRegionStgr, $id_schema);
        }

        $persona = $this->findPersonaEnGlobal($id_nom, $problemasRegionStgr, $id_schema);
        if ($persona !== null) {
            return $persona;
        }

        $persona = $this->findFirstPersonaDl(['id_nom' => $id_nom]);
        if ($persona !== null) {
            return $persona;
        }

        return $this->personaAllRepository->findByIdNomParaLookup($id_nom, $id_schema);
    }

    /**
     * Búsqueda de persona para listados: global activa y, si falla por dl sin región stgr, pub.
     *
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     */
    public function findPersonaParaListado(
        int $id_nom,
        array &$problemasRegionStgr = [],
        bool &$marcaRegionStgr = false,
    ): PersonaDl|PersonaPub|PersonaEx|null {
        $marcaRegionStgr = false;
        if ($id_nom < 0) {
            return $this->findPersonaEnGlobalODePaso($id_nom, $problemasRegionStgr);
        }

        try {
            $persona = $this->findPersonaEnGlobal($id_nom, $problemasRegionStgr);
            if ($persona !== null) {
                return $persona;
            }
        } catch (\RuntimeException $e) {
            if (!RegionStgrAviso::esDlSinRegion($e)) {
                throw $e;
            }
            RegionStgrAviso::registrar($problemasRegionStgr, $e);
        }

        return $this->personaPubRepository->findByIdParaListado($id_nom, $problemasRegionStgr, $marcaRegionStgr);
    }

    /**
     * @return list<array{esquema: string, persona: PersonaDl|PersonaEx}>
     */
    public function buscarEnTodasRegiones(int $id_nom): array
    {
        $aWhere = [
            'situacion' => 'A',
            'id_nom' => $id_nom,
        ];

        $aResultados = [];

        foreach ($this->getPosiblesEsquemas() as $esquema) {
            $oDB = $this->oDB();
            $path_ini = $this->cambiarEsquema($esquema, $oDB);

            try {
                if ($esquema === 'restov') {
                    $resultado = $this->personaExRepository->getPersonas($aWhere);
                } else {
                    $personaDlRepository = $this->personaDlRepositoryFactory->createWithConnection($oDB);
                    $resultado = $personaDlRepository->getPersonas($aWhere);
                }

                foreach ($resultado as $persona) {
                    $aResultados[] = [
                        'esquema' => $esquema,
                        'persona' => $persona,
                    ];
                }
            } finally {
                $this->restaurarEsquema($oDB, $path_ini);
            }
        }

        return $aResultados;
    }

    /**
     * id_nom con ficha en algún esquema Aquinate presente en esta base
     * (`personas_dl`), salvo `resto`. Cualquier situación.
     *
     * Solo cuentan los esquemas cargados: en una instalación parcial no se
     * supone el resto de regiones.
     *
     * @param list<int> $idNoms
     * @return array<int, true>
     */
    public function idNomsEnEsquemasAquinate(array $idNoms): array
    {
        $ids = [];
        foreach ($idNoms as $idNom) {
            $idNom = (int) $idNom;
            if ($idNom > 0) {
                $ids[$idNom] = $idNom;
            }
        }
        if ($ids === []) {
            return [];
        }

        $esquemas = $this->esquemasConPersonasDl();
        if ($esquemas === []) {
            return [];
        }

        $in = implode(',', $ids);
        $unions = [];
        foreach ($esquemas as $esquema) {
            $quoted = '"' . str_replace('"', '""', $esquema) . '"';
            $unions[] = "SELECT id_nom FROM {$quoted}.personas_dl WHERE id_nom IN ($in)";
        }

        $sql = 'SELECT DISTINCT id_nom FROM (' . implode(' UNION ALL ', $unions) . ') u';
        $stmt = $this->oDBR()->query($sql);
        if ($stmt === false) {
            throw new \RuntimeException(_('No se pudo consultar las fichas de los esquemas cargados'));
        }

        $presentes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id_nom'])) {
                continue;
            }
            $presentes[(int) $row['id_nom']] = true;
        }

        return $presentes;
    }

    /**
     * Esquemas de esta base que tienen `personas_dl`, excepto resto.
     *
     * @return list<string>
     */
    private function esquemasConPersonasDl(): array
    {
        $stmt = $this->oDBR()->query(
            "SELECT n.nspname
             FROM pg_class c
             JOIN pg_namespace n ON n.oid = c.relnamespace
             WHERE c.relname = 'personas_dl'
               AND c.relkind = 'r'
               AND n.nspname NOT LIKE 'resto%'
               AND n.nspname NOT LIKE 'pg_%'
             ORDER BY n.nspname"
        );
        if ($stmt === false) {
            throw new \RuntimeException(_('No se pudo consultar las fichas de los esquemas cargados'));
        }

        $esquemas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nombre = $row['nspname'] ?? null;
            if (!is_string($nombre) || $nombre === '' || preg_match('/^[A-Za-z0-9_-]+$/', $nombre) !== 1) {
                continue;
            }
            $esquemas[] = $nombre;
        }

        return $esquemas;
    }

    /**
     * @return list<string>
     */
    private function getPosiblesEsquemas(): array
    {
        $qRs = $this->oDBR()->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        if ($qRs === false) {
            return [];
        }
        $aResultSql = $qRs->fetchAll(PDO::FETCH_ASSOC);

        $a_posibles = [];

        foreach ($aResultSql as $esquemaName) {
            if (!is_array($esquemaName) || !isset($esquemaName['schemaname']) || !is_string($esquemaName['schemaname'])) {
                continue;
            }
            $esquema = $esquemaName['schemaname'];

            if (strpos($esquema, '-') !== false) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1);
                if ($reg === $dl) {
                    continue;
                }
            }

            if (in_array($esquema, ['global', 'public', 'publicv'], true)) {
                continue;
            }

            $a_posibles[] = $esquema;
        }

        return $a_posibles;
    }

    private function cambiarEsquema(string $esquema, PDO &$oDB): string
    {
        if (ConfigGlobal::mi_region_dl() === $esquema) {
            $oDB = $this->oDB();
        } else {
            $oDB = $this->oDBR();
        }

        $qRs = $oDB->query('SHOW search_path');
        if ($qRs === false) {
            return '';
        }
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        $path_ini = is_array($aPath) && is_string($aPath['search_path'] ?? null) ? $aPath['search_path'] : '';

        $oDB->exec('SET search_path TO public,"' . $esquema . '"');

        return $path_ini;
    }

    private function restaurarEsquema(PDO $oDB, string $path_ini): void
    {
        $oDB->exec("SET search_path TO $path_ini");
    }
}
