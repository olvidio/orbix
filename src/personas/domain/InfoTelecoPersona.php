<?php

namespace src\personas\domain;

use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\domain\entity\TelecoPersona;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

class InfoTelecoPersona extends DatosInfoRepo
{
    private TelecoPersonaRepositoryInterface $telecoPersonaRepository;

    public function __construct(
        private TelecoPersonaDlRepositoryInterface $telecoPersonaDlRepository,
        private TelecoPersonaExRepositoryInterface $telecoPersonaExRepository,
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
        $this->telecoPersonaRepository = $this->telecoPersonaDlRepository;
        $this->setTxtTitulo(_("telecomunicaciones de una persona"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta teleco?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\TelecoPersona');
        $this->setMetodoGestor('getTelecosPersona');
        $this->setPau('p');

        $this->setRepositoryInterface(TelecoPersonaDlRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1001;
    }

    /**
     * @return list<TelecoPersona>
     */
    public function getColeccion(): array
    {
        $aWhere = [];
        $aOperador = [];
        if (!empty($this->id_pau)) {
            $aWhere['id_nom'] = $this->id_pau;
        }
        if (empty($this->k_buscar)) {
            $aWhere['_ordre'] = 'id_tipo_teleco';
        } else {
            $aWhere['congreso'] = $this->k_buscar;
            $aOperador['congreso'] = 'sin_acentos';
        }

        return $this->telecoPersonaRepository->getTelecosPersona($aWhere, $aOperador);
    }

    public function setObj_pau(string $Qobj_pau): void
    {
        $this->telecoPersonaRepository = match ($Qobj_pau) {
            'PersonaN' => $this->telecoPersonaDlRepository,
            'PersonaEx' => $this->telecoPersonaExRepository,
            default => $this->telecoPersonaDlRepository,
        };
        $this->repoInterface = match ($Qobj_pau) {
            'PersonaEx' => TelecoPersonaExRepositoryInterface::class,
            default => TelecoPersonaDlRepositoryInterface::class,
        };
    }

    public function getOpcionesParaCondicion(mixed $pKeyRepository, mixed $valor_depende, mixed $opcion_sel = null): string
    {
        $valorDepende = empty($valor_depende) ? '0' : (is_scalar($valor_depende) ? (string) $valor_depende : '0');
        $aOpciones = $this->descTelecoRepository->getArrayDescTelecoPersonas($valorDepende);

        $opciones_txt = '<option></option>';
        $opcionSelStr = is_scalar($opcion_sel) ? (string) $opcion_sel : '';
        foreach ($aOpciones as $key => $val) {
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
        return ['id_tipo_teleco' => 'id_desc_teleco'];
    }
}
