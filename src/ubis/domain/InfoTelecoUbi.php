<?php

namespace src\ubis\domain;

use src\shared\domain\DatosInfoRepo;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;

/* No vale el underscore en el nombre */

class InfoTelecoUbi extends DatosInfoRepo
{
    private ?string $objPau = null;

    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
        $this->setTxtTitulo(_("Telecos del Ubi"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar(_("buscar una teleco (número)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TelecoUbi');
        $this->setMetodoGestor('getTelecos');
        $this->setPau('u');
    }

    public function getId_dossier(): int
    {
        return 2001;
    }

    /**
     * @return list<TelecoUbi>
     */
    /**
     * @return list<TelecoUbi>
     */
    public function getColeccion(): array
    {
        if ($this->objPau === null) {
            throw new \RuntimeException('InfoTelecoUbi: falta setObj_pau() antes de getColeccion()');
        }

        $aWhere = [];
        if (!empty($this->id_pau)) {
            $aWhere['id_ubi'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'id_tipo_teleco';
            $aOperador = [];
        } else {
            $aWhere = ['num_teleco' => $this->k_buscar];
            $aOperador = ['num_teleco' => 'sin_acentos'];
        }

        $repo = $this->ubiRepositoryResolver->getTelecoRepository($this->objPau);

        return $repo->getTelecos($aWhere, $aOperador);
    }

    public function setObj_pau(string $obj_pau): void
    {
        $this->objPau = (string)$obj_pau;
        $this->setRepositoryInterface($this->ubiRepositoryResolver->getTelecoRepositoryClass($this->objPau));
    }

    public function getOpcionesParaCondicion(mixed $pKeyRepository, mixed $valor_depende, mixed $opcion_sel = null): string
    {
        $valorDepende = (is_int($valor_depende) || is_string($valor_depende)) ? (string) $valor_depende : '';
        $aOpciones = $this->descTelecoRepository->getArrayDescTelecoUbis($valorDepende);

        $opciones_txt = '<option></option>';
        $opcionSel = is_scalar($opcion_sel) || $opcion_sel === null ? (string) $opcion_sel : '';
        foreach ($aOpciones as $key => $val) {
            $keyStr = (string) $key;
            $valStr = (string) $val;
            $sel = ($keyStr === $opcionSel) ? 'selected' : '';
            $opciones_txt .= "<option value=\"$keyStr\" $sel>$valStr</option>";
        }

        return $opciones_txt;
    }

    /**
     * @return array<string, string>
     */
    public function getArrayCamposDepende(): array
    {
        return ['id_tipo_teleco' => 'id_desc_teleco'];
    }
}
