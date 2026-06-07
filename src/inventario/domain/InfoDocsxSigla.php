<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\shared\config\ConfigGlobal;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoDocsxSigla extends DatosInfoRepo
{
    public function __construct(
        private DocumentoRepositoryInterface $documentoRepository,
        private TipoDocRepositoryInterface $tipoDocRepository,
        private LugarRepositoryInterface $lugarRepository,
    ) {
        $this->setTxtTitulo(_('documentos'));
        $this->setTxtEliminar(_('¿Está seguro que desea eliminar este documento?'));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Documento');
        $this->setMetodoGestor('getId_doc');
        $this->setPau('p');

        $this->setRepositoryInterface(DocumentoRepositoryInterface::class);
    }

    public function getBuscar_view(): string
    {
        return '../view/buscarDocsxSigla.phtml';
    }

    public function getBuscar_namespace(): string
    {
        return __NAMESPACE__;
    }

    public function addCamposFormBuscar(): string
    {
        return '';
    }

    /**
     * @param array<string, mixed> $a_campos
     * @return array<string, mixed>
     */
    public function addCampos(array $a_campos = []): array
    {
        $a_campos['aOpcionesTiposDoc'] = $this->tipoDocRepository->getArrayTipoDoc();

        $url_bloque = ConfigGlobal::getWeb() . '/frontend/inventario/controller/documentos_form.php';
        $a_campos['url_bloque'] = $url_bloque;
        $a_campos['documentos_form_hash_meta'] = [
            'url' => $url_bloque,
            'campos_form' => 'id_tipo_doc!documentos',
        ];

        $a_campos['locale_us'] = ConfigGlobal::is_locale_us();

        return $a_campos;
    }

    /**
     * @return list<Documento>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['id_tipo_doc' => 0];
            $aOperador = [];
        } else {
            $aWhere = ['id_tipo_doc' => $this->k_buscar, '_ordre' => 'id_ubi,id_lugar'];
            $aOperador = [];
        }

        return $this->documentoRepository->getDocumentos($aWhere, $aOperador);
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
