<?php

namespace src\notas\application;


use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Prepara los datos para elegir a que persona (con el mismo primer
 * apellido) se copiara la tessera de otra persona.
 *
 * Busca coincidencias de apellido1 en numerarios y en agregados.
 * Devuelve `['nom' => string, 'posibles_personas' => [id_nom => nombre]]`
 * con sufijo ` (n)` o ` (agd)` segun el tipo.
 * Lanza `RuntimeException` si no encuentra la persona origen ni como
 * numerario ni como agregado.
 */
final class TesseraCopiarSelectData
{

    public function __construct(
        private readonly PersonaNRepositoryInterface $personaNRepository,
        private readonly PersonaAgdRepositoryInterface $personaAgdRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_nom): array
    {
        $repoN = $this->personaNRepository;
        $repoAgd = $this->personaAgdRepository;

        $oPersona = $repoN->findById($id_nom);
        if ($oPersona === null) {
            $oPersona = $repoAgd->findById($id_nom);
            if ($oPersona === null) {
                throw new \RuntimeException(sprintf(_("No existe una persona con id_nom: %s"), $id_nom));
            }
        }
        $apellido1 = $oPersona->getApellido1();
        $nom = $oPersona->getNombreApellidos();

        $posibles = [];
        $criterioApellido = Criteria::create()->where(Filter::eq(PersonaField::Apellido1, $apellido1));
        foreach ($repoN->matching($criterioApellido) as $oPer) {
            if ($oPer->getId_nom() === $id_nom) {
                continue;
            }
            $posibles[$oPer->getId_nom()] = $oPer->getNombreApellidos() . ' (n)';
        }
        foreach ($repoAgd->matching($criterioApellido) as $oPer) {
            if ($oPer->getId_nom() === $id_nom) {
                continue;
            }
            $posibles[$oPer->getId_nom()] = $oPer->getNombreApellidos() . ' (agd)';
        }
        asort($posibles, SORT_STRING | SORT_FLAG_CASE);

        return [
            'nom' => $nom,
            'posibles_personas' => $posibles,
        ];
    }
}
