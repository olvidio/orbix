<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as DocF;
use src\inventario\domain\entity\Documento;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Guardar la asignación de un tipo de documento a centros (`doc_asignar_ctr_guardar`) o a lugares de
 * la dl (`doc_asignar_dlb_guardar`): el número de cada destino es el de registro si el tipo es numerado
 * y, si no, el número de ejemplares; las fechas de recibido y asignado solo se cambian si llegan.
 *
 * - A centros: si el centro no tiene el documento, se crea; si lo tiene, se actualiza el primero.
 * - A lugares: si en el lugar no hay ningún documento de ese tipo, se crea (como en los centros: duda 14
 *   de la sección 6b del plan de tests); si hay uno, se actualiza; si hay varios no se sabe cuál es y
 *   se avisa sin tocar ninguno.
 *
 * Sucesor de la lógica de esos dos controladores (fase 5, paso 5.2). Cambios: una fecha mal escrita ya
 * no borra la que tenía el documento (se devuelve el error sin guardar nada), y si el tipo es numerado
 * se lee del propio tipo, no de un campo del formulario.
 */
final class DocumentosAsignar
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
        private DocumentoRepositoryInterface $documentoRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
    }

    /**
     * @param array<int|string, mixed> $numeros id del centro => número
     * @return string texto de error ('' si todo fue bien)
     */
    public function aCentros(int $idTipoDoc, array $numeros, string $fRecibido, string $fAsignado): string
    {
        return $this->asignar($idTipoDoc, $numeros, $fRecibido, $fAsignado, function (int $idUbi) use ($idTipoDoc): Documento {
            $cDocumentos = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::IdUbi, $idUbi))
                ->andWhere(Filter::eq(DocF::IdTipoDoc, $idTipoDoc)));
            if ($cDocumentos !== []) {
                return $cDocumentos[0];
            }
            $oDocumento = new Documento();
            $oDocumento->setId_doc($this->documentoRepository->getNewId());
            $oDocumento->setIdUbiVo($idUbi);
            $oDocumento->setId_tipo_doc($idTipoDoc);

            return $oDocumento;
        });
    }

    /**
     * @param array<int|string, mixed> $numeros id del lugar => número
     * @return string texto de error ('' si todo fue bien)
     */
    public function aLugares(int $idTipoDoc, array $numeros, string $fRecibido, string $fAsignado): string
    {
        return $this->asignar($idTipoDoc, $numeros, $fRecibido, $fAsignado, function (int $idLugar) use ($idTipoDoc): Documento|string|null {
            $oLugar = $this->lugarRepository->findById($idLugar);
            if ($oLugar === null) {
                return null;
            }
            $cDocumentos = $this->documentoRepository->matching(Criteria::create()
                ->where(Filter::eq(DocF::IdUbi, $oLugar->getId_ubi()))
                ->andWhere(Filter::eq(DocF::IdLugar, $idLugar))
                ->andWhere(Filter::eq(DocF::IdTipoDoc, $idTipoDoc)));

            if (count($cDocumentos) > 1) {
                return _("No se encuentra el documento") . "\n";
            }
            if ($cDocumentos !== []) {
                return $cDocumentos[0];
            }
            $oDocumento = new Documento();
            $oDocumento->setId_doc($this->documentoRepository->getNewId());
            $oDocumento->setIdUbiVo($oLugar->getId_ubi());
            $oDocumento->setId_lugar($idLugar);
            $oDocumento->setId_tipo_doc($idTipoDoc);

            return $oDocumento;
        });
    }

    /**
     * @param array<int|string, mixed> $numeros
     * @param callable(int): (Documento|string|null) $documentoDe documento del destino, texto de error o null (saltar)
     */
    private function asignar(int $idTipoDoc, array $numeros, string $fRecibido, string $fAsignado, callable $documentoDe): string
    {
        $oTipoDoc = $this->tipoDocRepository->findById($idTipoDoc);
        if ($oTipoDoc === null) {
            return _("No se encuentra el documento");
        }
        $fechas = [];
        foreach (['setF_recibido' => $fRecibido, 'setF_asignado' => $fAsignado] as $setter => $texto) {
            if ($texto === '') {
                continue; // vacía: se deja la que esté
            }
            $fecha = DateTimeLocal::createFromLocal($texto);
            if (!$fecha instanceof DateTimeLocal) {
                return sprintf(_("Fecha no válida: %s"), $texto);
            }
            $fechas[$setter] = $fecha;
        }

        $error = '';
        foreach ($numeros as $id => $numRaw) {
            if (!is_numeric($id)) {
                continue;
            }
            $oDocumento = $documentoDe((int) $id);
            if (!$oDocumento instanceof Documento) {
                $error .= (string) $oDocumento;
                continue;
            }
            $num = is_numeric($numRaw) ? (int) $numRaw : 0;
            if ($oTipoDoc->isNumerado()) {
                $oDocumento->setNum_reg($num);
            } else {
                $oDocumento->setNum_ejemplares($num);
            }
            foreach ($fechas as $setter => $fecha) {
                $oDocumento->{$setter}($fecha);
            }
            if ($this->documentoRepository->Guardar($oDocumento) === false) {
                $error .= _("hay un error, no se ha guardado") . "\n" . $this->documentoRepository->getErrorTxt();
            }
        }

        return $error;
    }
}
