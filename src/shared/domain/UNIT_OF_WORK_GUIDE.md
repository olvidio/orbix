# Guía de Uso del Unit of Work

## Descripción

El patrón **Unit of Work** gestiona el despacho de eventos de dominio de forma inteligente:
- **Con transacción explícita**: Acumula eventos y los despacha al hacer commit
- **Sin transacción (modo automático)**: Despacha eventos inmediatamente

Esto garantiza que los eventos se publiquen solo cuando las operaciones tienen éxito.

## Beneficios

1. **Despacho inteligente**: Automático si no hay transacción, diferido si hay transacción
2. **Consistencia transaccional**: En transacciones, los eventos se despachan SOLO si hay commit exitoso
3. **Separación de responsabilidades**: Los repositorios no conocen el EventBus
4. **Flexibilidad**: Funciona con y sin transacciones explícitas
5. **Simplicidad**: Los repositorios solo registran entidades, el UnitOfWork decide cuándo despachar

## Arquitectura del Sistema

```
Repositorio
    ↓
1. Guarda en BD
2. Marca evento: entity->marcarComoNueva()
3. Registra: unitOfWork->registerEntity(entity)
    ↓
UnitOfWork decide:
    ├─ Sin transacción → Despacha INMEDIATAMENTE
    └─ Con transacción → Acumula para commit
```

## Uso en Repositorios (Recomendado: usar Trait)

### Opción A: Usar el Trait `DispatchesDomainEvents` ✅ (Recomendado)

```php
use src\shared\traits\DispatchesDomainEvents;

class PgAsistenteRepository extends ClaseRepository
{
    use HandlesPdoErrors;
    use DispatchesDomainEvents;  // ← Trait para eventos

    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $Asistente): bool
    {
        $bInsert = $this->isNew($id_activ, $id_nom);
        $datosActuales = $bInsert ? [] : $this->datosById($id_activ, $id_nom);

        // ... código de INSERT/UPDATE ...
        $success = $this->PdoExecute($stmt, $aDatos);

        if ($success) {
            // Una línea marca Y registra automáticamente
            if ($bInsert) {
                $this->markAsNew($Asistente, $datosActuales);
            } else {
                $this->markAsModified($Asistente, $datosActuales);
            }
        }

        return $success;
    }

    public function Eliminar(Asistente $Asistente): bool
    {
        $datosActuales = $this->datosById($id_activ, $id_nom);

        // ... código de DELETE ...
        $success = $this->pdoExec($oDbl, $sql);

        if ($success && $datosActuales) {
            $this->markAsDeleted($Asistente, $datosActuales);  // ← Una línea
        }

        return $success;
    }
}
```

**Métodos del Trait:**
- `markAsNew($entity, $datosActuales)` - Para INSERT
- `markAsModified($entity, $datosActuales)` - Para UPDATE
- `markAsDeleted($entity, $datosActuales)` - Para DELETE

### Opción B: Manual (sin Trait)

```php
class PgAsistenteRepository extends ClaseRepository
{
    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $Asistente): bool
    {
        // ... código de guardado ...

        if ($success) {
            // Marcar evento
            if ($bInsert) {
                $Asistente->marcarComoNueva($datosActuales);
            } else {
                $Asistente->marcarComoModificada($datosActuales);
            }

            // Registrar para despacho (inmediato o diferido según contexto)
            $this->unitOfWork->registerEntity($Asistente);
        }

        return $success;
    }
}
```

## Modo Automático (Sin Transacción)

**La mayoría de casos**: Los repositorios funcionan sin transacciones explícitas.

```php
// En un controlador simple
$repository->Guardar($asistente);
// ↑ Los eventos se despachan AUTOMÁTICAMENTE al registrar la entidad
```

El `UnitOfWork` detecta que NO hay transacción activa y despacha los eventos inmediatamente.

## Modo Transaccional (Con Transacción Explícita)

Para **múltiples operaciones** que deben ser atómicas:

```php
// Forma simple: con execute()
$result = $unitOfWork->execute(function($uow) use ($asistente, $repository) {
    $success = $repository->Guardar($asistente);
    // Los eventos se acumulan y se despachan al final si todo OK
    return $success;
});
```

### Control manual de transacciones

Para casos complejos con lógica condicional:

```php
try {
    $unitOfWork->beginTransaction();

    // Operación 1
    $repository->Guardar($asistente1);
    // No necesitas registerEntity - el repositorio ya lo hace

    // Operación 2
    $repository->Guardar($asistente2);

    // Lógica adicional
    if ($algunaCondicion) {
        $repository->Guardar($asistente3);
    }

    // Confirmar y despachar TODOS los eventos acumulados
    $unitOfWork->commit();

} catch (Exception $e) {
    // Revertir BD y NO despachar eventos
    $unitOfWork->rollback();
    throw $e;
}
```

## Ejemplos Completos

### Ejemplo 1: Operación Simple (Modo Automático)

```php
<?php
// apps/asistentes/controller/asistente_update.php

use src\asistentes\domain\contracts\AsistenteRepositoryInterface;

$container = \DI\ContainerSingleton::getInstance();
$repository = $container->get(AsistenteRepositoryInterface::class);

// Obtener datos
$id_activ = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
$id_nom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

// Buscar y modificar
$asistente = $repository->findById($id_activ, $id_nom);
$asistente->setObserv($_POST['observ']);

// Guardar - Los eventos se despachan AUTOMÁTICAMENTE
$success = $repository->Guardar($asistente);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
```

### Ejemplo 2: Múltiples Operaciones (Modo Transaccional)

```php
<?php
// apps/asistentes/controller/asistente_bulk_update.php

use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;

$container = \DI\ContainerSingleton::getInstance();
$repository = $container->get(AsistenteRepositoryInterface::class);
$unitOfWork = $container->get(UnitOfWorkInterface::class);

$asistentes = $_POST['asistentes']; // Array de asistentes a actualizar

try {
    $unitOfWork->execute(function() use ($asistentes, $repository) {
        foreach ($asistentes as $data) {
            $asistente = $repository->findById($data['id_activ'], $data['id_nom']);
            $asistente->setObserv($data['observ']);

            // El repositorio registra automáticamente la entidad
            $repository->Guardar($asistente);
        }

        return true;
    });

    // Todos los eventos se despachan aquí (después del commit)
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Si falla, rollback y NO se despachan eventos
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
```

## Notas Importantes

### ✅ Dos Modos de Operación

1. **Modo Automático (sin transacción)**:
   - Cuando llamas a `repository->Guardar()` directamente
   - Los eventos se despachan INMEDIATAMENTE
   - Usa esto para operaciones simples (90% de los casos)

2. **Modo Transaccional (con transacción explícita)**:
   - Cuando usas `unitOfWork->execute()` o `beginTransaction()`
   - Los eventos se ACUMULAN y se despachan en el commit
   - Usa esto para múltiples operaciones que deben ser atómicas

### 🔑 Reglas Clave

1. **Los repositorios registran automáticamente**: Usan `$this->unitOfWork->registerEntity()` internamente
2. **No necesitas llamar registerEntity() manualmente**: El repositorio ya lo hace (si usas el trait o el patrón correcto)
3. **UnitOfWork es inteligente**: Detecta automáticamente si hay transacción activa
4. **Sin transacción = inmediato**: Los eventos se publican tan pronto como guardas
5. **Con transacción = diferido**: Los eventos esperan al commit

## Migración desde Código Anterior

### ❌ Antes: Despacho directo en repositorio (Incorrecto)

```php
class PgAsistenteRepository {
    protected EventBusInterface $eventBus;

    public function Guardar(Asistente $asistente): bool {
        // ... guardar en BD ...

        // ❌ Despacho directo - viola separación de responsabilidades
        foreach ($asistente->pullDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }

        return true;
    }
}
```

### ✅ Ahora: Registro en UnitOfWork (Correcto)

**Opción recomendada - con Trait:**

```php
class PgAsistenteRepository {
    use DispatchesDomainEvents;  // ← Trait simplifica

    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $asistente): bool {
        // ... guardar en BD ...

        // ✅ Una línea - marca Y registra
        if ($bInsert) {
            $this->markAsNew($asistente, $datosActuales);
        } else {
            $this->markAsModified($asistente, $datosActuales);
        }

        return true;
    }
}
```

**Opción manual - sin Trait:**

```php
class PgAsistenteRepository {
    protected UnitOfWorkInterface $unitOfWork;

    public function Guardar(Asistente $asistente): bool {
        // ... guardar en BD ...

        // ✅ Dos líneas - marca y registra
        $asistente->marcarComoNueva($datosActuales);
        $this->unitOfWork->registerEntity($asistente);  // UnitOfWork decide cuándo despachar

        return true;
    }
}
```

## Diagrama de Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                        CONTROLLER                            │
│                    (Capa de Aplicación)                      │
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
         ┌─────────────────────────────────┐
         │    Llama: repository->Guardar() │
         └─────────────────┬───────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                       REPOSITORY                              │
│                  (Capa de Infraestructura)                    │
│                                                               │
│  1. Guarda en BD                                              │
│  2. entity->marcarComoNueva(datosActuales)                   │
│  3. unitOfWork->registerEntity(entity)                       │
└──────────────────────────┬───────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                      UNIT OF WORK                             │
│              (Coordinador de Eventos)                         │
│                                                               │
│  ┌──────────────────┐          ┌──────────────────┐         │
│  │ Sin transacción  │          │ Con transacción  │         │
│  │   ↓              │          │   ↓              │         │
│  │ Despacha         │          │ Acumula          │         │
│  │ INMEDIATAMENTE   │          │ para COMMIT      │         │
│  └──────────────────┘          └──────────────────┘         │
└──────────────────────────┬───────────────────────────────────┘
                           │
                           ▼
┌──────────────────────────────────────────────────────────────┐
│                       EVENT BUS                               │
│                 (Despachador de Eventos)                      │
│                                                               │
│  Despacha evento → Listeners (ej: RegistrarCambioListener)   │
└───────────────────────────────────────────────────────────────┘
```

## Resumen

- **Repositorio**: Guarda en BD + marca evento + registra en UnitOfWork
- **UnitOfWork**: Decide CUÁNDO despachar (inmediato vs diferido)
- **Entity**: Solo contiene lógica de negocio (no conoce infraestructura)
- **EventBus**: Distribuye eventos a los listeners

## Ver También

- `src/shared/domain/contracts/UnitOfWorkInterface.php` - Interfaz del patrón
- `src/shared/infrastructure/PdoUnitOfWork.php` - Implementación con PDO
- `src/shared/traits/DispatchesDomainEvents.php` - Trait helper para repositorios
- `src/shared/domain/DOMAIN_EVENTS_GUIDE.md` - Guía de eventos de dominio

---

**Última actualización**: 2026-01-30
**Versión**: 2.0
