<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaNax;
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
 * Factory para crear instancias de PersonaNax para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaNaxFactory
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
     * Crea una instancia simple de PersonaNax con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaNax
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaNax = new PersonaNax();
        $oPersonaNax->setId_auto($id);

        $oPersonaNax->setId_nom(2001);
        $oPersonaNax->setIdTablaVo(new PersonaTablaCode('nax'));
        $oPersonaNax->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaNax->setSituacionVo(new SituacionCode('A'));

        return $oPersonaNax;
    }

    /**
     * Crea una instancia de PersonaNax con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaNax
     */
    public function create(?int $id = null): PersonaNax
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaNax = new PersonaNax();
        $oPersonaNax->setId_auto($id);

        $oPersonaNax->setId_nom($faker->numberBetween(2001, 20000));
        $oPersonaNax->setIdTablaVo(new PersonaTablaCode('nax'));
        $oPersonaNax->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaNax->setSacd($faker->boolean);
        $oPersonaNax->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaNax->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaNax->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaNax->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaNax->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaNax->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaNax->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaNax->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaNax->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaNax->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaNax->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaNax->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaNax->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaNax->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaNax->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaNax->setEapVo(new EapText($faker->word));
        $oPersonaNax->setObservVo(new ObservText($faker->realText()));
        $oPersonaNax->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaNax->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaNax->setEs_publico($faker->boolean);
        $oPersonaNax->setCeVo(new CeCurso($faker->numberBetween(1, 10)));
        $oPersonaNax->setCeIniVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaNax->setCeFinVo(new CeNumber($faker->numberBetween(1, 10)));
        $oPersonaNax->setCeLugarVo(new CeLugarText($faker->word));

        return $oPersonaNax;
    }

    /**
     * Crea múltiples instancias de PersonaNax
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
