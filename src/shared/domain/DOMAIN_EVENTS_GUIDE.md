# Guía de Eventos de Dominio

## ¿Qué son los Eventos de Dominio?

Los **eventos de dominio** son notificaciones que una entidad emite cuando ocurre algo importante en el negocio. En este proyecto, los eventos de dominio se usan principalmente para:

1. **Auditoría**: Registrar todos los cambios en la tabla `av_cambios`
2. **Notificaciones**: Avisar a usuarios interesados cuando cambian actividades que les importan
3. **Trazabilidad**: Mantener un historial de quién cambió qué y cuándo

---

## Arquitectura del Sistema de Eventos

```
┌──────────────────┐
│   Controller     │
│   Crea/Modifica  │
│   Entidad        │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│   Repository     │  1. Guarda en BD
│   save()         │  2. Llama a marcarComo*()
└────────┬─────────┘  3. Despacha eventos
         │
         ▼
┌──────────────────┐
│   Entity         │  Métodos disponibles:
│   (Asistente)    │  - marcarComoNueva()
└────────┬─────────┘  - marcarComoModificada()
         │            - marcarComoEliminada()
         │
         ▼
┌──────────────────┐
│  EventBus        │  Distribuye eventos
│  (InMemory)      │  a los listeners
└────────┬─────────┘
         │
         ▼
┌─────────────────────────┐
│ RegistrarCambioListener │  Guarda en av_cambios
│ GestorAvisoCambios      │  (auditoría y notificaciones)
└─────────────────────────┘
```

---

## Clases Principales

### 1. `Entity` (antes `AggregateRoot`)

Clase base para todas las entidades que emiten eventos.

```php
use src\shared\domain\entity\Entity;

class MiEntidad extends Entity {
    private int $iid_activ;
    // ...
}
```

**Proporciona:**
- ✅ `marcarComoNueva()` - Para INSERT
- ✅ `marcarComoModificada()` - Para UPDATE
- ✅ `marcarComoEliminada()` - Para DELETE
- ✅ `pullDomainEvents()` - Para obtener eventos pendientes

---

### 2. `EntidadModificada` (Evento)

El evento que se emite cuando cambia una entidad.

```php
namespace src\shared\domain\events;

final readonly class EntidadModificada {
    public function __construct(
        public string $objeto,        // "Asistente", "ActividadCargo"
        public string $tipoCambio,    // "INSERT", "UPDATE", "DELETE"
        public ?int $idActiv,         // ID actividad (null si no aplica)
        public array $datosNuevos,    // Datos actuales
        public array $datosActuales   // Datos anteriores
    ) {}
}
```

**NOTA IMPORTANTE**: `idActiv` es **opcional** (nullable). No todas las entidades están relacionadas con actividades.

---

### 3. `RegistrarCambioListener` (Listener)

Procesa los eventos y los guarda en la base de datos.

```php
class RegistrarCambioListener {
    public function __invoke(EntidadModificada $event): void {
        $this->gestorCambios->addCanvi(
            $event->objeto,
            $event->tipoCambio,
            $event->idActiv,
            $event->datosNuevos,
            $event->datosActuales
        );
    }
}
```

---

## ¿Cuándo Usar Eventos de Dominio?

### ✅ USAR eventos cuando:

1. **Cambios importantes en entidades clave**
   - Asistentes (inscripciones/bajas en actividades)
   - Cargos de actividades
   - Plazas de actividades
   - Notas de estudiantes

2. **Necesitas auditoría**
   - Saber quién modificó qué y cuándo
   - Trazabilidad de cambios

3. **Necesitas notificaciones**
   - Avisar a usuarios cuando cambian actividades que siguen
   - Alertas de cambios importantes

4. **Entidades relacionadas con actividades**
   - Si la entidad tiene `id_activ`, probablemente necesitas eventos

### ❌ NO usar eventos cuando:

1. **Entidades de configuración**
   - Parámetros del sistema
   - Tablas de catálogo estáticas

2. **Datos temporales**
   - Logs
   - Sesiones
   - Cache

3. **Datos de solo lectura**
   - Vistas materializadas
   - Reportes generados

4. **Alto volumen de cambios triviales**
   - Contadores de visitas
   - Timestamps de "última actividad"

---

## Ejemplo Completo: Crear un Asistente

### 1. **Entidad que extiende `Entity`**

```php
namespace src\asistentes\domain\entity;

use src\shared\domain\entity\Entity;

class Asistente extends Entity {
    private int $iid_activ;
    private int $iid_nom;
    // ... otros atributos

    public function getId_activ(): int {
        return $this->iid_activ;
    }

    // ... getters/setters
}
```

### 2. **Repositorio que registra eventos**

```php
namespace src\asistentes\infrastructure\repositories;

use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\DispatchesDomainEvents;

class PgAsistenteRepository {
    use DispatchesDomainEvents;  // ← Trait helper

    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $asistente): bool {
        $bInsert = $this->isNew($id_activ, $id_nom);
        $datosActuales = $bInsert ? [] : $this->datosById($id_activ, $id_nom);

        // 1. Guardar en BD
        $stmt = $this->oDB->prepare("INSERT INTO d_asistentes_dl ...");
        $stmt->execute([...]);

        // 2. Marcar Y registrar evento (UnitOfWork decide cuándo despachar)
        if ($bInsert) {
            $this->markAsNew($asistente, $datosActuales);
        } else {
            $this->markAsModified($asistente, $datosActuales);
        }

        return true;
    }
}
```

**Nota**: El UnitOfWork despachará los eventos:
- **Inmediatamente** si NO hay transacción activa
- **En el commit** si hay transacción activa

### 3. **Registro en `av_cambios`**

Automático. El listener `RegistrarCambioListener` se encarga:

```sql
INSERT INTO av_cambios (
    id_activ,    -- 12345
    objeto,      -- "Asistente"
    tipo_cambio, -- "INSERT"
    datos_nuevos,-- {"id_nom": 67890, ...}
    datos_actuales, -- []
    id_usuario,  -- Usuario actual
    fecha_cambio -- NOW()
);
```

---

## Entidades Actuales con Eventos

### ✅ Implementadas:
1. **Asistente** (`src/asistentes/domain/entity/Asistente.php`)
2. **ActividadCargo** (`src/actividadcargos/domain/entity/ActividadCargo.php`)

### 🟡 Candidatas para implementar:
1. **ActividadAll** - Cambios en actividades
2. **Nota** - Cambios en notas de estudiantes
3. **PlazaPeticion** - Peticiones de plazas
4. **CartaPresentacion** - Cartas de presentación

### ❌ NO necesitan eventos:
1. **Delegacion** - Configuración estática
2. **TipoActividad** - Catálogo
3. **Situacion** - Catálogo
4. **Config** - Parámetros del sistema

---

## Cómo Agregar Eventos a una Entidad Nueva

### Paso 1: Heredar de `Entity`

```php
use src\shared\domain\entity\Entity;

class MiEntidad extends Entity {
    private int $iid_activ; // Opcional
    // ...
}
```

### Paso 2: Modificar el Repositorio

**Opción A: Con Trait (Recomendado)**

```php
use src\shared\traits\DispatchesDomainEvents;

class PgMiEntidadRepository {
    use DispatchesDomainEvents;

    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(MiEntidad $entidad): bool {
        $bInsert = $this->isNew($entidad->getId());
        $datosActuales = $bInsert ? [] : $this->datosById($entidad->getId());

        // 1. Guardar en BD
        $this->pdoPrepare(...);
        $stmt->execute([...]);

        // 2. Marcar y registrar (una línea)
        if ($bInsert) {
            $this->markAsNew($entidad, $datosActuales);
        } else {
            $this->markAsModified($entidad, $datosActuales);
        }

        return true;
    }
}
```

**Opción B: Manual**

```php
class PgMiEntidadRepository {
    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(MiEntidad $entidad): bool {
        $bInsert = $this->isNew($entidad->getId());
        $datosActuales = $bInsert ? [] : $this->datosById($entidad->getId());

        // 1. Guardar en BD
        $stmt->execute([...]);

        // 2. Marcar evento
        if ($bInsert) {
            $entidad->marcarComoNueva($datosActuales);
        } else {
            $entidad->marcarComoModificada($datosActuales);
        }

        // 3. Registrar en UnitOfWork (despacha automáticamente)
        $this->unitOfWork->registerEntity($entidad);

        return true;
    }
}
```

### Paso 3: Inyectar UnitOfWork

```php
use src\shared\domain\contracts\UnitOfWorkInterface;

class PgMiEntidadRepository {
    protected UnitOfWorkInterface $unitOfWork;

    public function __construct(UnitOfWorkInterface $unitOfWork) {
        $this->unitOfWork = $unitOfWork;
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
    }
}
```

En `dependencies.php`:

```php
use src\shared\domain\contracts\UnitOfWorkInterface;
use function DI\get;

return [
    PgMiEntidadRepository::class => autowire()
        ->constructor(get(UnitOfWorkInterface::class)),
];
```

---

## Detección Automática de `id_activ`

La clase `Entity` detecta automáticamente si una entidad tiene `id_activ`:

```php
protected function tryGetIdActiv(): ?int {
    // Intenta getter
    if (method_exists($this, 'getId_activ')) {
        return $this->getId_activ();
    }

    // Intenta propiedad pública
    if (property_exists($this, 'id_activ')) {
        return $this->id_activ ?? null;
    }

    if (property_exists($this, 'iid_activ')) {
        return $this->iid_activ ?? null;
    }

    return null; // No tiene relación con actividades
}
```

**Resultado:**
- ✅ **Asistente** → `idActiv: 12345` (tiene `getId_activ()`)
- ✅ **Nota** → `idActiv: null` (no tiene relación con actividades)
- ✅ **Usuario** → `idActiv: null` (no tiene relación con actividades)

---

## Preguntas Frecuentes

### ¿Puedo usar eventos sin `id_activ`?
**Sí.** El campo `idActiv` es opcional (nullable). Simplemente no implementes `getId_activ()` en tu entidad.

### ¿Qué pasa si no quiero eventos en una entidad?
**No heredes de `Entity`.** Simplemente crea tu clase sin heredar de nada, o crea una clase base diferente.

### ¿Los eventos se ejecutan en una transacción?
**No automáticamente.** Los eventos se despachan DESPUÉS de guardar en BD. Si el listener falla, el cambio ya está persistido. Para transacciones atómicas, necesitas implementar un patrón de Unit of Work.

### ¿Puedo agregar más listeners?
**Sí.** Registra nuevos listeners en `InMemoryEventBus`. Por ejemplo:

```php
$eventBus->subscribe(
    EntidadModificada::class,
    new EnviarNotificacionEmailListener()
);
```

### ¿Cómo desactivo eventos temporalmente?
Simplemente no llames a `marcarComo*()` en el repositorio. Los eventos solo se emiten si llamas explícitamente a esos métodos.

---

## Buenas Prácticas

1. ✅ **Siempre leer datos actuales antes de UPDATE**
   ```php
   $datosActuales = $existing?->toArray() ?? [];
   $entidad->marcarComoModificada($datosActuales);
   ```

2. ✅ **Despachar eventos DESPUÉS de guardar en BD**
   ```php
   $stmt->execute([...]);  // Primero BD
   $this->dispatchDomainEvents($entidad);  // Luego eventos
   ```

3. ✅ **Usar nombres descriptivos en eventos**
   ```php
   protected function getEntityName(): string {
       return 'AsistenteDl'; // Más específico que "Asistente"
   }
   ```

4. ❌ **NO emitir eventos en getters**
   ```php
   public function getNombre() {
       // ❌ NO hacer esto:
       $this->marcarComoModificada([]);
       return $this->nombre;
   }
   ```

5. ❌ **NO emitir eventos en constructores**
   ```php
   public function __construct(...) {
       // ❌ NO hacer esto:
       $this->marcarComoNueva();
   }
   ```

---

## Migración desde `AggregateRoot`

Si tienes código usando `AggregateRoot`, migra gradualmente:

```php
// Antes:
use src\shared\domain\entity\AggregateRoot;
class MiEntidad extends AggregateRoot { }

// Después:
use src\shared\domain\entity\Entity;
class MiEntidad extends Entity { }
```

`AggregateRoot` sigue funcionando (es un alias de `Entity`), pero está deprecado y será eliminado en futuras versiones.

---

## Patrón Unit of Work (Actualización 2026-01-30)

**IMPORTANTE**: A partir de enero 2026, los repositorios ya NO despachan eventos directamente. Ahora usan el patrón **Unit of Work**.

### ✅ Nuevo patrón (correcto):

```php
class PgAsistenteRepository {
    use DispatchesDomainEvents;  // ← Trait helper

    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $asistente): bool {
        // ... guardar en BD ...

        // Marca Y registra (UnitOfWork decide cuándo despachar)
        $this->markAsNew($asistente, $datosActuales);
    }
}
```

### ❌ Patrón antiguo (deprecado):

```php
class PgAsistenteRepository {
    protected EventBusInterface $eventBus;  // ❌ Ya no se usa

    public function Guardar(Asistente $asistente): bool {
        // ... guardar en BD ...

        // ❌ Despacho directo - deprecado
        foreach ($asistente->pullDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
```

**Ventajas del nuevo patrón**:
- ✅ Despacho inteligente (inmediato vs diferido)
- ✅ Soporte para transacciones
- ✅ Más simple con el trait `DispatchesDomainEvents`
- ✅ Mejor separación de responsabilidades

Ver `src/shared/domain/UNIT_OF_WORK_GUIDE.md` para más detalles.

---

## Recursos Adicionales

- **Código fuente**: `src/shared/domain/entity/Entity.php`
- **Evento**: `src/shared/domain/events/EntidadModificada.php`
- **Listener**: `src/shared/application/listeners/RegistrarCambioListener.php`
- **EventBus**: `src/shared/infrastructure/InMemoryEventBus.php`
- **UnitOfWork**: `src/shared/infrastructure/PdoUnitOfWork.php`
- **Trait helper**: `src/shared/traits/DispatchesDomainEvents.php`
- **Ejemplo completo**: `src/asistentes/infrastructure/repositories/PgAsistenteRepository.php`
- **Guía UnitOfWork**: `src/shared/domain/UNIT_OF_WORK_GUIDE.md`

---

**Última actualización**: 2026-01-30
**Versión**: 2.1
