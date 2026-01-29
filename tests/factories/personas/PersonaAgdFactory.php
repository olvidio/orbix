<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\CeCurso;
use src\personas\domain\value_objects\CeNumber;
use src\personas\domain\value_objects\CeLugarText;
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
 * Factory para crear instancias de PersonaAgd para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaAgdFactory
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
     * Crea una instancia simple de PersonaAgd con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaAgd
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaAgd = new PersonaAgd();
        $oPersonaAgd->setId_auto($id);

        $oPersonaAgd->setId_nom(2001);
        $oPersonaAgd->setIdTablaVo(new PersonaTablaCode('a'));
        $oPersonaAgd->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaAgd->setSituacionVo(new SituacionCode('A'));

        return $oPersonaAgd;
    }

    /**
     * Crea una instancia de PersonaAgd con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaAgd
     */
    public function create(?int $id = null): PersonaAgd
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaAgd = new PersonaAgd();
        $oPersonaAgd->setId_auto($id);

        $oPersonaAgd->setId_nom($faker->numberBetween(2001, 20000));
        $oPersonaAgd->setIdTablaVo(new PersonaTablaCode('a'));
        $oPersonaAgd->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaAgd->setSacd($faker->boolean);
        $oPersonaAgd->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaAgd->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaAgd->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaAgd->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaAgd->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaAgd->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaAgd->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaAgd->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaAgd->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaAgd->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaAgd->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaAgd->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaAgd->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaAgd->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaAgd->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaAgd->setEapVo(new EapText($faker->word));
        $oPersonaAgd->setObservVo(new ObservText($faker->realText()));
        $oPersonaAgd->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaAgd->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaAgd->setEs_publico($faker->boolean);
        $oPersonaAgd->setCeVo(new CeCurso($faker->numberBetween(1, 10)));
        $oPersonaAgd->setCeIniVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaAgd->setCeFinVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaAgd->setCeLugarVo(new CeLugarText($faker->word));

        return $oPersonaAgd;
    }

    /**
     * Crea múltiples instancias de PersonaAgd
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
