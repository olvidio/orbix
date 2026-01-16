<?php

namespace src\shared\domain\traits;

/**
 * Trait para automatizar la hidratación de entidades desde arrays.
 *
 * Proporciona dos formas de hidratar una entidad:
 * 1. fromArray() - Constructor estático (recomendado para nuevas implementaciones)
 * 2. setAllAttributes() - Método de instancia (compatibilidad con código existente)
 *
 * VENTAJAS:
 * - Reduce código boilerplate (no necesitas escribir setAllAttributes manualmente)
 * - Consistente en toda la aplicación
 * - Soporta Value Objects automáticamente (busca métodos setXxxVo())
 * - Soporta tipos nativos PHP y nullable
 *
 * USO:
 * ```php
 * // Opción 1: Constructor estático (recomendado)
 * $entidad = MiEntidad::fromArray($data);
 *
 * // Opción 2: Método de instancia (compatibilidad)
 * $entidad = (new MiEntidad())->setAllAttributes($data);
 * ```
 *
 * @package orbix
 * @subpackage shared\domain\traits
 * @version 2.0
 * @created 2026-01-02
 */
trait Hydratable
{
    /**
     * Crea una nueva instancia de la entidad hidratada desde un array.
     *
     * Este es el método recomendado para crear entidades desde datos de BD.
     *
     * @param array $aDatos Array asociativo con los datos (ej: resultado de PDO::fetch)
     * @return static Nueva instancia hidratada
     *
     * @example
     * ```php
     * $data = ['id_activ' => 123, 'nom_activ' => 'Curso PHP'];
     * $actividad = ActividadAll::fromArray($data);
     * ```
     */
    public static function fromArray(array $aDatos): static
    {
        return (new static())->setAllAttributes($aDatos);
    }

    /**
     * Establece los atributos de la entidad a partir de un array asociativo.
     *
     * ESTRATEGIA DE HIDRATACIÓN (prioridades):
     * 1. Si el valor es un objeto → Busca setXxxVo() para pasar VOs directamente
     * 2. Si el valor es primitivo → Busca setXxx() (setter que acepta primitivos)
     * 3. Si no existe setXxx() → Busca setXxxVo() e intenta pasarle el primitivo
     *    (permite que el setter Vo cree el VO internamente desde el primitivo)
     *
     * Esto permite máxima flexibilidad:
     * - Datos de BD (primitivos) pueden usar setXxx() o setXxxVo()
     * - VOs ya creados se pasan directamente con setXxxVo()
     *
     * Conversión de nombres (snake_case → PascalCase):
     * - 'id_activ' → setIdActiv() o setIdActivVo()
     * - 'nom_activ' → setNomActiv() o setNomActivVo()
     * - 'dl_org' → setDlOrg() o setDlOrgVo()
     *
     * @param array $aDatos Array asociativo con los datos
     * @return $this Para encadenamiento fluido
     *
     * @example
     * ```php
     * // Caso 1: Desde BD con setter primitivo
     * $data = ['id_activ' => 123, 'nom_activ' => 'Curso PHP'];
     * $actividad = ActividadAll::fromArray($data);
     * // Llama a setIdActiv(123) y setNomActiv('Curso PHP')
     *
     * // Caso 2: Desde BD sin setter primitivo (solo setXxxVo)
     * $data = ['nom_activ' => 'Curso PHP'];
     * $actividad = ActividadAll::fromArray($data);
     * // Llama a setNomActivVo('Curso PHP'), que crea el VO internamente
     *
     * // Caso 3: Con VOs ya creados
     * $data = ['nom_activ' => new ActividadNomText('Curso PHP')];
     * $actividad = ActividadAll::fromArray($data);
     * // Llama a setNomActivVo($vo) directamente
     * ```
     */
    public function setAllAttributes(array $aDatos): static
    {
        foreach ($aDatos as $key => $value) {
            // Empezar con mayúscula (id_activ → Id_activ)
            $methodNamePrimitive = ucfirst($key);
            // Convertir snake_case a PascalCase (id_activ → IdActiv)
            $methodNameVo = str_replace('_', '', ucwords($key, '_'));

            // Prioridad 1: Si el valor es un objeto (VO), buscar setter específico para VOs
            if (is_object($value)) {
                $methodVo = 'set' . $methodNameVo . 'Vo';
                if (method_exists($this, $methodVo)) {
                    $this->$methodVo($value);
                    continue;
                }
            }

            // Prioridad 2: Buscar setter normal para primitivos
            $method = 'set' . $methodNamePrimitive;
            if (method_exists($this, $method)) {
                $this->$method($value);
                continue;
            }

            // Prioridad 3: Si no hay setter primitivo, intentar con setXxxVo()
            // Esto permite que setXxxVo() acepte tanto VOs como primitivos
            $methodVo = 'set' . $methodNameVo . 'Vo';
            if (method_exists($this, $methodVo)) {
                $this->$methodVo($value);
            }
        }
        return $this;
    }

    /**
     * Convierte la entidad a un array asociativo basándose en sus propiedades privadas.
     * Sigue la convención de prefijos: s (string), i (int), b (bool), d (date), o (object/vo).
     *
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
        $data = [];

        foreach ($properties as $property) {
            $name = $property->getName();

            $method = 'get' . ucfirst($name);
            if (!method_exists($this, $method)) {
                // Probar con is... para booleanos
                $method = 'is' . ucfirst($name);
            }

            if (method_exists($this, $method)) {
                $data[$name] = $this->$method();
            }
        }

        return $data;
    }

    /**
     * Convierte la entidad a un array asociativo con valores primitivos para base de datos.
     *
     * ESTRATEGIA DE CONVERSIÓN (prioridades):
     * 1. Busca getXxxVo() y extrae value() si es un VO (método preferido)
     * 2. Busca isXxx() para propiedades booleanas
     * 3. Busca getXxx() estándar (fallback, incluye deprecated)
     * 4. Convierte DateTimeLocal/TimeLocal a strings formato BD
     * 5. Convierte arrays PHP a formato PostgreSQL con array_php2pg()
     * 6. Aplica conversiones especiales definidas en $converters (JSON, timestamp, etc.)
     *
     * @param array $converters Array opcional con conversiones especiales por campo
     *          Formato: ['campo' => callable] donde callable recibe el valor y devuelve convertido
     *          Ejemplo: ['timestamp_cambio' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
     *                    'json_fases_sv' => fn($v) => (new ConverterJson($v, false))->toPg(false),
     *                   ]);
     * @return array Array con valores primitivos listos para INSERT/UPDATE
     *
     * @example
     * ```php
     * $actividad = ActividadAll::fromArray($data);
     * $dbData = $actividad->toArrayForDatabase();
     * // ['id_activ' => 123, 'nom_activ' => 'Curso PHP', 'precio' => 50.00]
     *
     * // Con conversiones especiales
     * $dbData = $cambio->toArrayForDatabase([
     *     'timestamp_cambio' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
     *     'json_fases_sv' => fn($v) => (new ConverterJson($v, false))->toPg(false),
     * ]);
     * ```
     */
    public function toArrayForDatabase(array $converters = []): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
        $data = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            if ($name === 'id_auto') { continue; }
            if ($name === 'id_schema') { continue; }
            $value = null;
            $isBoolean = false; // Flag para saber si el campo es booleano

            // Convertir snake_case a PascalCase para buscar métodos Vo
            $methodNameVo = str_replace('_', '', ucwords($name, '_'));

            // Prioridad 1: Buscar getter Vo (getXxxVo) y extraer value()
            $methodVo = 'get' . $methodNameVo . 'Vo';
            if (method_exists($this, $methodVo)) {
                $vo = $this->$methodVo();
                // Si es un VO con método toArray() (para VOs que encapsulan arrays)
                if (is_object($vo) && method_exists($vo, 'toArray')) {
                    $value = $vo->toArray();
                }
                // Si es un VO con método value(), extraerlo
                elseif (is_object($vo) && method_exists($vo, 'value')) {
                    $value = $vo->value();
                }
                // Si es un VO tipo Dinero con asFloat()
                elseif (is_object($vo) && method_exists($vo, 'asFloat')) {
                    $value = $vo->asFloat();
                }
                // Si es DateTimeLocal/TimeLocal, convertir a string
                elseif (is_object($vo) && method_exists($vo, 'format')) {
                    $value = $vo->format('Y-m-d H:i:s');
                }
                // Si es un objeto genérico, usar toString si existe
                elseif (is_object($vo) && method_exists($vo, '__toString')) {
                    $value = (string) $vo;
                }
                // Si es null o ya es primitivo, usarlo directamente
                else {
                    $value = $vo;
                }
            }
            // Prioridad 2: Buscar getter de boolean (isXxx)
            elseif (method_exists($this, 'is' . ucfirst($name))) {
                $value = $this->{'is' . ucfirst($name)}();
                $isBoolean = true; // Marcar como booleano
            }
            // Prioridad 3: Fallback a getter estándar (getXxx) - incluye deprecated
            else {
                $method = 'get' . ucfirst($name);
                if (method_exists($this, $method)) {
                    $value = $this->$method();
                }
            }

            // Aplicar conversiones especiales primero (tienen prioridad máxima)
            if (isset($converters[$name]) && is_callable($converters[$name])) {
                $value = $converters[$name]($value);
            }
            // Para booleanos: null o false => 'false', true => 'true'
            elseif ($isBoolean) {
                $value = ($value === true) ? 'true' : 'false';
            }
            // Convertir tipos especiales a formato BD (si no hay converter específico)
            elseif ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }
            // Para TimeLocal sin fecha
            elseif (is_object($value) && method_exists($value, 'format') && !($value instanceof \DateTimeInterface)) {
                // Asumir que es TimeLocal
                $value = $value->format('H:i:s');
            }
            // Para arrays PHP, convertir a formato PostgreSQL
            elseif (is_array($value)) {
                $value = \core\array_php2pg($value);
            }
            // Para booleanos que no vengan de isXxx() (detectados por tipo)
            elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $data[$name] = $value;
        }

        // Aplicar core\poner_null para convertir strings vacíos a NULL
        array_walk($data, 'core\poner_null');

        return $data;
    }
}
