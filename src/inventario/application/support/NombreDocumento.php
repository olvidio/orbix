<?php

declare(strict_types=1);

namespace src\inventario\application\support;

use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\TipoDoc;

/**
 * Nombre compuesto de un documento en los listados de inventario: «* » si se guarda bajo llave, sigla,
 * nombre del tipo, «, núm. de registro», «(n ej.)» si hay varios ejemplares y el rango de números
 * («desde el 5-Actual» / «5- 9»; el texto que va delante del rango depende del listado).
 */
final class NombreDocumento
{
    public static function compuesto(TipoDoc $oTipoDoc, Documento $oDocumento, string $antesDelRango = ' desde el '): string
    {
        $numReg = $oDocumento->getNumRegVo()?->value();
        $numEjemplares = $oDocumento->getNumEjemplaresVo()?->value();
        $numIni = $oDocumento->getNumIniVo()?->value();
        $numFin = $oDocumento->getNumFinVo()?->value();

        $nom = ($oTipoDoc->isBajo_llave() ? '* ' : '') . $oTipoDoc->getSigla();
        $nomDoc = $oTipoDoc->getNom_doc();
        $nom .= empty($nomDoc) ? '' : ' ' . $nomDoc;
        $nom .= !empty($numReg) ? ", $numReg" : '';
        $nom .= (!empty($numEjemplares) && $numEjemplares > 1) ? " ($numEjemplares ej.)" : '';
        if (!empty($numIni)) {
            $nom .= $antesDelRango . $numIni . '-';
            $nom .= !empty($numFin) ? " $numFin" : _("Actual");
        }

        return $nom;
    }
}
