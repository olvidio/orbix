<?php

namespace src\notas\application;

use src\notas\application\legacy\Resumen;

/**
 * Calcula el informe anual STGR de "profesores" (puntos 36..47).
 *
 * Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que
 * los controllers del frontend no importen la clase legacy directamente.
 * Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado.
 *
 * Tipos de profesor utilizados:
 *   1 Ordinario
 *   2 Extraordinario
 *   3 Adjunto
 *   4 Encargado
 *   5 Ayudante
 *   6 Asociado
 *   0 (todos)
 */
final class InformeStgrProfesores
{
    private const TIPO_ORDINARIO = 1;
    private const TIPO_EXTRAORDINARIO = 2;
    private const TIPO_ADJUNTO = 3;
    private const TIPO_ENCARGADO = 4;
    private const TIPO_AYUDANTE = 5;
    private const TIPO_ASOCIADO = 6;
    private const TIPO_TODOS = 0;

    /**
     * @param bool $lista  si `true`, cada metrica incluye el listado HTML de personas.
     *
     * @return array{res: array<int|string, array{num: int|float|string, lista?: string}>,
     *               textos: array<int|string, string>,
     *               curso_txt: string}
     */
    public function calcular(bool $lista): array
    {
        [$any_ini_curs, $curso_txt] = $this->cursoActual();

        $Resumen = new Resumen('profesores');
        $Resumen->setAnyIniCurs($any_ini_curs);
        $Resumen->setLista($lista);
        $Resumen->nuevaTablaProfe();

        $res = [];
        $textos = [];

        $res[36] = $Resumen->profesorDeTipo(self::TIPO_ORDINARIO);
        $textos[36] = ucfirst(_("número de profesores ordinarios"));
        $res[37] = $Resumen->profesorDeTipo(self::TIPO_EXTRAORDINARIO);
        $textos[37] = ucfirst(_("número de profesores extraordinarios"));
        $res[38] = $Resumen->profesorDeTipo(self::TIPO_ADJUNTO);
        $textos[38] = ucfirst(_("número de profesores adjuntos"));
        $res[39] = $Resumen->profesorDeTipo(self::TIPO_ENCARGADO);
        $textos[39] = ucfirst(_("número de profesores encargados"));
        $res[40] = $Resumen->profesorDeTipo(self::TIPO_ASOCIADO);
        $textos[40] = ucfirst(_("número de profesores asociados"));
        $res[41] = $Resumen->profesorDeTipo(self::TIPO_AYUDANTE);
        $textos[41] = ucfirst(_("número de profesores ayudantes"));
        $res[42] = $Resumen->profesorDeTipo(self::TIPO_TODOS);
        $textos[42] = ucfirst(_("número de total de profesores"));
        $res[43] = $Resumen->profesorDeLatin();
        $textos[43] = ucfirst(_("número de profesores de latín"));

        $res[44] = $Resumen->profesorEspecialidad(false);
        $textos[44] = ucfirst(_("número de profesores que dieron clase de su especialidad"));
        $res[45] = $Resumen->profesorEspecialidad(true);
        $textos[45] = ucfirst(_("número de profesores que dieron clase de otras asignaturas"));

        $res[46] = $Resumen->ProfesorCongreso();
        $textos[46] = ucfirst(_("número de profesores asistentes a cve del stgr u otras reuniones"));

        $res[47] = $Resumen->Departamentos();
        $textos[47] = ucfirst(_("nº de departamentos"));

        return [
            'res' => $res,
            'textos' => $textos,
            'curso_txt' => $curso_txt,
        ];
    }

    /**
     * @return array{0: int, 1: string} `[any_ini_curs, curso_txt]`.
     */
    private function cursoActual(): array
    {
        $any = (int)date('Y');
        $mes = (int)date('m');
        if ($mes > 3) {
            $any1 = $any - 1;
            return [$any1, "$any1-$any"];
        }
        $any1 = $any - 2;
        $any--;
        return [$any1, "$any1-$any"];
    }
}
