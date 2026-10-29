<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\DocumentoField as F;
use src\inventario\domain\criteria\UbiInventarioField as UbiF;
use src\inventario\domain\entity\Documento;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoDocsxCtr extends DatosInfoRepo
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
        $this->setTxtTitulo(_("documentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este documento?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Documento');
        $this->setMetodoGestor('getId_doc');
        $this->setPau('p');

        $this->setRepositoryInterface(DocumentoRepositoryInterface::class);
    }

    public function getBuscar_view(): string
    {
        return '../view/buscarDocsxCtr.phtml';
    }

    public function getBuscar_namespace(): string
    {
        return __NAMESPACE__;
    }

    public function addCamposFormBuscar(): string
    {
        return '!exacto';
    }

    /**
     * @param array<string, mixed> $a_campos
     * @return array<string, mixed>
     */
    public function addCampos(array $a_campos = []): array
    {
        return $a_campos;
    }

    /**
     * @return list<Documento>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            return [];
        }

        $nom_ubi = str_replace('+', '\+', $this->k_buscar);
        $criterioUbi = Criteria::create();
        $criterioUbi = $this->exacto
            ? $criterioUbi->where(Filter::eq(UbiF::NomUbi, $nom_ubi))
            : $criterioUbi->where(Filter::matchesUnaccented(UbiF::NomUbi, $nom_ubi));

        $cUbisInventario = $this->ubiInventarioRepository->matching($criterioUbi);
        $aIdUbi = [];
        foreach ($cUbisInventario as $oUbiDoc) {
            $aIdUbi[] = $oUbiDoc->getId_ubi();
        }

        $criteria = Criteria::create();
        if ($aIdUbi === []) {
            $criteria = $criteria->limit(6);
        } else {
            $criteria = $criteria->where(Filter::in(F::IdUbi, $aIdUbi));
        }

        return $this->documentoRepository->matching($criteria);
    }

    public function getOpcionesParaCondicion(mixed $pKeyRepository, mixed $valor_depende, mixed $opcion_sel = null): string
    {
        $idUbi = is_numeric($valor_depende) ? (int) $valor_depende : 0;
        $aOpciones = $this->lugarRepository->getArrayLugares($idUbi);

        $opciones_txt = '<option></option>';
        foreach ($aOpciones as $key => $val) {
            $opcionSelStr = is_scalar($opcion_sel) ? (string) $opcion_sel : '';
            $sel = ((string) $key === $opcionSelStr) ? 'selected' : '';
            $opciones_txt .= "<option value=\"$key\" $sel>$val</option>";
        }

        return $opciones_txt;
    }

    /**
     * @return array<string, string>
     */
    public function getArrayCamposDepende(): array
    {
        return ['id_ubi' => 'id_lugar'];
    }
}
