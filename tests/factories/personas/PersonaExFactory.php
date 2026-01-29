<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\EapText;
use src\personas\domain\value_objects\IncCode;
use src\personas\domain\value_objects\LugarNacimientoText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaApellido2Text;
use src\personas\domain\value_objects\PersonaNombreText;
use src\personas\domain\value_objects\PersonaNx1Text;
use src\personas\domain\value_objects\PersonaNx2Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\PersonaTratoCode;
use src\personas\domain\value_objects\ProfesionText;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\LocaleCode;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * Factory para crear instancias de PersonaEx para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaExFactory
{
    private int $count = 1;

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Crea una instancia simple de PersonaEx con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaEx
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaEx = new PersonaEx();
        $oPersonaEx->setId_auto($id);

        $oPersonaEx->setId_nom(10021);
        $oPersonaEx->setIdTablaVo(new PersonaTablaCode('pa'));
        $oPersonaEx->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaEx->setSituacionVo(new SituacionCode('A'));

        return $oPersonaEx;
    }

    /**
     * Crea una instancia de PersonaEx con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaEx
     */
    public function create(?int $id = null): PersonaEx
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaEx = new PersonaEx();
        $oPersonaEx->setId_auto($id);

        $oPersonaEx->setId_nom($faker->numberBetween(1, 1000));
        $oPersonaEx->setIdTablaVo(new PersonaTablaCode('pn'));
        $oPersonaEx->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaEx->setSacd($faker->boolean);
        $oPersonaEx->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaEx->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaEx->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaEx->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaEx->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaEx->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaEx->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaEx->setIdiomaPreferidoVo(new LocaleCode($faker->locale().".UTF-8"));
        $oPersonaEx->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaEx->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaEx->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaEx->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaEx->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaEx->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaEx->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaEx->setEapVo(new EapText($faker->word));
        $oPersonaEx->setObservVo(new ObservText($faker->realText()));
        $oPersonaEx->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaEx->setEdad($faker->numberBetween(1, 100));
        $oPersonaEx->setProfesor_stgr($faker->boolean);

        return $oPersonaEx;
    }

    /**
     * Crea múltiples instancias de PersonaEx
     * @param int $count Número de instancias a crear
     * @param int|null $startId ID inicial (se incrementará)
     * @return array
     */
    public function createMany(int $count, ?int $startId = null): array
    {
        $startId = $startId ?? (9900000 + random_int(1000, 9999));
        $instances = [];

        for ($i = 0; $i < $count; $i++) {
            $instances[] = $this->create($startId + $i);
        }

        return $instances;
    }
}
