<?php

namespace certificados\domain;

use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use core\DBPropiedades;
use Exception;
use personas\model\entity\Persona;
use personas\model\entity\TrasladoDl;
use tablonanuncios\domain\AnuncioId;
use tablonanuncios\domain\entity\Anuncio;
use tablonanuncios\domain\repositories\AnuncioRepository;
use ubis\model\entity\GestorDelegacion;
use web\DateTimeLocal;

class CertificadoEnviar
{

    /**
     * @param int $id_item
     * @return string
     */
    public static function enviar(int $id_item): string
    {
        $CertificadoRepository = new CertificadoRepository();
        $oCertificado = $CertificadoRepository->findById($id_item);

        $id_nom = $oCertificado->getId_nom();
        $certificado = $oCertificado->getCertificado();

        $error_txt = '';
        // destino?
        $gesPersona = new Persona();
        /*     $a_lista = [
                            'esquema' => $esquema,
                            'id_nom' => $id_nom,
                            'ape_nom' => $ape_nom,
                            'nombre' => $nombre,
                            'dl_persona' => $dl_persona,
                            'apellido1' => $apellido1,
                            'apellido2' => $apellido2,
                            'situacion' => $situacion,
                         ]
        */
        $a_lista = $gesPersona->buscarEnTodasRegiones($id_nom);
        $b_saltar = FALSE;
        if (empty($a_lista)) {
            $error_txt .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            $b_saltar = TRUE;
        }
        if (count($a_lista) > 1) {
            $error_txt .= "Existe más de una persona con este id, dado de alta:";
            foreach ($a_lista as $dl) {
                $error_txt .= "\n";
                $error_txt .= $dl['esquema'];
            }
            $b_saltar = TRUE;
        }

        if (!$b_saltar) {
            $nombre_apellidos = $a_lista[0]['ape_nom'];
            $dl_destino = $a_lista[0]['dl_persona'];

            $gesDelegacion = new GestorDelegacion();
            try {
                $a_datos_region_stgr = $gesDelegacion->mi_region_stgr($dl_destino);
                $esquema_region_stgr_dst = $a_datos_region_stgr['esquema_region_stgr'];
                $esquema_dl_dst = $a_datos_region_stgr['esquema_dl'];
            } catch (Exception $e) {
                $error_txt .= $e->getMessage() . "\n";
            }

            //1.- saber si está en aquinate
            // comprobar que no es una dl que ya tiene su esquema
            $oDBPropiedades = new DBPropiedades();
            $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(TRUE, TRUE);
            $is_dl_in_orbix = FALSE;
            foreach ($a_posibles_esquemas as $esquema) {
                $row = explode('-', $esquema);
                if ($row[1] === $dl_destino) {
                    $is_dl_in_orbix = TRUE;
                    break;
                }
            }

            //2.- mover $certificado
            if ($is_dl_in_orbix) {
                $oHoy = new DateTimeLocal();
                $oCertificado->setF_enviado($oHoy);
                $oTrasladoDl = new TrasladoDl();
                $oTrasladoDl->setReg_dl_dst($esquema_dl_dst);

                $oTrasladoDl->copiar_certificados_a_dl($oCertificado);
                $error_txt .= $oTrasladoDl->getError();
                //3.- enviar aviso
                $texto_anuncio = sprintf(_("se ha recibido el certificado %s para %s."), $certificado, $nombre_apellidos);
                $Anuncio = new Anuncio();
                $uuid_item = AnuncioId::random();
                $tanotado = new DateTimeLocal();

                $Anuncio->setUuid_item($uuid_item);
                $Anuncio->setUsuarioCreador(ConfigGlobal::mi_usuario());
                $Anuncio->setEsquemaEmisor(ConfigGlobal::mi_region_dl());
                $Anuncio->setEsquemaDestino($esquema_region_stgr_dst);
                $Anuncio->setTextoAnuncio($texto_anuncio);
                $Anuncio->setIdioma('');
                $Anuncio->setTablon('vest|Estudios');
                $Anuncio->setTanotado($tanotado);
                $Anuncio->setCategoria(Anuncio::CAT_AVISO);

                $AnuncioRepository = new AnuncioRepository();
                $AnuncioRepository->Guardar($Anuncio);

            } else {
                $error_txt .= _("Hay que enviar manualmente el certificado. Esta persona no está en aquinate");
            }
        }
        return $error_txt;
    }
}