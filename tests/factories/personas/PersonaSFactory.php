<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeLugarText;
use src\personas\domain\value_objects\CeNumber;
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
 * Factory para crear instancias de PersonaS para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaSFactory
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
     * Crea una instancia simple de PersonaS con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaS
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaS = new PersonaS();
        $oPersonaS->setId_auto($id);

        $oPersonaS->setId_nom(2001);
        $oPersonaS->setIdTablaVo(new PersonaTablaCode('s'));
        $oPersonaS->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaS->setSituacionVo(new SituacionCode('A'));

        return $oPersonaS;
    }

    /**
     * Crea una instancia de PersonaS con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaS
     */
    public function create(?int $id = null): PersonaS
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaS = new PersonaS();
        $oPersonaS->setId_auto($id);

        $oPersonaS->setId_nom($faker->numberBetween(2001, 20000));
        $oPersonaS->setIdTablaVo(new PersonaTablaCode('s'));
        $oPersonaS->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaS->setSacd($faker->boolean);
        $oPersonaS->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaS->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaS->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaS->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaS->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaS->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaS->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaS->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaS->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaS->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaS->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaS->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaS->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaS->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaS->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaS->setEapVo(new EapText($faker->word));
        $oPersonaS->setObservVo(new ObservText($faker->realText()));
        $oPersonaS->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaS->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaS->setEs_publico($faker->boolean);
        $oPersonaS->setCeVo(new CeCurso($faker->numberBetween(1, 10)));
        $oPersonaS->setCeIniVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaS->setCeFinVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaS->setCeLugarVo(new CeLugarText($faker->word));

        return $oPersonaS;
    }

    /**
     * Crea múltiples instancias de PersonaS
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
