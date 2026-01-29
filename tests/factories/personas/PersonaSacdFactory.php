<?php

namespace Tests\factories\personas;

use Faker\Factory;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaSacd;
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
 * Factory para crear instancias de PersonaSacd para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class PersonaSacdFactory
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
     * Crea una instancia simple de PersonaSacd con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): PersonaSacd
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oPersonaPub = new PersonaSacd();
        $oPersonaPub->setId_schema($id);

        $oPersonaPub->setId_nom(10021);
        $oPersonaPub->setIdTablaVo(new PersonaTablaCode('pa'));
        $oPersonaPub->setApellido1Vo(new PersonaApellido1Text('test_apellido1vo'));
        $oPersonaPub->setSituacionVo(new SituacionCode('A'));

        return $oPersonaPub;
    }

    /**
     * Crea una instancia de PersonaSacd con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return PersonaSacd
     */
    public function create(?int $id = null): PersonaSacd
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oPersonaPub = new PersonaSacd();
        $oPersonaPub->setId_schema($id);

        $oPersonaPub->setId_nom($faker->numberBetween(1, 1000));
        $oPersonaPub->setIdTablaVo(new PersonaTablaCode('pn'));
        $oPersonaPub->setDlVo(new DelegacionCode(substr($faker->word, 0, 8)));
        $oPersonaPub->setSacd($faker->boolean);
        $oPersonaPub->setTratoVo(new PersonaTratoCode(substr($faker->word, 0, 5)));
        $oPersonaPub->setNomVo(new PersonaNombreText($faker->firstNameMale()));
        $oPersonaPub->setNx1Vo(new PersonaNx1Text(substr($faker->word, 0, 7)));
        $oPersonaPub->setApellido1Vo(new PersonaApellido1Text($faker->lastName));
        $oPersonaPub->setNx2Vo(new PersonaNx2Text(substr($faker->word, 0, 7)));
        $oPersonaPub->setApellido2Vo(new PersonaApellido2Text($faker->lastName));
        $oPersonaPub->setF_nacimiento(new DateTimeLocal($faker->date()));
        $oPersonaPub->setIdiomaPreferidoVo(new LocaleCode($faker->locale() . ".UTF-8"));
        $oPersonaPub->setSituacionVo(new SituacionCode(strtoupper($faker->randomLetter())));
        $oPersonaPub->setF_situacion(new DateTimeLocal($faker->date()));
        $oPersonaPub->setApelFamVo(new ApelFamText($faker->word));
        $oPersonaPub->setIncVo(new IncCode(substr($faker->word, 0, 2)));
        $oPersonaPub->setF_inc(new DateTimeLocal($faker->date()));
        $oPersonaPub->setNivelStgrVo(new NivelStgrId($faker->randomElement(array_keys(NivelStgrId::getArrayNivelStgr()))));
        $oPersonaPub->setProfesionVo(new ProfesionText($faker->realText(255)));
        $oPersonaPub->setEapVo(new EapText($faker->word));
        $oPersonaPub->setObservVo(new ObservText($faker->realText()));
        $oPersonaPub->setId_ctr($faker->numberBetween(10011, 50000));
        $oPersonaPub->setLugarNacimientoVo(new LugarNacimientoText($faker->realText(255)));
        $oPersonaPub->setEdad($faker->numberBetween(1, 100));
        $oPersonaPub->setProfesor_stgr($faker->boolean);

        return $oPersonaPub;
    }

    /**
     * Crea múltiples instancias de PersonaSacd
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
