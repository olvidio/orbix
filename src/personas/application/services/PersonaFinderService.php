<?php

namespace src\personas\application\services;

use src\shared\config\ConfigGlobal;
use PDO;
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
    private PDO $oDB;
    private PDO $oDBR;

    public function __construct(
        PersonaDlRepositoryFactoryInterface $personaDlRepositoryFactory,
        PersonaPubRepositoryInterface $personaPubRepository,
        PersonaExRepositoryInterface $personaExRepository,
    ) {
        $this->personaDlRepositoryFactory = $personaDlRepositoryFactory;
        $this->personaPubRepository = $personaPubRepository;
        $this->personaExRepository = $personaExRepository;
        $this->oDB = GlobalPdo::get('oDB');
        $this->oDBR = GlobalPdo::get('oDBR');
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
     * Busca una persona por id_nom en el esquema global (local).
     *
     * @param array<string, array<string, string>> $problemasRegionStgr
     * @param-out array<string, array<string, string>> $problemasRegionStgr
     */
    public function findPersonaEnGlobal(int $id_nom, array &$problemasRegionStgr = []): PersonaDl|PersonaPub|null
    {
        $persona = $this->findFirstPersonaDl(['id_nom' => $id_nom, 'situacion' => 'A']);
        if ($persona !== null) {
            return $persona;
        }

        $marcaAvisoRegionStgr = false;
        $persona = $this->personaPubRepository->findByIdParaListado($id_nom, $problemasRegionStgr, $marcaAvisoRegionStgr);
        if ($persona === null) {
            return null;
        }
        if ($persona->getSituacion() !== 'A') {
            return null;
        }

        return $persona;
    }

    /**
     * Búsqueda de persona para listados: global activa y, si falla por dl sin región stgr, pub.
     *
     * @param array<string, array<string, string>> $problemasRegionStgr
     * @param-out array<string, array<string, string>> $problemasRegionStgr
     */
    public function findPersonaParaListado(
        int $id_nom,
        array &$problemasRegionStgr = [],
        bool &$marcaRegionStgr = false,
    ): PersonaDl|PersonaPub|null {
        $marcaRegionStgr = false;
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
            $oDB = $this->oDB;
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
     * @return list<string>
     */
    private function getPosiblesEsquemas(): array
    {
        $qRs = $this->oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
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
            $oDB = $this->oDB;
        } else {
            $oDB = $this->oDBR;
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
