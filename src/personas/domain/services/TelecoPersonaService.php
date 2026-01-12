<?php

namespace src\personas\domain\services;

use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

/**
 * Servicio de dominio para operaciones relacionadas con telecomunicaciones de personas
 */
class TelecoPersonaService
{
    public function __construct(
        private DescTelecoRepositoryInterface $descTelecoRepository
    ) {
    }

    /**
     * Devuelve los teleco de una persona especificados por parámetros
     *
     * @param int $id_nom ID de la persona
     * @param string $tipo_teleco Tipo de telecomunicación (ej: 'e-mail', 'teléfono')
     * @param string $separador Separador entre telecomunicaciones
     * @param string $desc_teleco Descripción del teleco. Si es '*', se añade la descripción entre paréntesis
     * @param bool $bDescripcion Si mostrar o no la descripción
     * @return string Telecomunicaciones formateadas
     */
    public function getTelecosPorTipo(
        int $id_nom,
        string $tipo_teleco,
        string $separador = '',
        string $desc_teleco = '',
        bool $bDescripcion = true
    ): string {

        // Ahora mismo:  telf => 1, movil => 2 ,email => 3
        $tipos_teleco =  ['telf' => 1, 'móvil' => 2 , 'e-mail' => 3 ];
        $id_tipo_teleco = $tipos_teleco[$tipo_teleco] ?? '';

        $aWhere = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_tipo_teleco'] = $id_tipo_teleco;

        if ($desc_teleco !== '*' && !empty($desc_teleco)) {
            $aWhere['id_desc_teleco'] = (int)$desc_teleco;
        }

        $TelecoPersonaRepository = $GLOBALS['container']->get(TelecoPersonaRepositoryInterface::class);
        $cTelecos = $TelecoPersonaRepository->getTelecospersona($aWhere);

        $tels = '';
        $separador = empty($separador) ? ".-<br>" : $separador;

        foreach ($cTelecos as $oTelecoPersona) {
            $iDescTel = $oTelecoPersona->getId_desc_teleco();
            $num_teleco = $oTelecoPersona->getNumTelecoVo()->value();

            if ($desc_teleco === "*" && !empty($iDescTel) && $bDescripcion) {
                $oDescTel = $this->descTelecoRepository->findById((int)$iDescTel);
                $desc = $oDescTel?->getDescTelecoVo()?->value() ?? '';
                $tels .= $num_teleco . "(" . $desc . ")" . $separador;
            } else {
                $tels .= $num_teleco . $separador;
            }
        }

        $tels = substr($tels, 0, -(strlen($separador)));
        return $tels;
    }

    /**
     * Devuelve el e-mail principal o primero de la lista de teleco de una persona
     *
     * @param int $id_nom ID de la persona
     * @param int $id_desc_teleco Descripción del teleco en la tabla xd_desc_teleco:
     *                         13 = e-mail principal
     *                         14 = e-mail profesional
     *                         15 = e-mail otros
     * @return string E-mail encontrado o cadena vacía
     */
    public function getEmailPrincipalOPrimero(int $id_nom, int $id_desc_teleco = 13): string
    {
        $aWhere = [];
        $aWhere['id_nom'] = $id_nom;
        $aWhere['id_tipo_teleco'] = 3; //'e-mail';

        if ($id_desc_teleco !== 13) {
            $aWhere['id_desc_teleco'] = $id_desc_teleco;
        }

        $aWhere['_ordre'] = 'id_desc_teleco';

        $e_mail = '';
        $TelecoPersonaRepository = $GLOBALS['container']->get(TelecoPersonaRepositoryInterface::class);
        $cTelecos = $TelecoPersonaRepository->getTelecospersona($aWhere);

        if (!empty($cTelecos) && count($cTelecos) > 0) {
            $oTeleco = $cTelecos[0];
            $e_mail = $oTeleco->getNumTelecoVo()->value();
        }

        return $e_mail;
    }
}
