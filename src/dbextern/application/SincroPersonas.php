<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\application\support\SincroDBFactory;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class SincroPersonas
{
    public function __construct(
        private IdMatchPersonaRepositoryInterface $idMatchRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private SincroDBFactory $sincroDBFactory,
    ) {
    }

    /**
     * @return array{count: int, msg: string}
     */
    public function __invoke(string $region, string $dl_listas, string $tipo_persona): array
    {
        $cCentros = $this->centroDlRepository->getCentros();
        $a_centros = [];
        foreach ($cCentros as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $ctr = $oCentro->getNombre_ubi();
            $a_centros[$ctr] = $id_ubi;
        }

        $oSincroDB = $this->sincroDBFactory->create();
        $oSincroDB->setTipo_persona($tipo_persona);
        $oSincroDB->setRegion($region);
        $oSincroDB->setDlListas($dl_listas);
        $oSincroDB->setCentros($a_centros);

        $cPersonasListas = $oSincroDB->getPersonasBDU();
        $i = 0;
        $msg = '';
        foreach ($cPersonasListas as $oPersonaListas) {
            $id_nom_listas = $oPersonaListas->getIdentif();

            $cIdMatch = $this->idMatchRepository->getIdMatchPersonas(['id_listas' => $id_nom_listas]);
            if ($cIdMatch !== []) {
                $i++;
                $id_orbix = $cIdMatch[0]->getId_orbix();
                if ($id_orbix === null) {
                    continue;
                }
                try {
                    $rta = $oSincroDB->syncro($oPersonaListas, $id_orbix);
                } catch (\Throwable $e) {
                    $msg .= ($msg !== '' ? "\n" : '') . sprintf(
                        _('sincronización interrumpida en %s (id listas %s, id orbix %s): %s. Esta ficha y las siguientes no se han guardado.'),
                        $oPersonaListas->getApenom(),
                        (string) $id_nom_listas,
                        (string) $id_orbix,
                        $e->getMessage(),
                    );
                    break;
                }
                if (is_array($rta)) {
                    $msg .= ($msg !== '' ? "\n" : '') . $rta['error'];
                    if (($rta['abort'] ?? false) === true) {
                        break;
                    }
                } elseif ($rta !== '') {
                    $msg .= ($msg !== '' ? "\n" : '') . $rta;
                }
            }
        }

        return ['count' => $i, 'msg' => $msg];
    }
}
