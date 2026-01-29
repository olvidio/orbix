<?php

namespace Tests\factories\menus;

use Faker\Factory;
use src\menus\domain\entity\TemplateMenu;
use src\menus\domain\value_objects\TemplateMenuName;

/**
 * Factory para crear instancias de TemplateMenu para tests
 * Generado automáticamente - puede ser modificado según necesidades
 */
class TemplateMenuFactory
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
     * Crea una instancia simple de TemplateMenu con datos mínimos
     * Útil para tests que no requieren datos complejos
     */
    public function createSimple(?int $id = null): TemplateMenu
    {
        $id = $id ?? (9900000 + random_int(1000, 9999));
        $oTemplateMenu = new TemplateMenu();
        $oTemplateMenu->setId_template_menu($id);

        $oTemplateMenu->setNombreVo(new TemplateMenuName('test_nombre_vo'));

        return $oTemplateMenu;
    }

    /**
     * Crea una instancia de TemplateMenu con datos realistas usando Faker
     * @param int|null $id ID específico o null para generar uno aleatorio
     * @return TemplateMenu
     */
    public function create(?int $id = null): TemplateMenu
    {
        $faker = Factory::create('es_ES');
        $id = $id ?? (9900000 + random_int(1000, 9999));

        $oTemplateMenu = new TemplateMenu();
        $oTemplateMenu->setId_template_menu($id);

        $oTemplateMenu->setNombreVo(new TemplateMenuName($faker->name));

        return $oTemplateMenu;
    }

    /**
     * Crea múltiples instancias de TemplateMenu
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
