<?php

declare(strict_types=1);

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ResiduoOtraRegionConsultaInterface;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Comprueba el residuo de `e_notas_otra_region_stgr` en el orden en que hay
 * que mirarlo antes de borrarlo.
 *
 * @return array{pasos: list<array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}>}
 */
final class ResiduoOtraRegionComprobarData
{
    public function __construct(
        private readonly ResiduoOtraRegionConsultaInterface $consulta,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @return array{pasos: list<array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}>}
     */
    public function execute(): array
    {
        /** @var array<int, string> $nombresAsignatura */
        $nombresAsignatura = $this->asignaturaRepository->getArrayAsignaturas();
        $filas = $this->consulta->listar();

        $dePaso = [];
        $conActa = [];
        $sinActa = [];
        $conJson = [];
        foreach ($filas as $fila) {
            if ($fila['id_nom'] < 0) {
                $dePaso[] = $fila;
            } elseif ($fila['hay_acta_dl']) {
                $conActa[] = $fila;
            } else {
                $sinActa[] = $fila;
            }
            if ($fila['certificados'] !== []) {
                $conJson[] = $fila;
            }
        }

        return [
            'pasos' => [
                $this->pasoInventario($filas),
                $this->pasoDePaso($dePaso, $nombresAsignatura),
                $this->pasoConActa($conActa, $nombresAsignatura),
                $this->pasoSinActa($sinActa, $nombresAsignatura),
                $this->pasoJson($conJson, $nombresAsignatura),
            ],
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @return array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}
     */
    private function pasoInventario(array $filas): array
    {
        /** @var array<string, array{filas: int, personas: array<int, true>, paso: int, resto: int, con_acta: int, sin_acta: int, con_json: int}> $porEsquema */
        $porEsquema = [];
        foreach ($filas as $fila) {
            $esquema = (string) $fila['esquema'];
            if (!isset($porEsquema[$esquema])) {
                $porEsquema[$esquema] = [
                    'filas' => 0,
                    'personas' => [],
                    'paso' => 0,
                    'resto' => 0,
                    'con_acta' => 0,
                    'sin_acta' => 0,
                    'con_json' => 0,
                ];
            }
            $porEsquema[$esquema]['filas']++;
            $porEsquema[$esquema]['personas'][(int) $fila['id_nom']] = true;
            if ((int) $fila['id_nom'] < 0) {
                $porEsquema[$esquema]['paso']++;
            } else {
                $porEsquema[$esquema]['resto']++;
            }
            if ($fila['hay_acta_dl'] === true) {
                $porEsquema[$esquema]['con_acta']++;
            } else {
                $porEsquema[$esquema]['sin_acta']++;
            }
            if (is_array($fila['certificados']) && $fila['certificados'] !== []) {
                $porEsquema[$esquema]['con_json']++;
            }
        }

        $tabla = [];
        ksort($porEsquema);
        foreach ($porEsquema as $esquema => $cuenta) {
            $tabla[] = [
                $esquema,
                (string) $cuenta['filas'],
                (string) count($cuenta['personas']),
                (string) $cuenta['paso'],
                (string) $cuenta['resto'],
                (string) $cuenta['con_acta'],
                (string) $cuenta['sin_acta'],
                (string) $cuenta['con_json'],
            ];
        }

        return [
            'numero' => 1,
            'titulo' => _('Inventario'),
            'texto' => _('Filas que siguen en e_notas_otra_region_stgr de los esquemas cargados en esta base.'),
            'tablas' => [[
                'titulo' => '',
                'cabeceras' => [
                    _('esquema'),
                    _('filas'),
                    _('personas'),
                    _('de paso'),
                    _('resto'),
                    _('con acta pareja'),
                    _('sin acta pareja'),
                    _('con json'),
                ],
                'filas' => $tabla,
            ]],
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<int, string> $nombresAsignatura
     * @return array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}
     */
    private function pasoDePaso(array $filas, array $nombresAsignatura): array
    {
        return [
            'numero' => 2,
            'titulo' => _('Personas de paso'),
            'texto' => _('id_nom negativo. Si no hay filas, el residuo no contiene personas de paso: sus notas están en e_notas_dl.'),
            'tablas' => [[
                'titulo' => '',
                'cabeceras' => $this->cabecerasFila(),
                'filas' => $this->filasDetalle($filas, $nombresAsignatura),
            ]],
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<int, string> $nombresAsignatura
     * @return array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}
     */
    private function pasoConActa(array $filas, array $nombresAsignatura): array
    {
        return [
            'numero' => 3,
            'titulo' => _('Resto que ya tiene la asignatura en un acta'),
            'texto' => _('Misma persona y misma asignatura en algún e_notas_dl. La fila del residuo no aporta la calificación.'),
            'tablas' => [[
                'titulo' => '',
                'cabeceras' => $this->cabecerasFila(),
                'filas' => $this->filasDetalle($filas, $nombresAsignatura),
            ]],
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<int, string> $nombresAsignatura
     * @return array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}
     */
    private function pasoSinActa(array $filas, array $nombresAsignatura): array
    {
        /** @var array<string, int> $porPrefijo */
        $porPrefijo = [];
        /** @var array<int, int> $porSituacion */
        $porSituacion = [];
        /** @var array<int, array{nombre: string, filas: int, asignaturas: list<string>, actas: array<string, true>}> $porPersona */
        $porPersona = [];
        foreach ($filas as $fila) {
            $prefijo = $this->prefijoActa((string) $fila['acta']);
            $porPrefijo[$prefijo] = ($porPrefijo[$prefijo] ?? 0) + 1;
            $situacion = (int) $fila['id_situacion'];
            $porSituacion[$situacion] = ($porSituacion[$situacion] ?? 0) + 1;

            $idNom = (int) $fila['id_nom'];
            if (!isset($porPersona[$idNom])) {
                $porPersona[$idNom] = [
                    'nombre' => (string) $fila['nombre'],
                    'filas' => 0,
                    'asignaturas' => [],
                    'actas' => [],
                ];
            }
            $porPersona[$idNom]['filas']++;
            $porPersona[$idNom]['asignaturas'][] = $this->nombreAsignatura((int) $fila['id_asignatura'], $nombresAsignatura);
            $acta = trim((string) $fila['acta']);
            if ($acta !== '') {
                $porPersona[$idNom]['actas'][$acta] = true;
            }
        }
        arsort($porPrefijo);

        $tablaPrefijo = [];
        foreach ($porPrefijo as $prefijo => $cuenta) {
            $tablaPrefijo[] = [$prefijo, (string) $cuenta];
        }
        $tablaSituacion = [];
        arsort($porSituacion);
        foreach ($porSituacion as $id => $cuenta) {
            $tablaSituacion[] = [$this->nombreSituacion($id), (string) $cuenta];
        }

        ksort($porPersona);
        $tablaPersona = [];
        foreach ($porPersona as $idNom => $persona) {
            $actas = implode(', ', array_keys($persona['actas']));
            $tablaPersona[] = [
                (string) $idNom,
                $persona['nombre'],
                (string) $persona['filas'],
                $this->recortar(implode(', ', $persona['asignaturas'])),
                $this->recortar($actas),
            ];
        }

        return [
            'numero' => 4,
            'titulo' => _('Resto sin acta pareja'),
            'texto' => _('Es la única fila de esa persona y asignatura en los e_notas_dl cargados. Borrarla la quita del expediente.'),
            'tablas' => [
                [
                    'titulo' => _('Por prefijo del acta'),
                    'cabeceras' => [_('prefijo'), _('filas')],
                    'filas' => $tablaPrefijo,
                ],
                [
                    'titulo' => _('Por situación'),
                    'cabeceras' => [_('situación'), _('filas')],
                    'filas' => $tablaSituacion,
                ],
                [
                    'titulo' => _('Por persona'),
                    'cabeceras' => [_('id'), _('nombre'), _('filas'), _('asignaturas'), _('actas')],
                    'filas' => $tablaPersona,
                ],
            ],
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<int, string> $nombresAsignatura
     * @return array{numero: int, titulo: string, texto: string, tablas: list<array{titulo: string, cabeceras: list<string>, filas: list<list<string>>}>}
     */
    private function pasoJson(array $filas, array $nombresAsignatura): array
    {
        $tabla = [];
        foreach ($filas as $fila) {
            $numeros = is_array($fila['certificados']) ? implode(', ', $fila['certificados']) : '';
            $tabla[] = [
                (string) $fila['esquema'],
                (string) $fila['id_nom'],
                (string) $fila['nombre'],
                $this->nombreAsignatura((int) $fila['id_asignatura'], $nombresAsignatura),
                $numeros,
                $fila['certificado_en_modulo'] === true ? _('sí') : _('no'),
            ];
        }

        return [
            'numero' => 5,
            'titulo' => _('json_certificados'),
            'texto' => _('Sello de envío en la fila. Si el número ya está en certificados emitidos, el json no aporta el documento.'),
            'tablas' => [[
                'titulo' => '',
                'cabeceras' => [_('esquema'), _('id'), _('nombre'), _('asignatura'), _('certificado'), _('en el módulo')],
                'filas' => $tabla,
            ]],
        ];
    }

    /**
     * @return list<string>
     */
    private function cabecerasFila(): array
    {
        return [
            _('esquema'),
            _('id'),
            _('nombre'),
            _('asignatura'),
            _('situación'),
            _('acta'),
            _('fecha'),
            _('detalle'),
            _('acta en dl'),
        ];
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<int, string> $nombresAsignatura
     * @return list<list<string>>
     */
    private function filasDetalle(array $filas, array $nombresAsignatura): array
    {
        $tabla = [];
        foreach ($filas as $fila) {
            $tabla[] = [
                (string) $fila['esquema'],
                (string) $fila['id_nom'],
                (string) $fila['nombre'],
                $this->nombreAsignatura((int) $fila['id_asignatura'], $nombresAsignatura),
                $this->nombreSituacion((int) $fila['id_situacion']),
                (string) $fila['acta'],
                (string) $fila['f_acta'],
                $this->recortar((string) $fila['detalle']),
                $fila['hay_acta_dl'] === true ? _('sí') : _('no'),
            ];
        }

        return $tabla;
    }

    /**
     * @param array<int, string> $nombresAsignatura
     */
    private function nombreAsignatura(int $id, array $nombresAsignatura): string
    {
        $nombre = $nombresAsignatura[$id] ?? '';

        return $nombre !== '' ? $nombre : (string) $id;
    }

    private function nombreSituacion(int $id): string
    {
        return NotaSituacion::getArraySituacionTxt()[$id] ?? (string) $id;
    }

    private function prefijoActa(string $acta): string
    {
        $acta = trim($acta);
        if ($acta === '') {
            return _('(vacío)');
        }
        $partes = preg_split('/\s+/', $acta);

        return is_array($partes) && isset($partes[0]) && $partes[0] !== '' ? $partes[0] : $acta;
    }

    private function recortar(string $texto): string
    {
        if (strlen($texto) <= 240) {
            return $texto;
        }

        return substr($texto, 0, 237) . '...';
    }
}
