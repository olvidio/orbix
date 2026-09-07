<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Filas de la tabla de ayuda «regiones en Orbix».
 * Para añadir o quitar una región/dl, editar solo estas listas.
 */
final class RegionesEnOrbix
{
    /**
     * @return list<array{sigla: string, nombre: string, codigo: string}>
     */
    public static function regiones(): array
    {
        return [
            ['sigla' => 'crAes', 'nombre' => 'Asia Este y Sur', 'codigo' => 'Aes-crAes'],
            ['sigla' => 'crAmc', 'nombre' => 'América Central', 'codigo' => 'Amc-crAmc'],
            ['sigla' => 'crAut', 'nombre' => 'Australia', 'codigo' => 'Aut-crAut'],
            ['sigla' => 'crCeb', 'nombre' => 'Costa de Marfil', 'codigo' => 'Ceb-crCeb'],
            ['sigla' => 'crCong', 'nombre' => 'Congo', 'codigo' => 'Cong-crCong'],
            ['sigla' => 'crEcs', 'nombre' => 'Europa Central Norte', 'codigo' => 'Ecs-crEcs'],
            ['sigla' => 'crEso', 'nombre' => 'Europa del Noroeste', 'codigo' => 'Eso-crEso'],
            ['sigla' => 'crEuc', 'nombre' => 'Centroeuropa', 'codigo' => 'Euc-crEuc'],
            ['sigla' => 'crGalbel', 'nombre' => 'Francia y Bélgica', 'codigo' => 'Galbel-crGalbel'],
            ['sigla' => 'crH', 'nombre' => 'España', 'codigo' => 'H-crH'],
            ['sigla' => 'crI', 'nombre' => 'Italia', 'codigo' => 'I-crI'],
            ['sigla' => 'crL', 'nombre' => 'Portugal', 'codigo' => 'L-crL'],
            ['sigla' => 'crM', 'nombre' => 'México', 'codigo' => 'M-crM'],
            ['sigla' => 'crNig', 'nombre' => 'Nigeria', 'codigo' => 'Nig-crNig'],
            ['sigla' => 'crPas', 'nombre' => 'Pacífico Sur', 'codigo' => 'Pas-crPas'],
            ['sigla' => 'crPl', 'nombre' => 'Filipinas', 'codigo' => 'Pl-crPl'],
            ['sigla' => 'crPla', 'nombre' => 'región del Plata', 'codigo' => 'Pla-crPla'],
            ['sigla' => 'crUsca', 'nombre' => 'región de Usa y Canadá', 'codigo' => 'Usca-crUsca'],
        ];
    }

    /**
     * @return list<array{sigla: string, nombre: string, codigo: string}>
     */
    public static function delegaciones(): array
    {
        return [
            ['sigla' => 'dlal', 'nombre' => 'Aragón y Levante', 'codigo' => 'H-dlal'],
            ['sigla' => 'dlb', 'nombre' => 'Barcelona', 'codigo' => 'H-dlb'],
            ['sigla' => 'dlg', 'nombre' => 'Guadalajara', 'codigo' => 'M-dlg'],
            ['sigla' => 'dlgr', 'nombre' => 'Granada', 'codigo' => 'H-dlgr'],
            ['sigla' => 'dlmE', 'nombre' => 'Madrid Este', 'codigo' => 'H-dlmE'],
            ['sigla' => 'dlmO', 'nombre' => 'Madrid Oeste', 'codigo' => 'H-dlmO'],
            ['sigla' => 'dln', 'nombre' => 'Noroeste', 'codigo' => 'H-dln'],
            ['sigla' => 'dlp', 'nombre' => 'Pamplona', 'codigo' => 'H-dlp'],
            ['sigla' => 'dls', 'nombre' => 'Sevilla', 'codigo' => 'H-dls'],
            ['sigla' => 'dly', 'nombre' => 'Monterrey', 'codigo' => 'M-dly'],
        ];
    }

    /**
     * Bloques de la tabla, en orden: cada lista de filas, separadas por `--`.
     *
     * @return list<list<array{sigla: string, nombre: string, codigo: string}>>
     */
    public static function bloques(): array
    {
        return [
            self::regiones(),
            self::delegaciones(),
        ];
    }

    public static function codigoTodos(): string
    {
        $filas = [];
        foreach (self::bloques() as $bloque) {
            foreach ($bloque as $fila) {
                $filas[] = $fila;
            }
        }

        return implode(',', array_column($filas, 'codigo'));
    }
}
