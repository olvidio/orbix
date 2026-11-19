<?php

declare(strict_types=1);

namespace src\shared\application;

use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaId;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Anota una pregunta hecha a la ayuda y lo que la ayuda contestó ({@see AyudaPreguntar}), para
 * poder revisar después las que no tuvieron respuesta o no sirvieron.
 *
 * Es anónimo: recibe la delegación de la sesión, nunca el usuario. Un fallo al anotar no puede
 * estropear la ayuda: devuelve `''` y quien pregunta recibe su respuesta igual.
 */
final class AyudaConsultaRegistrar
{
    private const MAX_PREGUNTA = 400;

    /** Las consultas de más de estos meses se borran (salvo las marcadas sin revisar). */
    private const MESES_QUE_SE_CONSERVAN = 6;

    private const RUTA_CONTROLLER = '#frontend/[a-z][a-z0-9_]*/controller/[A-Za-z0-9_]+\.php#';

    public function __construct(
        private readonly AyudaConsultaRepositoryInterface $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $resultado Lo que devolvió {@see AyudaPreguntar::execute()}.
     * @param string $esquema Delegación de la sesión (p. ej. `H-dlbv`).
     * @param string $pantalla Ruta o URL de la pantalla desde la que se abrió la ayuda.
     * @return string El id de la consulta anotada, o `''` si no se ha anotado nada.
     */
    public function execute(array $resultado, string $esquema, string $pantalla, ?DateTimeLocal $ahora = null): string
    {
        $pregunta = isset($resultado['pregunta']) && is_string($resultado['pregunta']) ? trim($resultado['pregunta']) : '';
        if ($pregunta === '') {
            return '';
        }
        $ahora ??= new DateTimeLocal();
        $hits = isset($resultado['resultados']) && is_array($resultado['resultados'])
            ? array_values($resultado['resultados'])
            : [];
        $primero = isset($hits[0]) && is_array($hits[0]) ? $hits[0] : [];

        try {
            $consulta = new AyudaConsulta();
            $consulta->setUuid_item(AyudaConsultaId::random());
            $consulta->setF_consulta($ahora);
            $consulta->setPregunta(mb_substr($pregunta, 0, self::MAX_PREGUNTA));
            $consulta->setEsquema($esquema !== '' ? $esquema : null);
            $consulta->setPantalla(
                preg_match(self::RUTA_CONTROLLER, $pantalla, $m) === 1 ? $m[0] : null
            );
            $consulta->setN_resultados(count($hits));
            if ($primero !== []) {
                $consulta->setPuntuacion(is_numeric($primero['score'] ?? null) ? (float) $primero['score'] : null);
                $consulta->setFuente(is_string($primero['fuente'] ?? null) ? $primero['fuente'] : null);
                $consulta->setTitulo(is_string($primero['titulo'] ?? null) ? $primero['titulo'] : null);
            }

            $limite = clone $ahora;
            $limite->modify('-' . self::MESES_QUE_SE_CONSERVAN . ' months');
            $this->repository->purgarAnterioresA($limite);

            return $this->repository->Guardar($consulta) ? $consulta->getUuid_item()->value() : '';
        } catch (\Throwable) {
            return '';
        }
    }
}
