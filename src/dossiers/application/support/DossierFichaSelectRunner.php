<?php

namespace src\dossiers\application\support;

use src\shared\domain\DatosInfoRepo;
use src\shared\infrastructure\DependencyResolver;

/**
 * Invoca widgets Select_* y clases DatosInfoRepo con tipado seguro para PHPStan.
 */
final class DossierFichaSelectRunner
{
    /**
     * @param class-string $fqcn
     * @param array<string, mixed> $post
     * @return array<string, mixed>|null null si no hay clase resoluble
     */
    public function buildSelectSegment(
        string $fqcn,
        string $nomBloque,
        string $idDossier,
        string $pau,
        string $objPau,
        int $idPau,
        string $permiso,
        string $bloque,
        string $queSel,
        string $stack,
        mixed $idSel,
        int $scrollId,
        int $idActiv,
        int $modoCurso,
        array $post,
    ): ?array {
        if (!class_exists($fqcn)) {
            return null;
        }

        $select = DependencyResolver::get($fqcn);
        $this->callIfExists($select, 'setId_dossier', $idDossier);
        $this->callIfExists($select, 'setPau', $pau);
        $this->callIfExists($select, 'setObj_pau', $objPau);
        $this->callIfExists($select, 'setId_pau', $idPau);
        $this->callIfExists($select, 'setPermiso', $permiso);
        $this->callIfExists($select, 'setBloque', $bloque);
        $this->callIfExists($select, 'setQueSel', $queSel);
        $stackActual = $post['stack_actual'] ?? 0;
        $this->callIfExists($select, 'setStackActual', is_numeric($stackActual) ? (int) $stackActual : 0);

        if ($idSel !== '' && $idSel !== null && !(is_array($idSel) && $idSel === [])) {
            $this->callIfExists($select, 'setQId_sel', $idSel);
        }
        if ($scrollId > 0) {
            $this->callIfExists($select, 'setQScroll_id', $scrollId);
        }

        switch ((int) $idDossier) {
            case 1301:
            case 1302:
                $this->callIfExists($select, 'setModo_curso', $modoCurso);
                break;
            case 1303:
                if ($idActiv > 0) {
                    $this->callIfExists($select, 'setQId_activ', $idActiv);
                }
                $this->callIfExists($select, 'setTodos', $post['todos'] ?? null);
                break;
        }

        if (method_exists($select, 'getSegmentData')) {
            $segmentPayload = $select->getSegmentData();
            $payload = is_array($segmentPayload) ? $segmentPayload : [];
            $segmentTipo = is_string($payload['segment_tipo'] ?? null)
                ? $payload['segment_tipo']
                : 'select_habitaciones_cdc';
            unset($payload['segment_tipo']);

            return array_merge(
                [
                    'tipo' => $segmentTipo,
                    'id' => $nomBloque,
                ],
                $payload
            );
        }

        if (method_exists($select, 'getHtml')) {
            return [
                'tipo' => 'html',
                'id' => $nomBloque,
                'html' => $select->getHtml(),
            ];
        }

        return null;
    }

    /**
     * @return array{info: DatosInfoRepo, clase_info_encoded: string}
     */
    public function resolveDatosInfo(string $claseInfo, int $idPau, string $objPau): array
    {
        if (!is_subclass_of($claseInfo, DatosInfoRepo::class)) {
            throw new \RuntimeException('Clase info no extiende DatosInfoRepo: ' . $claseInfo);
        }
        $encoded = urlencode($claseInfo);
        $info = DependencyResolver::get($claseInfo);
        $info->setId_pau($idPau);
        if (method_exists($info, 'setObj_pau')) {
            $info->setObj_pau($objPau);
        }

        return ['info' => $info, 'clase_info_encoded' => $encoded];
    }

    private function callIfExists(object $target, string $method, mixed ...$args): void
    {
        if (method_exists($target, $method)) {
            $target->$method(...$args);
        }
    }
}
