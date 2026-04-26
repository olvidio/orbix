<?php

namespace src\configuracion\application;

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * Caso de uso que lee los parámetros de configuración de la dl (tabla
 * `x_config_schema`) y construye un {@see ConfigSnapshot} inmutable y
 * serializable.
 *
 * Sustituye la antigua `src\configuracion\domain\entity\Config`, que hacía
 * `$GLOBALS['container']->get(...)` en cada getter porque no podía inyectar
 * el repositorio por constructor (la clase se guardaba en `$_SESSION` y PDO
 * no se serializa). El snapshot soluciona ambos problemas: el repositorio
 * se inyecta aquí por constructor y lo que acaba en sesión son valores
 * primitivos.
 */
final class ObtenerConfigSnapshot
{
    public function __construct(
        private readonly ConfigSchemaRepositoryInterface $repo,
    ) {
    }

    public function execute(): ConfigSnapshot
    {
        return new ConfigSnapshot(
            gesCalendario:           $this->value('gesCalendario'),
            ceLugar:                 $this->value('ce_lugar'),
            regionLatin:             $this->value('region_latin'),
            vstgr:                   $this->value('vstgr'),
            lugarFirma:              $this->value('lugar_firma'),
            dirStgr:                 $this->value('dir_stgr'),
            ambito:                  $this->value('ambito'),
            notaCorte:               $this->value('nota_corte'),
            notaMax:                 $this->value('nota_max'),
            caducaCursada:           $this->value('caduca_cursada'),
            idiomaDefault:           $this->value('idioma_default'),
            iniContadorCertificados: $this->value('ini_contador_certificados'),
            jefeCalendario:          $this->value('jefe_calendario'),
            aCursoStgr:              $this->jsonArray('curso_stgr'),
            aCursoCrt:               $this->jsonArray('curso_crt'),
        );
    }

    private function value(string $parametro): ?string
    {
        $oConfigSchema = $this->repo->findById($parametro);
        return $oConfigSchema?->getValorVo()?->value();
    }

    private function jsonArray(string $parametro): ?array
    {
        $raw = $this->value($parametro);
        if ($raw === null || $raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }
}
