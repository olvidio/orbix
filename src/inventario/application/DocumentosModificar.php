<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\support\DocumentoTablaSelKey;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Modificación en lote de los documentos seleccionados en la tabla (`documentos_guardar`): solo se
 * cambian los campos marcados (`chk_*`): fechas de recibido, asignado y eliminado, eliminado sí/no y
 * rango de números. Una fecha vacía borra la que había; una mal escrita no toca nada.
 *
 * Sucesor de la lógica del controlador `documentos_guardar.php` (fase 5, paso 5.2). Mismo comportamiento.
 */
final class DocumentosModificar
{
    private const FECHAS = ['f_recibido' => 'setF_recibido', 'f_asignado' => 'setF_asignado', 'f_eliminado' => 'setF_eliminado'];

    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $in `documentos` (claves separadas por «#»), `chk_<campo>` y `<campo>`
     * @return string texto de error ('' si todo fue bien)
     */
    public function execute(array $in): string
    {
        $marcado = static fn (string $campo): bool => FuncTablasSupport::isTrue(FuncTablasSupport::inputString($in, "chk_$campo")) === true;
        $texto = static fn (string $campo): string => FuncTablasSupport::inputString($in, $campo);

        $documentos = $texto('documentos');
        if ($documentos === '') {
            return _("No ha seleccionado ningún documento");
        }
        $campos = array_filter(['f_recibido', 'f_asignado', 'eliminado', 'f_eliminado', 'num_ini', 'num_fin'], $marcado);
        if ($campos === []) {
            return _("Debe marcar al menos un campo a modificar");
        }
        // Una fecha mal escrita no se puede tomar como «vacía»: borraría la que había.
        $fechas = [];
        $noValidas = [];
        foreach (array_keys(self::FECHAS) as $campo) {
            if (!$marcado($campo)) {
                continue;
            }
            $fecha = $texto($campo) === '' ? null : DateTimeLocal::createFromLocal($texto($campo));
            if ($fecha !== null && !$fecha instanceof DateTimeLocal) {
                $noValidas[] = $texto($campo);
                continue;
            }
            $fechas[$campo] = $fecha;
        }
        if ($noValidas !== []) {
            return sprintf(_("Fecha no válida: %s"), implode(', ', $noValidas));
        }
        $eliminado = FuncTablasSupport::inputInt($in, 'eliminado');
        $numero = static fn (string $campo): ?int => is_numeric($texto($campo)) ? (int) $texto($campo) : null;

        $error = '';
        $hechos = 0;
        foreach (explode('#', $documentos) as $clave) {
            $idDoc = $clave === '' ? null : DocumentoTablaSelKey::idDocFromUrlsafeKey($clave);
            $oDocumento = $idDoc === null ? null : $this->documentoRepository->findById($idDoc);
            if ($oDocumento === null) {
                continue;
            }
            foreach ($fechas as $campo => $fecha) {
                $oDocumento->{self::FECHAS[$campo]}($fecha);
            }
            if ($marcado('eliminado') && ($eliminado === 1 || $eliminado === 2)) {
                $oDocumento->setEliminado($eliminado === 1);
            }
            if ($marcado('num_ini')) {
                $oDocumento->setNum_ini($numero('num_ini'));
            }
            if ($marcado('num_fin')) {
                $oDocumento->setNum_fin($numero('num_fin'));
            }
            if ($this->documentoRepository->Guardar($oDocumento) === false) {
                $error .= _("hay un error, no se ha guardado") . "\n" . $this->documentoRepository->getErrorTxt();
            } else {
                $hechos++;
            }
        }
        if ($error === '' && $hechos === 0) {
            return _("No se pudo aplicar la modificación a los documentos seleccionados");
        }

        return $error;
    }
}
