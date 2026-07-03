<?php

namespace src\asistentes\application;

use frontend\shared\web\Periodo;
use Psr\Container\ContainerInterface;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\StatusId;
use src\asistentes\application\ListaPlazasConjuntoActividades;
use src\shared\config\ConfigGlobal;

/**
 * Listados conjuntos de plazas/actividades (`lista_asis_conjunto_activ.php`).
 */
final class ListaAsisConjuntoActivData
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{content_html: string}
     */
    public function build(array $input): array
    {
        $Qque = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'que');

        $Qstatus = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'status');
        $Qstatus = $Qstatus === 0 ? StatusId::ACTUAL : $Qstatus;
        $Qid_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ');
        $Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $Qnom_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'nom_activ');
        $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');
        $Qyear = $Qyear === 0 ? (int)date('Y') : $Qyear;
        $Qdl_org = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl_org');
        $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

        if ($Qperiodo === '') {
            $Qperiodo = 'actual';
        }

        $aWhere = [];
        $aOperador = [];

        if ($Qstatus != StatusId::ALL) {
            $aWhere['status'] = $Qstatus;
        }

        if ($Qid_tipo_activ === '') {
            $Qsfsv = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sfsv');
            $Qsasistentes = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sasistentes');
            $Qsactividad = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'sactividad');
            $Qsnom_tipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'snom_tipo');

            $Qssfsv = $Qsfsv;
            $mi_sfsv = ConfigGlobal::mi_sfsv();
            if (empty($Qssfsv)) {
                if ($mi_sfsv == 1) {
                    $Qssfsv = 'sv';
                }
                if ($mi_sfsv == 2) {
                    $Qssfsv = 'sf';
                }
            }
            $ssfsv = $Qssfsv;
            $sasistentes = $Qsasistentes === '' ? '.' : $Qsasistentes;
            $sactividad = $Qsactividad === '' ? '.' : $Qsactividad;
            $snom_tipo = $Qsnom_tipo === '' ? '...' : $Qsnom_tipo;
            $oTipoActiv = new TiposActividades();
            $oTipoActiv->setSfsvText($ssfsv);
            $oTipoActiv->setAsistentesText($sasistentes);
            $oTipoActiv->setActividadText($sactividad);
            $Qid_tipo_activ = $oTipoActiv->getId_tipo_activ();
        } else {
            $oTipoActiv = new TiposActividades($Qid_tipo_activ);
            $ssfsv = $oTipoActiv->getSfsvText();
            $sasistentes = $oTipoActiv->getAsistentesText();
            $sactividad = $oTipoActiv->getActividadText();
        }

        if ($Qid_tipo_activ !== '......') {
            $aWhere['id_tipo_activ'] = '^' . $Qid_tipo_activ;
            $aOperador['id_tipo_activ'] = '~';
        }

        if ($Qid_ubi !== 0) {
            $aWhere['id_ubi'] = $Qid_ubi;
        }

        $oPeriodo = Periodo::conCalendarioDesdeBackend();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();
        if ($Qperiodo === 'desdeHoy') {
            $aWhere['f_fin'] = "'$inicioIso','$finIso'";
            $aOperador['f_fin'] = 'BETWEEN';
        } else {
            $aWhere['f_ini'] = "'$inicioIso','$finIso'";
            $aOperador['f_ini'] = 'BETWEEN';
        }

        if (!empty($Qdl_org)) {
            $aWhere['dl_org'] = $Qdl_org;
        }

        if ($Qnom_activ !== '') {
            $aWhere['nom_activ'] = '%' . $Qnom_activ . '%';
            $aOperador['nom_activ'] = 'ILIKE';
        }

        $Qmodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'modo');
        if ($Qmodo !== '' && $Qmodo === 'publicar') {
            $aWhere['publicado'] = 'f';
        }
        $aWhere['_ordre'] = 'f_ini';

        $mi_dele = ConfigGlobal::mi_delef();

        $oListaPlazasDl = null;
        $oListaPlazasOtras = null;

        if (empty($Qdl_org) || $Qdl_org == $mi_dele) {
            $aWhereDl = $aWhere;
            $aOperDl = $aOperador;
            $aWhereDl['dl_org'] = $mi_dele;

            /** @var ListaPlazasConjuntoActividades $oListaPlazasDl */
            $oListaPlazasDl = $this->container->get(ListaPlazasConjuntoActividades::class);
            $oListaPlazasDl->setMi_dele($mi_dele);
            $oListaPlazasDl->setWhere($aWhereDl);
            $oListaPlazasDl->setOperador($aOperDl);
            $oListaPlazasDl->setId_tipo_activ($Qid_tipo_activ);
            if ($Qque === 'list_cjto_sacd') {
                $oListaPlazasDl->setSacd(true);
            }
        }

        if (empty($Qdl_org) || $Qdl_org != $mi_dele) {
            $aWhereOt = $aWhere;
            $aOperOt = $aOperador;
            if (!empty($Qdl_org)) {
                $aWhereOt['dl_org'] = $Qdl_org;
                $aOperOt['dl_org'] = '=';
            } else {
                $aWhereOt['dl_org'] = $mi_dele;
                $aOperOt['dl_org'] = '!=';
            }

            /** @var ListaPlazasConjuntoActividades $oListaPlazasOtras */
            $oListaPlazasOtras = $this->container->get(ListaPlazasConjuntoActividades::class);
            $oListaPlazasOtras->setMi_dele($mi_dele);
            $oListaPlazasOtras->setWhere($aWhereOt);
            $oListaPlazasOtras->setOperador($aOperOt);
            $oListaPlazasOtras->setId_tipo_activ($Qid_tipo_activ);
        }

        $html = '';
        if ($oListaPlazasDl !== null) {
            $html .= '<h3>' . ucfirst(_('actividades de la dl')) . '</h3>';
            $oListaDl = $oListaPlazasDl->getLista();
            $html .= $oListaDl->listaPaginada();
        }
        if ($oListaPlazasOtras !== null) {
            $html .= '<h3>' . ucfirst(_('actividades de otras dl')) . '</h3>';
            $oListaOtras = $oListaPlazasOtras->getLista();
            $html .= $oListaOtras->listaPaginada();
        }

        return ['content_html' => $html];
    }
}
