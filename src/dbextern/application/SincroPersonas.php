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
                $rta = $oSincroDB->syncro($oPersonaListas, $id_orbix);
                if (is_array($rta)) {
                    $msg .= ($msg !== '' ? "\n" : '') . $rta['error'];
                } elseif ($rta !== '') {
                    $msg .= ($msg !== '' ? "\n" : '') . $rta;
                }
            }
        }

        return ['count' => $i, 'msg' => $msg];
    }
}
