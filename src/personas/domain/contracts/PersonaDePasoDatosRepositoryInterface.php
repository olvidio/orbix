<?php

declare(strict_types=1);

namespace src\personas\domain\contracts;

/**
 * Qué datos tiene una persona de paso en cualquier dl: su tabla (`restov.p_de_paso_ex`) es común a
 * todas, así que antes de borrarla hay que mirar en todas.
 */
interface PersonaDePasoDatosRepositoryInterface
{
    public const ASISTENCIAS = 'asistencias';
    public const CARGOS = 'cargos';
    public const ENCARGOS = 'encargos';
    public const NOTAS = 'notas';
    public const MATRICULAS = 'matriculas';
    public const DOCENCIA = 'docencia';
    public const ZONAS = 'zonas';

    /**
     * @return list<string> los tipos de datos (constantes de esta interfaz) que tiene; vacío si ninguno
     */
    public function datosDe(int $idNom): array;
}
