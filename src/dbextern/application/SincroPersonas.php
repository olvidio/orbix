<?php

namespace src\dbextern\application;

use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\SincroDB;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class SincroPersonas
{
    private IdMatchPersonaRepositoryInterface $idMatchRepository;
    private CentroDlRepositoryInterface $centroDlRepository;

    public function __construct(
        IdMatchPersonaRepositoryInterface $idMatchRepository,
        CentroDlRepositoryInterface       $centroDlRepository
    )
    {
        $this->idMatchRepository = $idMatchRepository;
        $this->centroDlRepository = $centroDlRepository;
    }

    /**
     * Sincroniza todas las personas unidas de una DL.
     *
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
        // SincroDB::syncro usa $GLOBALS['a_centros']
        $GLOBALS['a_centros'] = $a_centros;

        $oSincroDB = new SincroDB();
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
            if (!empty($cIdMatch[0]) && !empty($cIdMatch)) {
                $i++;
                $id_orbix = $cIdMatch[0]->getId_orbix();
                $rta = $oSincroDB->syncro($oPersonaListas, $id_orbix);
                if (!empty($rta)) {
                    $msg .= !empty($msg) ? "\n" : '';
                    $msg .= $rta;
                }
            }
        }

        return ['count' => $i, 'msg' => $msg];
    }
}
