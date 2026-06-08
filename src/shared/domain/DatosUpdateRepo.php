<?php

namespace src\shared\domain;

use src\configuracion\domain\entity\ModuloInstalado;
use src\profesores\domain\entity\ProfesorLatin;
use src\shared\domain\contracts\DatosCrudRepositoryInterface;
use src\shared\domain\contracts\DatosFichaInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use function src\shared\domain\helpers\is_true;

class DatosUpdateRepo
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ?object $oFicha = null;
    /** @var array<string, mixed> */
    private array $Campos = [];
    private ?string $RepositoryInterface = null;

    public function eliminar(): bool|string
    {
        /** @var DatosFichaInterface|null $ficha */
        $ficha = $this->getFicha();
        if ($ficha === null || $this->RepositoryInterface === null) {
            return 'Ficha o repositorio no configurado';
        }

        $oRepository = $this->resolveRepository();
        if ($oRepository->Eliminar($ficha) === false) {
            return $oRepository->getErrorTxt();
        }

        return true;
    }

    public function nuevo(): bool|string
    {
        $aCampos = $this->getCampos();
        /** @var DatosFichaInterface|null $ficha */
        $ficha = $this->getFicha();
        if ($ficha === null) {
            return 'Ficha no configurada';
        }

        foreach ($ficha->getDatosCampos() as $oDatosCampo) {
            $nom_camp = $oDatosCampo->getNom_camp();
            if ($nom_camp === null) {
                continue;
            }
            $tipo = $oDatosCampo->getTipo();
            if ($tipo === 'check') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = false;
                } else {
                    $aCampos[$nom_camp] = is_true($aCampos[$nom_camp]);
                }
            }
            if ($tipo === 'fecha') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = null;
                } else {
                    $aCampos[$nom_camp] = DateTimeLocal::createFromLocal($aCampos[$nom_camp]);
                }
            }
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) {
                $decimalValue = $aCampos[$nom_camp];
                $aCampos[$nom_camp] = str_replace(',', '.', is_scalar($decimalValue) ? (string) $decimalValue : '');
            }

            if (empty($aCampos[$nom_camp])) {
                $aCampos[$nom_camp] = null;
            }

            if ($nom_camp === 'id_nom' || $nom_camp === 'id_ubi' || $nom_camp === 'id_activ') {
                $aCampos[$nom_camp] = $this->Campos['id_pau'];
            }

            $metodo = $oDatosCampo->getMetodoSet() ?? '';
            if (substr($metodo, -2) === 'Vo') {
                $metodo = substr($metodo, 0, -2);
            }
            try {
                $ficha->$metodo($aCampos[$nom_camp]);
            } catch (\Throwable $e) {
                return 'Error al ejecutar ' . $metodo . ' para el campo ' . $nom_camp . ': ' . $e->getMessage();
            }
        }

        $oRepository = $this->resolveRepository();
        $NoNewId = false;
        $new_id = 0;
        if ($ficha instanceof ProfesorLatin) {
            $new_id = $this->Campos['id_pau'];
            $NoNewId = true;
        }
        if ($ficha instanceof ModuloInstalado) {
            $new_id = $this->Campos['id_mod'];
            $NoNewId = true;
        }

        if ($NoNewId === false) {
            $new_id = $oRepository->getNewId();
        }

        $primaryKey = $ficha->getPrimary_key();
        $pkName = is_string($primaryKey) ? $primaryKey : '';
        $pks1 = 'set' . ucfirst($pkName);
        $ficha->$pks1($new_id);

        try {
            $oRepository->Guardar($ficha);
        } catch (\Throwable $e) {
            return 'Error al guardar: ' . $e->getMessage();
        }

        return true;
    }

    public function editar(): bool|string
    {
        $aCampos = $this->getCampos();
        /** @var DatosFichaInterface|null $ficha */
        $ficha = $this->getFicha();
        if ($ficha === null) {
            return 'Ficha no configurada';
        }

        foreach ($ficha->getDatosCampos() as $oDatosCampo) {
            $nom_camp = $oDatosCampo->getNom_camp();
            if ($nom_camp === null) {
                continue;
            }
            $tipo = $oDatosCampo->getTipo();
            if ($tipo === 'check') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = false;
                } else {
                    $aCampos[$nom_camp] = is_true($aCampos[$nom_camp]);
                }
            }
            if ($tipo === 'fecha') {
                if (empty($aCampos[$nom_camp])) {
                    $aCampos[$nom_camp] = null;
                } else {
                    $aCampos[$nom_camp] = DateTimeLocal::createFromLocal($aCampos[$nom_camp]);
                }
            }
            if ($tipo === 'decimal' && !empty($aCampos[$nom_camp])) {
                $decimalValue = $aCampos[$nom_camp];
                $aCampos[$nom_camp] = str_replace(',', '.', is_scalar($decimalValue) ? (string) $decimalValue : '');
            }

            $metodo = $oDatosCampo->getMetodoSet() ?? '';
            if (substr($metodo, -2) === 'Vo') {
                $metodo = substr($metodo, 0, -2);
            }

            if ($tipo !== 'check' && empty($aCampos[$nom_camp])) {
                $aCampos[$nom_camp] = null;
            }
            try {
                $ficha->$metodo($aCampos[$nom_camp]);
            } catch (\Throwable $e) {
                return 'Error al ejecutar ' . $metodo . ' para el campo ' . $nom_camp . ': ' . $e->getMessage();
            }
        }

        $oRepository = $this->resolveRepository();
        if ($oRepository->Guardar($ficha) === false) {
            return $oRepository->getErrorTxt();
        }

        return true;
    }

    public function getFicha(): ?object
    {
        return $this->oFicha;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCampos(): array
    {
        return $this->Campos;
    }

    public function setFicha(?object $oFicha): void
    {
        $this->oFicha = $oFicha;
    }

    /**
     * @param array<string, mixed> $Campos
     */
    public function setCampos(array $Campos): void
    {
        $this->Campos = $Campos;
    }

    public function setRepositoryInterface(?string $repository): void
    {
        $this->RepositoryInterface = $repository;
    }

    private function resolveRepository(): DatosCrudRepositoryInterface
    {
        $repositoryId = $this->RepositoryInterface;
        if ($repositoryId === null || $repositoryId === '') {
            throw new \RuntimeException('RepositoryInterface no configurado');
        }
        if (!interface_exists($repositoryId) && !class_exists($repositoryId)) {
            throw new \RuntimeException('RepositoryInterface invalido: ' . $repositoryId);
        }

        /** @var DatosCrudRepositoryInterface $repository */
        $repository = DependencyResolver::get($repositoryId);

        return $repository;
    }
}
