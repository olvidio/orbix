<?php

namespace src\encargossacd\application;

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\usuarios\domain\criteria\LocalField;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoField;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\shared\security\HashB;

/**
 * Datos para la lista de encargos (`encargo_select`). El frontend construye
 * la `frontend\shared\web\Lista` y los enlaces; aqui devolvemos unicamente los datos planos
 * de cada fila.
 */
final class EncargoSelectData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private EncargoRepositoryInterface $encargoRepository,
        private LocalRepositoryInterface $localRepository
    ) {
    }

    /**
     * @return array{
     *     filas: list<array{
     *         id_enc: int,
     *         sf_sv: int,
     *         idioma_enc: string,
     *         id_ubi: int,
     *         desc_enc: string,
     *         desc_lugar: string,
     *         seccion: string,
     *         nombre_ubi: string,
     *         idioma: string
     *     }>
     * }
     */
    public function execute(string $desc_enc, int $id_tipo_enc): array
    {
        $oAplicacion = $this->aplicacionService;
        $a_seccion = $oAplicacion->getArraySeccion();

        $criteria = Criteria::create()->orderBy(EncargoField::DescEnc);
        if ($desc_enc !== '') {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(EncargoField::DescEnc, $desc_enc));
        }
        if ($id_tipo_enc !== 0) {
            $criteria = $criteria->andWhere(Filter::eq(EncargoField::IdTipoEnc, $id_tipo_enc));
        }

        $cEncargos = $this->encargoRepository->matching($criteria);

        $filas = [];
        $idiomaCache = [];
        if ($cEncargos !== []) {
            foreach ($cEncargos as $oEncargo) {
                $id_ubi = (int)$oEncargo->getId_ubi();
                $idioma_enc = (string)$oEncargo->getIdioma_enc();
                if ($idioma_enc === '') {
                    $idioma_enc = 'ca_ES.UTF-8';
                }

                if (!array_key_exists($idioma_enc, $idiomaCache)) {
                    $idiomaCache[$idioma_enc] = $this->resolveNomIdioma($idioma_enc);
                }

                $nombre_ubi = '';
                if ($id_ubi !== 0) {
                    $oUbi = Ubi::NewUbi($id_ubi);
                    if ($oUbi !== null) {
                        $nombre_ubi = (string)$oUbi->getNombre_ubi();
                    }
                }

                $sf_sv = (int)$oEncargo->getGrupo_encargo();
                $seccion = '';
                if ($sf_sv !== 0) {
                    $seccion = (string) ($a_seccion[(string) $sf_sv] ?? '?¿?');
                }

                $filas[] = [
                    'id_enc' => (int)$oEncargo->getId_enc(),
                    'ctx_eliminar' => HashB::sign('encargo_ver_eliminar', [
                        'id_enc' => (int)$oEncargo->getId_enc(),
                    ]),
                    'sf_sv' => $sf_sv,
                    'idioma_enc' => $idioma_enc,
                    'id_ubi' => $id_ubi,
                    'desc_enc' => (string)$oEncargo->getDesc_enc(),
                    'desc_lugar' => (string)$oEncargo->getDesc_lugar(),
                    'seccion' => $seccion,
                    'nombre_ubi' => $nombre_ubi,
                    'idioma' => $idiomaCache[$idioma_enc],
                ];
            }
        }

        return ['filas' => $filas];
    }

    private function resolveNomIdioma(string $idioma_enc): string
    {
        $oLocal = $this->localRepository->findById($idioma_enc);
        if ($oLocal !== null) {
            return (string)($oLocal->getNomIdiomaAsString() ?? '');
        }

        $cIdiomas = $this->localRepository->matching(Criteria::create()->where(Filter::eq(LocalField::Idioma, $idioma_enc)));
        if ($cIdiomas !== []) {
            return (string)($cIdiomas[0]->getNomIdiomaAsString() ?? '');
        }

        return '';
    }
}
