<?php

namespace src\personas\application\services;

use core\ConfigGlobal;
use PDO;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaGlobal;

/**
 * Servicio de aplicación para búsqueda de personas en múltiples esquemas y repositorios.
 *
 * Este servicio orquesta las búsquedas de personas a través de diferentes repositorios
 * (Dl, Pub, Ex) y esquemas de base de datos, encapsulando la lógica compleja de búsqueda
 * que anteriormente estaba en la clase de dominio Persona.
 */
class PersonaFinderService
{
    private PersonaDlRepositoryInterface $personaDlRepository;
    private PersonaPubRepositoryInterface $personaPubRepository;
    private PersonaExRepositoryInterface $personaExRepository;
    private PDO $oDB;
    private PDO $oDBR;

    public function __construct(
        PersonaDlRepositoryInterface $personaDlRepository,
        PersonaPubRepositoryInterface $personaPubRepository,
        PersonaExRepositoryInterface $personaExRepository,
    ) {
        $this->personaDlRepository = $personaDlRepository;
        $this->personaPubRepository = $personaPubRepository;
        $this->personaExRepository = $personaExRepository;
        $this->oDB = $GLOBALS['oDB'];
        $this->oDBR = $GLOBALS['oDBR'];
    }

    /**
     * Busca una persona por id_nom en el esquema global (local).
     *
     * Busca primero en PersonaDl, luego en PersonaPub.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return PersonaGlobal|null La persona encontrada o null
     */
    public function findPersonaEnGlobal(int $id_nom): ?PersonaGlobal
    {
        $aWhere = ['id_nom' => $id_nom, 'situacion' => 'A'];

        // Buscar primero en PersonaDl
        $cPersonas = $this->personaDlRepository->getPersonasDl($aWhere);
        if (count($cPersonas) > 0 && $cPersonas[0] !== null) {
            return $cPersonas[0];
        }

        // Buscar en PersonaPub
        $cPersonas = $this->personaPubRepository->getPersonas($aWhere);
        if (count($cPersonas) > 0 && $cPersonas[0] !== null) {
            return $cPersonas[0];
        }

        return null;
    }

    /**
     * Busca una persona en todas las regiones/esquemas disponibles.
     *
     * Itera por todos los esquemas de base de datos y busca la persona
     * en cada uno, cambiando temporalmente el search_path de PostgreSQL.
     *
     * @param int $id_nom ID de la persona a buscar
     * @return array Array de PersonaGlobal encontradas
     */
    public function buscarEnTodasRegiones(int $id_nom): array
    {
        $aWhere = [
            'situacion' => 'A',
            'id_nom' => $id_nom
        ];

        $aResultados = [];

        foreach ($this->getPosiblesEsquemas() as $esquema) {
            $path_ini = $this->cambiarEsquema($esquema, $oDB);

            try {
                if ($esquema === 'restov') {
                    $resultado = $this->personaExRepository->getPersonas($aWhere);
                } else {
                    $this->personaDlRepository->setoDbl($oDB);
                    $resultado = $this->personaDlRepository->getPersonasDl($aWhere);
                }

                if (!empty($resultado)) {
                    $aResultados[] = $resultado;
                }
            } finally {
                $this->restaurarEsquema($oDB, $path_ini);
            }
        }

        return !empty($aResultados) ? array_merge(...$aResultados) : [];
    }

    /**
     * Obtiene una lista de esquemas válidos para buscar personas.
     *
     * Excluye esquemas como 'global', 'public', y esquemas H-H (misma región-delegación).
     *
     * @return array Lista de nombres de esquemas
     */
    private function getPosiblesEsquemas(): array
    {
        $qRs = $this->oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        $aResultSql = $qRs->fetchAll(PDO::FETCH_ASSOC);

        $a_posibles = [];

        foreach ($aResultSql as $esquemaName) {
            $esquema = $esquemaName['schemaname'];

            // Eliminar esquemas H-H (misma región-delegación)
            if (strpos($esquema, '-') !== false) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1); // quitar la 'v' o 'f'
                if ($reg === $dl) {
                    continue;
                }
            }

            // Eliminar esquemas especiales
            if (in_array($esquema, ['global', 'public', 'publicv'], true)) {
                continue;
            }

            $a_posibles[] = $esquema;
        }

        return $a_posibles;
    }

    /**
     * Cambia el search_path de PostgreSQL al esquema especificado.
     *
     * @param string $esquema Nombre del esquema a establecer
     * @param PDO &$oDB Referencia al objeto PDO que se usará
     * @return string El search_path original (para restaurarlo después)
     */
    private function cambiarEsquema(string $esquema, PDO &$oDB): string
    {
        // Usar oDB para el esquema local, oDBR para otros esquemas
        if (ConfigGlobal::mi_region_dl() === $esquema) {
            $oDB = $this->oDB;
        } else {
            $oDB = $this->oDBR;
        }

        $qRs = $oDB->query('SHOW search_path');
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        $path_ini = $aPath['search_path'];

        $oDB->exec('SET search_path TO public,"' . $esquema . '"');

        return $path_ini;
    }

    /**
     * Restaura el search_path de PostgreSQL a su valor original.
     *
     * @param PDO $oDB Objeto PDO a restaurar
     * @param string $path_ini Path original a restaurar
     */
    private function restaurarEsquema(PDO $oDB, string $path_ini): void
    {
        $oDB->exec("SET search_path TO $path_ini");
    }
}
