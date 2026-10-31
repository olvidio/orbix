<?php

namespace src\asistentes\application;

use frontend\shared\web\Periodo;
use Psr\Container\ContainerInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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

        $criteria = Criteria::create();

        if ($Qstatus != StatusId::ALL) {
            $criteria = $criteria->andWhere(Filter::eq(ActividadAllField::Status, $Qstatus));
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
            $criteria = $criteria->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^' . $Qid_tipo_activ));
        }

        if ($Qid_ubi !== 0) {
            $criteria = $criteria->andWhere(Filter::eq(ActividadAllField::IdUbi, $Qid_ubi));
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
            $criteria = $criteria->andWhere(Filter::between(ActividadAllField::FFin, $inicioIso, $finIso));
        } else {
            $criteria = $criteria->andWhere(Filter::between(ActividadAllField::FIni, $inicioIso, $finIso));
        }

        if ($Qnom_activ !== '') {
            // Antes ILIKE '%texto%'; se busca el texto literal sin distinguir mayúsculas.
            $criteria = $criteria->andWhere(
                Filter::matchesInsensitive(ActividadAllField::NomActiv, preg_quote($Qnom_activ))
            );
        }

        $Qmodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'modo');
        if ($Qmodo !== '' && $Qmodo === 'publicar') {
            $criteria = $criteria->andWhere(Filter::eq(ActividadAllField::Publicado, false));
        }
        $criteria = $criteria->orderBy(ActividadAllField::FIni);

        $mi_dele = ConfigGlobal::mi_delef();

        $oListaPlazasDl = null;
        $oListaPlazasOtras = null;

        if (empty($Qdl_org) || $Qdl_org == $mi_dele) {
            $criteriaDl = $criteria->andWhere(Filter::eq(ActividadAllField::DlOrg, $mi_dele));

            /** @var ListaPlazasConjuntoActividades $oListaPlazasDl */
            $oListaPlazasDl = $this->container->get(ListaPlazasConjuntoActividades::class);
            $oListaPlazasDl->setMi_dele($mi_dele);
            $oListaPlazasDl->setCriteria($criteriaDl);
            $oListaPlazasDl->setId_tipo_activ($Qid_tipo_activ);
            if ($Qque === 'list_cjto_sacd') {
                $oListaPlazasDl->setSacd(true);
            }
        }

        if (empty($Qdl_org) || $Qdl_org != $mi_dele) {
            $criteriaOt = $criteria->andWhere(
                !empty($Qdl_org)
                    ? Filter::eq(ActividadAllField::DlOrg, $Qdl_org)
                    : Filter::neq(ActividadAllField::DlOrg, $mi_dele)
            );

            if ($oListaPlazasDl !== null) {
                // PHP-DI cachea get(): sin clonar, el filtro de otras dl/r pisa el de la propia.
                $oListaPlazasOtras = clone $oListaPlazasDl;
                $oListaPlazasOtras->setSacd(false);
            } else {
                /** @var ListaPlazasConjuntoActividades $oListaPlazasOtras */
                $oListaPlazasOtras = $this->container->get(ListaPlazasConjuntoActividades::class);
            }
            $oListaPlazasOtras->setMi_dele($mi_dele);
            $oListaPlazasOtras->setCriteria($criteriaOt);
            $oListaPlazasOtras->setId_tipo_activ($Qid_tipo_activ);
        }

        $html = '';
        if ($oListaPlazasDl !== null) {
            $html .= '<h3>' . ucfirst(_("actividades de la dl/r")) . '</h3>';
            $oListaDl = $oListaPlazasDl->getLista();
            $html .= $oListaDl->listaPaginada();
        }
        if ($oListaPlazasOtras !== null) {
            $html .= '<h3>' . ucfirst(_("actividades de otras dl/r")) . '</h3>';
            $oListaOtras = $oListaPlazasOtras->getLista();
            $html .= $oListaOtras->listaPaginada();
        }

        return ['content_html' => $html];
    }
}
