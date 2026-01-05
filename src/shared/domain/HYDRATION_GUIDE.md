# Guía de Hidratación de Entidades

## ¿Qué es la Hidratación?

**Hidratación** es el proceso de convertir datos "planos" (como arrays de BD) en objetos de dominio ricos con comportamiento y tipos.

```php
// Datos de BD (array plano)
$data = [
    'id_activ' => 123,
    'nom_activ' => 'Curso PHP',
    'f_ini' => '2026-01-15',
    'dl_org' => 'ca'
];

// ⬇️ HIDRATACIÓN ⬇️

// Entidad de dominio (objeto rico)
$actividad = new ActividadAll();
$actividad->setId_activ(123);
$actividad->setNom_activ('Curso PHP');  // ← Crea ActividadNomText VO
$actividad->setF_ini('2026-01-15');     // ← Crea DateTimeLocal
$actividad->setDl_org('ca');            // ← Crea DelegacionCode VO
```

---

## Problema: Código Duplicado

### ❌ **ANTES** (sin trait Hydratable)

Cada entidad necesita un método `setAllAttributes()` manual:

```php
class ActividadAll {
    public function setAllAttributes(array $aDatos): ActividadAll {
        if (array_key_exists('id_auto', $aDatos)) {
            $this->setId_auto($aDatos['id_auto']);
        }
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_tipo_activ', $aDatos)) {
            $valor = $aDatos['id_tipo_activ'];
            if ($valor instanceof ActividadTipoId) {
                $this->setTipoActividadVo($valor);
            } else {
                $this->setId_tipo_activ($valor);
            }
        }
        // ... 25 campos más (80+ líneas de código repetitivo)
        return $this;
    }
}
```

**Problemas:**
- 📝 80-200 líneas de código boilerplate por entidad
- 🐛 Fácil olvidar campos o cometer errores
- 🔄 Difícil mantener consistencia entre entidades
- ⏱️ Tiempo perdido escribiendo código repetitivo

---

## Solución: Trait `Hydratable`

### ✅ **AHORA** (con trait Hydratable)

```php
use src\shared\domain\traits\Hydratable;

class ActividadAll {
    use Hydratable;  // ← 1 línea

    // Ya tienes fromArray() y setAllAttributes() automáticamente
}
```

**Beneficios:**
- ✅ 1 línea en lugar de 80+
- ✅ Consistente en todas las entidades
- ✅ Soporta Value Objects automáticamente
- ✅ Menos bugs, más mantenible

---

## Cómo Funciona

El trait `Hydratable` detecta **automáticamente** si el valor es primitivo u objeto:

```php
// Estrategia inteligente:
foreach ($data as $key => $value) {
    // 1. ¿Es un objeto (Value Object)?
    if (is_object($value)) {
        // Busca setter específico para VOs: setNomActivVo()
        $this->setNomActivVo($value);  // ← Pasa el VO directamente
    }
    // 2. ¿Es primitivo (string, int, null)?
    else {
        // Busca setter normal: setNomActiv()
        $this->setNomActiv($value);    // ← El setter crea el VO internamente
    }
}
```

### **¿Por qué este orden?**

**Datos de BD (99% de los casos):**
```php
// Desde repositorio → siempre primitivos
$data = ['nom_activ' => 'Curso PHP'];  // ← string
$actividad = ActividadAll::fromArray($data);
// → Llama setNomActiv('Curso PHP')
// → El setter crea: new ActividadNomText('Curso PHP')
```

**Datos de otra entidad (casos especiales):**
```php
// Ya tienes VOs construidos
$vo = new ActividadNomText('Curso PHP');
$data = ['nom_activ' => $vo];  // ← objeto
$actividad = ActividadAll::fromArray($data);
// → Llama setNomActivVo($vo)
// → Usa el VO directamente sin recrearlo
```

### **Conversión de Nombres (snake_case → PascalCase)**

| Clave array | Si es primitivo | Si es objeto |
|-------------|-----------------|--------------|
| `id_activ` | `setIdActiv()` | `setIdActivVo()` |
| `nom_activ` | `setNomActiv()` | `setNomActivVo()` |
| `dl_org` | `setDlOrg()` | `setDlOrgVo()` |
| `f_ini` | `setFIni()` | `setFIniVo()` |
| `tipo_horario` | `setTipoHorario()` | `setTipoHorarioVo()` |

---

## Uso en Repositorios

### **Patrón Recomendado**

```php
namespace src\actividades\infrastructure\repositories;

use src\actividades\domain\entity\ActividadAll;

class PgActividadAllRepository {

    public function findById(int $id): ?ActividadAll {
        $stmt = $this->oDB->prepare("SELECT * FROM a_actividades_all WHERE id_activ = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // ✅ Opción 1: fromArray() (RECOMENDADO - más limpio)
        return ActividadAll::fromArray($row);

        // ✅ Opción 2: setAllAttributes() (COMPATIBILIDAD)
        // return (new ActividadAll())->setAllAttributes($row);
    }

    public function findAll(): array {
        $stmt = $this->oDB->query("SELECT * FROM a_actividades_all");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Hidratar múltiples entidades
        return array_map(fn($row) => ActividadAll::fromArray($row), $rows);
    }
}
```

---

## Dos Formas de Hidratar

### **1. `fromArray()` - Constructor Estático (RECOMENDADO)**

```php
// Uso simple
$actividad = ActividadAll::fromArray($data);

// Uso en map/filter
$actividades = array_map(
    fn($row) => ActividadAll::fromArray($row),
    $rows
);

// Ventaja: Más limpio, menos verbose
```

### **2. `setAllAttributes()` - Método de Instancia (COMPATIBILIDAD)**

```php
// Uso con instancia existente
$actividad = new ActividadAll();
$actividad->setAllAttributes($data);

// O encadenado
$actividad = (new ActividadAll())->setAllAttributes($data);

// Ventaja: Compatible con código legacy, permite modificaciones antes/después
```

---

## Flujo de Hidratación (Diagrama)

```
BASE DE DATOS
     ↓
['nom_activ' => 'Curso PHP']  ← string primitivo
     ↓
fromArray()
     ↓
¿Es objeto el valor?
     ↓ NO (es string)
setNomActiv('Curso PHP')
     ↓
new ActividadNomText('Curso PHP')  ← Crea VO
     ↓
$this->snom_activ = VO
```

```
OTRA ENTIDAD
     ↓
$vo = new ActividadNomText('Curso PHP')
['nom_activ' => $vo]  ← objeto VO
     ↓
fromArray()
     ↓
¿Es objeto el valor?
     ↓ SÍ
setNomActivVo($vo)
     ↓
$this->snom_activ = $vo  ← Usa VO directamente
```

---

## Soporte de Value Objects

El trait detecta automáticamente si necesita usar el setter de VO o el normal:

```php
class ActividadAll {
    use Hydratable;

    private ActividadNomText $snom_activ;
    private DelegacionCode $sdl_org;

    // Setter para primitivos
    public function setNom_activ(string $nom): void {
        $this->snom_activ = new ActividadNomText($nom);  // ← Crea VO
    }

    // Setter para Value Objects (usado si el dato ya es un VO)
    public function setNomActivVo(ActividadNomText $vo): void {
        $this->snom_activ = $vo;
    }
}

// Uso:
$data = ['nom_activ' => 'Curso PHP'];
$actividad = ActividadAll::fromArray($data);
// ↓ Llama a setNomActivVo() primero, si no existe llama a setNom_activ()
// Resultado: $actividad->snom_activ = ActividadNomText('Curso PHP')
```

---

## Método `toArray()`

El trait también proporciona `toArray()` para serializar entidades:

```php
$actividad = ActividadAll::fromArray([
    'id_activ' => 123,
    'nom_activ' => 'Curso PHP',
    'dl_org' => 'ca'
]);

$array = $actividad->toArray();
// [
//     'id_activ' => 123,
//     'nom_activ' => 'Curso PHP',
//     'dl_org' => 'ca',
//     ...
// ]
```

**Cómo funciona:**
1. Usa reflexión para obtener todas las propiedades privadas/protegidas
2. Quita prefijos de tipo (`s`, `i`, `b`, `d`, `o`)
3. Convierte `iid_xxx` → `id_xxx`
4. Llama a getters correspondientes

---

## Migración de Entidades Existentes

### **Paso 1: Agregar el trait**

```php
// ANTES
class MiEntidad {
    public function setAllAttributes(array $aDatos): MiEntidad {
        // 80 líneas de código manual
    }
}

// DESPUÉS
use src\shared\domain\traits\Hydratable;

class MiEntidad {
    use Hydratable;

    // ¡Ya no necesitas setAllAttributes() manual!
    // Opcional: puedes eliminar el método manual o dejarlo comentado
}
```

### **Paso 2: Actualizar repositorio (opcional)**

```php
// ANTES
return (new MiEntidad())->setAllAttributes($row);

// DESPUÉS (más limpio)
return MiEntidad::fromArray($row);
```

---

## Casos Especiales

### **1. Campos con Lógica Personalizada**

Si un campo necesita lógica especial, simplemente **no implementes el setter** y maneja manualmente:

```php
class ActividadAll {
    use Hydratable;

    // Hydratable hidrata automáticamente estos:
    private int $iid_activ;
    private string $snom_activ;

    // Campo con lógica especial (no tiene setter, se maneja manual)
    private ?string $computed_field = null;

    public static function fromArray(array $data): static {
        $instance = (new static())->setAllAttributes($data);

        // Lógica especial después de hidratación
        $instance->computed_field = $data['field1'] . '_' . $data['field2'];

        return $instance;
    }
}
```

### **2. Herencia de Entity**

Si tu entidad hereda de `Entity`, el trait ya está incluido:

```php
use src\shared\domain\entity\Entity;

class Asistente extends Entity {
    // ✅ Ya tiene Hydratable (Entity lo incluye)
    // No necesitas agregar "use Hydratable;"
}
```

### **3. Campos Opcionales/Nullable**

El trait maneja automáticamente `null`:

```php
class ActividadAll {
    private ?int $inum_asistentes = null;

    public function setNum_asistentes(?int $num): void {
        $this->inum_asistentes = $num;  // ← Acepta null
    }
}

$data = ['num_asistentes' => null];
$actividad = ActividadAll::fromArray($data);  // ✅ Funciona
```

---

## Ejemplos Completos

### **Ejemplo 1: Entidad Simple**

```php
use src\shared\domain\traits\Hydratable;

class Usuario {
    use Hydratable;

    private int $iid_usuario;
    private string $snombre;
    private string $semail;

    public function setId_usuario(int $id): void { $this->iid_usuario = $id; }
    public function setNombre(string $nombre): void { $this->snombre = $nombre; }
    public function setEmail(string $email): void { $this->semail = $email; }

    public function getId_usuario(): int { return $this->iid_usuario; }
    public function getNombre(): string { return $this->snombre; }
    public function getEmail(): string { return $this->semail; }
}

// Uso:
$data = ['id_usuario' => 1, 'nombre' => 'Juan', 'email' => 'juan@example.com'];
$usuario = Usuario::fromArray($data);

echo $usuario->getNombre();  // "Juan"
```

### **Ejemplo 2: Entidad con Value Objects**

```php
use src\shared\domain\traits\Hydratable;
use src\actividades\domain\value_objects\ActividadNomText;

class Actividad {
    use Hydratable;

    private int $iid_activ;
    private ActividadNomText $snom_activ;

    public function setId_activ(int $id): void {
        $this->iid_activ = $id;
    }

    public function setNom_activ(string $nom): void {
        $this->snom_activ = new ActividadNomText($nom);  // ← Crea VO desde primitivo
    }

    public function setNomActivVo(ActividadNomText $vo): void {
        $this->snom_activ = $vo;  // ← Recibe VO directamente
    }

    public function getId_activ(): int { return $this->iid_activ; }
    public function getNom_activ(): string { return $this->snom_activ->value(); }
}

// Uso con primitivos (desde BD):
$data = ['id_activ' => 123, 'nom_activ' => 'Curso PHP'];
$actividad = Actividad::fromArray($data);
// ↓ Llama a setNom_activ('Curso PHP')
// ↓ Crea ActividadNomText('Curso PHP')

// Uso con VOs (desde otra entidad):
$vo = new ActividadNomText('Curso PHP');
$actividad = Actividad::fromArray(['id_activ' => 123, 'nom_activ' => $vo]);
// ↓ Llama a setNomActivVo($vo)
// ↓ Usa el VO directamente
```

### **Ejemplo 3: Repositorio Real**

```php
namespace src\actividades\infrastructure\repositories;

use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;

class PgActividadAllRepository implements ActividadAllRepositoryInterface {
    private \PDO $oDB;

    public function __construct(\PDO $oDB) {
        $this->oDB = $oDB;
    }

    public function findById(int $id): ?ActividadAll {
        $stmt = $this->oDB->prepare("
            SELECT * FROM a_actividades_all
            WHERE id_activ = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? ActividadAll::fromArray($row) : null;
    }

    public function findBy(array $criteria): array {
        // Construir WHERE dinámicamente (simplificado)
        $where = implode(' AND ', array_map(fn($k) => "$k = :$k", array_keys($criteria)));

        $stmt = $this->oDB->prepare("
            SELECT * FROM a_actividades_all
            WHERE $where
        ");
        $stmt->execute($criteria);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Hidratar múltiples entidades
        return array_map(
            fn($row) => ActividadAll::fromArray($row),
            $rows
        );
    }
}

// Uso:
$repo = new PgActividadAllRepository($oDB);
$actividad = $repo->findById(123);  // ← Hidratación automática
$actividades = $repo->findBy(['dl_org' => 'ca']);  // ← Múltiples hidrataciones
```

---

## Comparativa: Manual vs Hydratable

### **Código Manual (ActividadAll.php - 80 líneas)**

```php
public function setAllAttributes(array $aDatos): ActividadAll {
    if (array_key_exists('id_auto', $aDatos)) {
        $this->setId_auto($aDatos['id_auto']);
    }
    if (array_key_exists('id_activ', $aDatos)) {
        $this->setId_activ($aDatos['id_activ']);
    }
    if (array_key_exists('id_tipo_activ', $aDatos)) {
        $valor = $aDatos['id_tipo_activ'];
        if ($valor instanceof ActividadTipoId) {
            $this->setTipoActividadVo($valor);
        } else {
            $this->setId_tipo_activ($valor);
        }
    }
    // ... 22 campos más (60+ líneas adicionales)
    return $this;
}
```

### **Con Trait Hydratable (1 línea)**

```php
use Hydratable;
```

**Ahorro:** 79 líneas × 100 entidades = **7,900 líneas de código eliminadas** 🎉

---

## Preguntas Frecuentes

### ¿Qué pasa si mi entidad ya tiene `setAllAttributes()` manual?

**Puedes mantener ambos** (el trait no sobrescribe métodos existentes). Pero lo recomendado es:
1. Eliminar el método manual
2. Confiar en el trait
3. Si necesitas lógica especial, sobrescribe `fromArray()`

### ¿El trait es más lento que el código manual?

**No significativamente.** El trait usa `method_exists()` que está optimizado en PHP. La diferencia es imperceptible (<1ms) y vale la pena por la reducción de código.

### ¿Funciona con herencia?

**Sí.** Si tu clase padre usa `Hydratable`, las clases hijas lo heredan automáticamente.

### ¿Puedo personalizar la hidratación?

**Sí.** Sobrescribe `fromArray()` o `setAllAttributes()`:

```php
class MiEntidad {
    use Hydratable;

    public static function fromArray(array $data): static {
        $instance = (new static())->setAllAttributes($data);

        // Lógica personalizada
        $instance->doSomethingSpecial();

        return $instance;
    }
}
```

### ¿Debo migrar todas las entidades ahora?

**No es urgente.** Migra gradualmente:
1. Nuevas entidades: usa `Hydratable` desde el inicio
2. Entidades existentes: migra cuando las edites
3. No hay prisa, el código manual sigue funcionando

---

## Checklist de Migración

Para migrar una entidad a `Hydratable`:

- [ ] Agregar `use Hydratable;` en la entidad
- [ ] Eliminar método `setAllAttributes()` manual (o comentarlo)
- [ ] Actualizar repositorio para usar `fromArray()` (opcional)
- [ ] Verificar que todos los setters existan y funcionen
- [ ] Probar hidratación con datos reales
- [ ] Verificar que `toArray()` devuelve los campos esperados

---

## Buenas Prácticas

1. ✅ **Usar `fromArray()` en repositorios**
   ```php
   return ActividadAll::fromArray($row);  // ← Recomendado
   ```

2. ✅ **Implementar setters para TODOS los campos**
   ```php
   public function setNom_activ(string $nom): void { ... }
   ```

3. ✅ **Seguir convención de nombres**
   ```php
   // Campo: nom_activ
   // Setter: setNom_activ() o setNomActivVo()
   ```

4. ❌ **NO mezclar hidratación con lógica de negocio en setters**
   ```php
   // ❌ MAL
   public function setNom_activ(string $nom): void {
       $this->snom_activ = $nom;
       $this->sendNotification();  // ← NO hacer esto
   }

   // ✅ BIEN
   public function setNom_activ(string $nom): void {
       $this->snom_activ = new ActividadNomText($nom);
   }
   ```

5. ✅ **Usar tipado fuerte**
   ```php
   public function setId_activ(int $id): void { ... }  // ← Con tipos
   ```

---

## Recursos Adicionales

- **Código fuente**: `src/shared/domain/traits/Hydratable.php`
- **Ejemplo Entity**: `src/shared/domain/entity/Entity.php` (usa Hydratable)
- **Ejemplo real**: `src/actividades/domain/entity/ActividadAll.php`
- **Repositorio ejemplo**: `src/actividades/infrastructure/repositories/PgActividadAllRepository.php`

---

**Última actualización**: 2026-01-02
**Versión**: 2.0
