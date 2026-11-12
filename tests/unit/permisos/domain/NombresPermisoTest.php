<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use frontend\shared\session\PermisoAccion;
use frontend\shared\session\PermisoOficina;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use src\permisos\domain\MenuDlPermissionBits;
use src\procesos\domain\PermAccionBits;

/**
 * Los nombres de permiso se usan mediante constantes: cada nombre del mapa tiene la suya,
 * no sobra ninguna, y las del frontend apuntan a las de `src`.
 */
final class NombresPermisoTest extends TestCase
{
    /**
     * @return array<string, string> nombre de constante => valor
     */
    private function constantes(string $clase): array
    {
        /** @var array<string, string> $c */
        $c = (new ReflectionClass($clase))->getConstants();

        return $c;
    }

    public function test_cada_nombre_del_mapa_de_oficinas_tiene_su_constante_y_no_sobra_ninguna(): void
    {
        $claves = array_keys(MenuDlPermissionBits::map());
        $valores = array_values($this->constantes(MenuDlPermissionBits::class));

        $this->assertEqualsCanonicalizing($claves, $valores);
        $this->assertCount(count($claves), array_unique($valores), 'ninguna constante repetida');
    }

    public function test_cada_nombre_del_mapa_de_acciones_tiene_su_constante_y_no_sobra_ninguna(): void
    {
        $claves = array_keys(PermAccionBits::map());
        $valores = array_values($this->constantes(PermAccionBits::class));

        $this->assertEqualsCanonicalizing($claves, $valores);
    }

    public function test_el_nombre_de_cada_constante_es_el_valor_en_mayusculas(): void
    {
        foreach ($this->constantes(PermAccionBits::class) as $nombre => $valor) {
            $this->assertSame($nombre, strtoupper($valor));
        }
        foreach ($this->constantes(MenuDlPermissionBits::class) as $nombre => $valor) {
            $esperado = $valor === 'jefeZona' ? 'JEFE_ZONA' : strtoupper($valor);
            $this->assertSame($esperado, $nombre);
        }
    }

    public function test_las_constantes_del_frontend_son_las_de_src(): void
    {
        $this->assertSame($this->constantes(MenuDlPermissionBits::class), $this->constantes(PermisoOficina::class));
        $this->assertSame($this->constantes(PermAccionBits::class), $this->constantes(PermisoAccion::class));
    }

    public function test_el_mapa_de_menus_comparte_los_nombres_de_oficina(): void
    {
        $menus = array_keys(\src\menus\domain\PermisoMenuBits::map());
        $dl = array_keys(MenuDlPermissionBits::map());

        $this->assertEqualsCanonicalizing($dl, array_intersect($dl, $menus), 'todas las oficinas de DL existen también en el mapa de menús');
    }

    /**
     * Regla del proyecto: no escribir el nombre del permiso como literal en las llamadas
     * (un nombre desconocido deniega siempre y el fallo pasaría desapercibido).
     */
    public function test_no_hay_literales_en_las_llamadas_de_permisos(): void
    {
        $raiz = dirname(__DIR__, 4);
        $patron = '/\b(havePermOficina|havePermActiv|onlyPerm|have_perm_activ|have_perm_action|have_perm_oficina|only_perm|permOficina)\(\s*[\'"]/';
        $infractores = [];
        foreach (['src', 'frontend'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/' . $dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile() || !preg_match('/\.(php|phtml|twig)$/', $f->getFilename())) {
                    continue;
                }
                foreach (file($f->getPathname()) ?: [] as $n => $linea) {
                    if (preg_match($patron, $linea)) {
                        $infractores[] = substr($f->getPathname(), strlen($raiz) + 1) . ':' . ($n + 1) . '  ' . trim($linea);
                    }
                }
            }
        }

        $this->assertSame([], $infractores, "Usar PermAccionBits::X / MenuDlPermissionBits::X (frontend: PermisoAccion / PermisoOficina) en lugar de literales:\n" . implode("\n", $infractores));
    }
}
