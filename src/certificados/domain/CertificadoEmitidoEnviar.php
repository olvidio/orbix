<?php

namespace src\certificados\domain;

use Exception;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\Trasladar;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use src\tablonanuncios\domain\value_objects\Categoria;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class CertificadoEmitidoEnviar
{
    public function __construct(
        private readonly CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly AnuncioRepositoryInterface $anuncioRepository,
        private readonly Trasladar $trasladar,
    ) {
    }

    public function execute(int $id_item): string
    {
        $oCertificadoEmitido = $this->certificadoEmitidoRepository->findById($id_item);
        if ($oCertificadoEmitido === null) {
            return _("No se encuentra el certificado");
        }

        $error_txt = '';
        $id_nom = (int) ($oCertificadoEmitido->getId_nom() ?? 0);
        $b_saltar = false;

        if ($id_nom < 0) {
            return _("Es una persona de paso. No se puede enviar. Hay que imprimir.");
        }

        $certificado = (string) ($oCertificadoEmitido->getCertificado() ?? '');
        $cPersonas = Persona::buscarEnTodasRegiones($id_nom);
        if ($cPersonas === []) {
            return "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ': line ' . __LINE__;
        }
        if (count($cPersonas) > 1) {
            $error_txt .= 'Existe más de una persona con este id, dado de alta:';
            foreach ($cPersonas as $aPersona) {
                $error_txt .= "\n" . $aPersona['esquema'];
            }

            return $error_txt;
        }

        $nombre_apellidos = $cPersonas[0]['persona']->getApellidosNombre();
        $dlVo = $cPersonas[0]['persona']->getDlVo();
        if ($dlVo === null) {
            return _('No se puede determinar la delegación destino');
        }
        $dl_destino = (string) $dlVo->value();

        try {
            $a_datos_region_stgr = $this->delegacionRepository->mi_region_stgr($dl_destino);
            $esquema_region_stgr_dst = is_string($a_datos_region_stgr['esquema_region_stgr'] ?? null)
                ? $a_datos_region_stgr['esquema_region_stgr']
                : '';
            $esquema_dl_dst = is_string($a_datos_region_stgr['esquema_dl'] ?? null)
                ? $a_datos_region_stgr['esquema_dl']
                : '';
        } catch (Exception $e) {
            $error_txt .= $e->getMessage() . "\n";
        }

        if ($error_txt !== '') {
            return $error_txt;
        }

        $oDBPropiedades = new DBPropiedades();
        $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(true, true);
        if (!is_array($a_posibles_esquemas)) {
            $a_posibles_esquemas = [];
        }
        $is_dl_in_orbix = false;
        foreach ($a_posibles_esquemas as $esquema) {
            $row = explode('-', (string) $esquema);
            if (($row[1] ?? '') === $dl_destino) {
                $is_dl_in_orbix = true;
                break;
            }
        }

        if (!$is_dl_in_orbix) {
            return $error_txt . _('Hay que enviar manualmente el certificado. Esta persona no está en aquinate');
        }

        $oHoy = new DateTimeLocal();
        $oCertificadoEmitido->setF_enviado($oHoy);
        $this->trasladar->setReg_dl_dst($esquema_dl_dst);
        $this->trasladar->copiar_certificados_a_dl($oCertificadoEmitido);
        $error_txt .= $this->trasladar->getError();

        $texto_anuncio = sprintf(_('se ha recibido el certificado %s para %s.'), $certificado, $nombre_apellidos);
        $Anuncio = new Anuncio();
        $Anuncio->setUuid_item(AnuncioId::random());
        $Anuncio->setUsuarioCreadorVo(ConfigGlobal::mi_usuario());
        $Anuncio->setEsquemaEmisorVo(ConfigGlobal::mi_region_dl());
        $Anuncio->setEsquemaDestinoVo($esquema_region_stgr_dst);
        $Anuncio->setTextoAnuncioVo($texto_anuncio);
        $Anuncio->setIdiomaVo('');
        $Anuncio->setTablonVo('vest|Estudios');
        $Anuncio->setT_anotado(new DateTimeLocal());
        $Anuncio->setCategoriaVo(Categoria::CAT_AVISO);
        $this->anuncioRepository->Guardar($Anuncio);

        return $error_txt;
    }
}
