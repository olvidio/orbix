<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\services\EncargoDominioService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Datos del formulario de horario de encargo (no sacd).
 */
final class EncargoHorarioVerData
{

    public function __construct(
        private EncargoDominioService $dominioService,
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function cargar(string $mod, int $id_enc, int $id_item_h): array
    {
        if ($mod === 'nuevo' || $id_item_h <= 0) {
            return self::conOpciones([
                'id_item_h' => '',
                'f_ini' => '',
                'f_fin' => '',
                'dia_ref' => '',
                'dia_num' => '',
                'mas_menos' => '',
                'dia_inc' => '',
                'h_ini' => '',
                'h_fin' => '',
                'n_sacd' => '',
                'mes' => '',
            ]);
        }

        $enc = $this->encargoHorarioRepository->findById($id_item_h);
        if ($enc === null) {
            return $this->cargar('nuevo', $id_enc, 0);
        }

        return self::conOpciones(self::serializeHorario($enc));
    }

    /**
     * Añade al payload el dia calculado (a partir de mas_menos/dia_ref/dia_inc)
     * y las opciones de los desplegables (dia_semana, dia_ref, ordinales). Así
     * el controlador frontend no necesita importar `EncargoConstants` ni
     * `EncargoFunciones` desde `src\`.
     *
     * @param array<string, int|string> $base
     * @return array<string, mixed>
     */
    private function conOpciones(array $base): array
    {
        $oDominio = $this->dominioService;
        $dia = $oDominio->calcular_dia(
            (string)($base['mas_menos'] ?? ''),
            (string)($base['dia_ref'] ?? ''),
            (string)($base['dia_inc'] ?? ''),
        );

        return array_merge($base, [
            'dia' => (string)$dia,
            'opciones_dia_semana' => EncargoConstants::OPCIONES_DIA_SEMANA,
            'opciones_dia_ref' => EncargoConstants::OPCIONES_DIA_REF,
            'opciones_ordinales' => EncargoConstants::OPCIONES_ORDINALES,
        ]);
    }

    /**
     * @return array<string, int|string>
     */
    private function serializeHorario(EncargoHorario $e): array
    {
        $f_ini = $e->getF_ini();
        $f_fin = $e->getF_fin();
        $h_ini = $e->getH_ini();
        $h_fin = $e->getH_fin();

        return [
            'id_item_h' => (string)$e->getId_item_h(),
            'f_ini' => self::fmtDate($f_ini),
            'f_fin' => self::fmtDate($f_fin),
            'dia_ref' => (string)($e->getDia_ref() ?? ''),
            'dia_num' => (string)($e->getDia_num() ?? ''),
            'mas_menos' => (string)($e->getMas_menos() ?? ''),
            'dia_inc' => (string)($e->getDia_inc() ?? ''),
            'h_ini' => self::fmtTime($h_ini),
            'h_fin' => self::fmtTime($h_fin),
            'n_sacd' => (string)($e->getN_sacd() ?? ''),
            'mes' => (string)($e->getMes() ?? ''),
        ];
    }

    private function fmtDate(mixed $d): string
    {
        if ($d === null) {
            return '';
        }
        if ($d instanceof DateTimeLocal) {
            return $d->getFromLocal();
        }
        if ($d instanceof NullDateTimeLocal) {
            return '';
        }

        return '';
    }

    private function fmtTime(mixed $t): string
    {
        if ($t === null) {
            return '';
        }
        if ($t instanceof TimeLocal) {
            return $t->toDatabaseString();
        }
        if ($t instanceof NullTimeLocal) {
            return '';
        }

        return '';
    }
}
