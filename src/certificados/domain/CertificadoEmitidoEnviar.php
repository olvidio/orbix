<?php

namespace src\certificados\domain;

use core\ConfigGlobal;
use core\DBPropiedades;
use Exception;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\personas\domain\TrasladoDl;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;
use src\tablonanuncios\domain\value_objects\Categoria;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\DateTimeLocal;

class CertificadoEmitidoEnviar
{

    /**
     * @param int $id_item
     * @return string
     */
    public static function enviar(int $id_item): string
    {
        $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
        $oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);

        $error_txt = '';
        $id_nom = $oCertificadoEmitido->getId_nom();
        $b_saltar = false;

        if ($id_nom < 0) {
            $error_txt = _("Es una persona de paso. No se puede enviar. Hay que imprimir.");
            $b_saltar = TRUE;
        } else {
            $certificado = $oCertificadoEmitido->getCertificado();

            // destino?
            $cPersonas = Persona::buscarEnTodasRegiones($id_nom);
            if (empty($cPersonas)) {
                $error_txt .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                $b_saltar = TRUE;
            }
            if (count($cPersonas) > 1) {
                $error_txt .= "Existe más de una persona con este id, dado de alta:";
                foreach ($cPersonas as $oPersona) {
                    $error_txt .= "\n";
                    $error_txt .= $$oPersona->getEsquema();
                }
                $b_saltar = TRUE;
            }
        }

        if (!$b_saltar) {
            $nombre_apellidos = $cPersonas[0]->getApellidosNombre();
            $dl_destino = $cPersonas[0]->getDlVo()->value();

            $gesDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
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
            $is_dl_in_orbix = false;
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
                $oCertificadoEmitido->setF_enviado($oHoy);
                $oTrasladoDl = new TrasladoDl();
                $oTrasladoDl->setReg_dl_dst($esquema_dl_dst);

                $oTrasladoDl->copiar_certificados_a_dl($oCertificadoEmitido);
                $error_txt .= $oTrasladoDl->getError();
                //3.- enviar aviso
                $texto_anuncio = sprintf(_("se ha recibido el certificado %s para %s."), $certificado, $nombre_apellidos);
                $Anuncio = new Anuncio();
                $uuid_itemVo = AnuncioId::random();
                $t_anotado = new DateTimeLocal();

                $Anuncio->setUuid_item($uuid_itemVo);
                $Anuncio->setUsuarioCreadorVo(ConfigGlobal::mi_usuario());
                $Anuncio->setEsquemaEmisorVo(ConfigGlobal::mi_region_dl());
                $Anuncio->setEsquemaDestinoVo($esquema_region_stgr_dst);
                $Anuncio->setTextoAnuncioVo($texto_anuncio);
                $Anuncio->setIdiomaVo('');
                $Anuncio->setTablonVo('vest|Estudios');
                $Anuncio->setT_anotado($t_anotado);
                $Anuncio->setCategoriaVo(Categoria::CAT_AVISO);

                $AnuncioRepository = $GLOBALS['container']->get(AnuncioRepositoryInterface::class);
                $AnuncioRepository->Guardar($Anuncio);

            } else {
                $error_txt .= _("Hay que enviar manualmente el certificado. Esta persona no está en aquinate");
            }
        }
        return $error_txt;
    }
}