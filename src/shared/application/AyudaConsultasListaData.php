<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\criteria\AyudaConsultaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\security\HashB;

/**
 * Datos de la pantalla de revisión de las consultas a la ayuda.
 *
 * Junta en una fila las veces que se ha hecho la misma pregunta (sin contar mayúsculas, acentos
 * ni signos) y pone primero lo que más interesa corregir en las guías: las que alguien marcó
 * como «no me ha servido», después las que no tuvieron ningún resultado y después las más
 * repetidas. Con `ver = problemas` (por defecto) solo salen las dos primeras clases.
 *
 * `tsv` son las preguntas mostradas en el formato de `tools/audit/ayuda_preguntas.tsv`
 * (pregunta, guía, apartado), con la guía que salió como propuesta: quien revisa la corrige.
 *
 * @phpstan-type Grupo array{
 *     pregunta: string, veces: int, ultima: string, no_sirvio: int, sirvio: int,
 *     comentarios: list<string>, esquemas: list<string>, pantallas: list<string>,
 *     n_resultados: int, puntuacion: ?float, fuente: string, titulo: string,
 *     ctx_resolver: string, ctx_descartar: string, ctx_reabrir: string
 * }
 */
final class AyudaConsultasListaData
{
    public const ACCION_ESTADO = 'ayuda_consultas_estado_update';

    /** Consultas que se leen como mucho (las más recientes del estado pedido). */
    private const LIMITE = 2000;

    /** Consultas de un grupo que caben en la cápsula de sus acciones. */
    private const MAX_POR_CAPSULA = 50;

    public function __construct(
        private readonly AyudaConsultaRepositoryInterface $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $input `estado` (1 pendiente, 2 resuelta, 3 descartada) y `ver` (`problemas` | `todas`).
     * @return array{
     *     estado: int, ver: string, opciones_estado: list<array{0: string, 1: string}>,
     *     total_consultas: int, grupos: list<Grupo>, tsv: string
     * }
     */
    public function execute(array $input): array
    {
        $estado = is_numeric($input['estado'] ?? null) ? (int) $input['estado'] : AyudaConsultaEstado::PENDIENTE;
        if (!array_key_exists($estado, AyudaConsultaEstado::getArrayEstado())) {
            $estado = AyudaConsultaEstado::PENDIENTE;
        }
        $ver = ($input['ver'] ?? '') === 'todas' ? 'todas' : 'problemas';

        $consultas = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(F::Estado, $estado))
                ->orderBy(F::FConsulta, OrderDirection::Desc)
                ->limit(self::LIMITE)
        );

        /** @var array<string, list<AyudaConsulta>> $porPregunta */
        $porPregunta = [];
        foreach ($consultas as $consulta) {
            $porPregunta[$this->clave($consulta->getPregunta())][] = $consulta;
        }

        $grupos = [];
        foreach ($porPregunta as $delGrupo) {
            if ($delGrupo === []) {
                continue;
            }
            $grupo = $this->grupo($delGrupo, $estado);
            if ($ver === 'problemas' && $grupo['no_sirvio'] === 0 && $grupo['n_resultados'] > 0) {
                continue;
            }
            $grupos[] = $grupo;
        }
        usort($grupos, fn (array $a, array $b): int => [$this->clase($a), $b['veces'], $b['ultima']]
            <=> [$this->clase($b), $a['veces'], $a['ultima']]);

        return [
            'estado' => $estado,
            'ver' => $ver,
            'opciones_estado' => OpcionesDesplegable::enOrden(AyudaConsultaEstado::getArrayEstado()),
            'total_consultas' => count($consultas),
            'grupos' => $grupos,
            'tsv' => $this->tsv($grupos),
        ];
    }

    /**
     * @param non-empty-list<AyudaConsulta> $consultas De más reciente a más antigua.
     * @return Grupo
     */
    private function grupo(array $consultas, int $estado): array
    {
        $reciente = $consultas[0];
        $noSirvio = 0;
        $sirvio = 0;
        $comentarios = [];
        $esquemas = [];
        $pantallas = [];
        $uuids = [];
        foreach ($consultas as $consulta) {
            $uuids[] = $consulta->getUuid_item()->value();
            if ($consulta->getUtil() === false) {
                $noSirvio++;
            } elseif ($consulta->getUtil() === true) {
                $sirvio++;
            }
            if ((string) $consulta->getComentario() !== '') {
                $comentarios[] = (string) $consulta->getComentario();
            }
            if ((string) $consulta->getEsquema() !== '') {
                $esquemas[(string) $consulta->getEsquema()] = true;
            }
            if ((string) $consulta->getPantalla() !== '') {
                $pantallas[(string) $consulta->getPantalla()] = true;
            }
        }
        $uuids = array_slice($uuids, 0, self::MAX_POR_CAPSULA);
        $pendiente = $estado === AyudaConsultaEstado::PENDIENTE;

        return [
            'pregunta' => $reciente->getPregunta(),
            'veces' => count($consultas),
            'ultima' => $reciente->getF_consulta()?->format('Y-m-d H:i') ?? '',
            'no_sirvio' => $noSirvio,
            'sirvio' => $sirvio,
            'comentarios' => $comentarios,
            'esquemas' => array_map('strval', array_keys($esquemas)),
            'pantallas' => array_map('strval', array_keys($pantallas)),
            'n_resultados' => $reciente->getN_resultados(),
            'puntuacion' => $reciente->getPuntuacion(),
            'fuente' => (string) $reciente->getFuente(),
            'titulo' => (string) $reciente->getTitulo(),
            'ctx_resolver' => $pendiente ? $this->capsula($uuids, AyudaConsultaEstado::RESUELTA) : '',
            'ctx_descartar' => $pendiente ? $this->capsula($uuids, AyudaConsultaEstado::DESCARTADA) : '',
            'ctx_reabrir' => $pendiente ? '' : $this->capsula($uuids, AyudaConsultaEstado::PENDIENTE),
        ];
    }

    /**
     * @param list<string> $uuids
     */
    private function capsula(array $uuids, int $estado): string
    {
        return HashB::sign(self::ACCION_ESTADO, ['uuids' => $uuids, 'estado' => $estado]);
    }

    /**
     * 0 alguien dijo que no le sirvió; 1 la ayuda no encontró nada; 2 el resto.
     *
     * @param Grupo $grupo
     */
    private function clase(array $grupo): int
    {
        if ($grupo['no_sirvio'] > 0) {
            return 0;
        }

        return $grupo['n_resultados'] === 0 ? 1 : 2;
    }

    /** La pregunta sin mayúsculas, acentos, signos ni espacios repetidos. */
    private function clave(string $pregunta): string
    {
        $texto = strtr(mb_strtolower($pregunta, 'UTF-8'), [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
            'à' => 'a', 'è' => 'e', 'ò' => 'o', 'ï' => 'i',
        ]);
        $texto = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $texto) ?? $texto;

        return trim($texto);
    }

    /**
     * @param list<Grupo> $grupos
     */
    private function tsv(array $grupos): string
    {
        $tsv = '';
        foreach ($grupos as $grupo) {
            $pregunta = trim(preg_replace('/\s+/u', ' ', $grupo['pregunta']) ?? $grupo['pregunta']);
            $guia = preg_match('#^docs/guias/(.+)\.md$#', $grupo['fuente'], $m) === 1 ? $m[1] : '';
            $tsv .= $pregunta . "\t" . $guia . "\t\n";
        }

        return $tsv;
    }
}
