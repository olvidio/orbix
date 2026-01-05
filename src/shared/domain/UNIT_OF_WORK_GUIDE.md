# Guía de Uso del Unit of Work

## Descripción

El patrón **Unit of Work** permite gestionar transacciones de base de datos y el despacho automático de eventos de dominio. Los eventos solo se despachan si la transacción tiene éxito.

## Beneficios

1. **Consistencia transaccional**: Los eventos se despachan SOLO si la operación de BD tiene éxito
2. **Separación de responsabilidades**: Los repositorios no conocen el EventBus
3. **Centralizado**: Un solo punto para gestionar transacciones + eventos
4. **Automático**: Los eventos se despachan automáticamente al hacer commit

## Uso Básico

### 1. Inyectar el Unit of Work en tu controlador

```php
use src\shared\domain\contracts\UnitOfWorkInterface;

$unitOfWork = $container->get(UnitOfWorkInterface::class);
```

### 2. Ejecutar operaciones dentro de una transacción

```php
// Forma simple: con execute()
$result = $unitOfWork->execute(function($uow) use ($asistente, $repository) {
    // Guardar la entidad
    $success = $repository->Guardar($asistente);

    // Registrar la entidad para despachar eventos
    $uow->registerEntity($asistente);

    return $success;
});
// Si todo va bien, se hace commit y se despachan los eventos automáticamente
```

### 3. Control manual de transacciones

Para casos más complejos:

```php
try {
    $unitOfWork->beginTransaction();

    // Operación 1
    $repository->Guardar($asistente1);
    $unitOfWork->registerEntity($asistente1);

    // Operación 2
    $repository->Guardar($asistente2);
    $unitOfWork->registerEntity($asistente2);

    // Confirmar y despachar eventos
    $unitOfWork->commit();

} catch (Exception $e) {
    // Revertir sin despachar eventos
    $unitOfWork->rollback();
    throw $e;
}
```

## Ejemplo Completo en un Controlador

```php
<?php
// apps/asistentes/controller/asistente_update.php

use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;

$container = \DI\ContainerSingleton::getInstance();
$asistenteRepository = $container->get(AsistenteRepositoryInterface::class);
$unitOfWork = $container->get(UnitOfWorkInterface::class);

// Obtener datos del request
$id_activ = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
$id_nom = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

// Buscar entidad
$asistente = $asistenteRepository->findById($id_activ, $id_nom);

// Modificar datos
$asistente->setObserv($_POST['observ']);
$asistente->setEncargo($_POST['encargo']);

// Guardar dentro de una transacción
try {
    $success = $unitOfWork->execute(function($uow) use ($asistente, $asistenteRepository) {
        $result = $asistenteRepository->Guardar($asistente);

        if ($result) {
            $uow->registerEntity($asistente);
        }

        return $result;
    });

    if ($success) {
        echo json_encode(['success' => true]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
```

## Múltiples Operaciones

Para múltiples operaciones en una sola transacción:

```php
$unitOfWork->execute(function($uow) use ($entities, $repositories) {
    foreach ($entities as $entity) {
        $repositories['asistente']->Guardar($entity->getAsistente());
        $uow->registerEntity($entity->getAsistente());

        $repositories['actividad']->Guardar($entity->getActividad());
        $uow->registerEntity($entity->getActividad());
    }

    return true;
});
```

## Notas Importantes

1. **Los repositorios NO despachan eventos**: Solo marcan los eventos en las entidades
2. **Siempre registrar entidades**: Después de guardar, registra la entidad con `registerEntity()`
3. **Eventos solo si hay commit**: Si hay rollback, no se despachan eventos
4. **Una transacción por request**: En la mayoría de casos, usa `execute()` que maneja todo automáticamente

## Migración desde el Código Anterior

### Antes (en el repositorio):
```php
// ❌ El repositorio despachaba eventos (INCORRECTO)
public function Guardar(Asistente $asistente): bool {
    // ... código de guardar ...
    $this->eventBus->dispatch($event); // ❌ Acoplado a infraestructura
    return true;
}
```

### Ahora (en el controlador):
```php
// ✅ El controlador usa Unit of Work (CORRECTO)
$unitOfWork->execute(function($uow) use ($asistente, $repository) {
    $repository->Guardar($asistente);
    $uow->registerEntity($asistente); // ✅ Eventos se despachan automáticamente
    return true;
});
```

## Arquitectura

```
Controller (Aplicación)
    ↓
UnitOfWork (Infraestructura compartida)
    ↓
Repository (Infraestructura)
    ↓
Entity (Dominio) → Registra eventos
    ↓
EventBus (Infraestructura) ← UnitOfWork despacha tras commit
```

## Ver También

- `src/shared/domain/contracts/UnitOfWorkInterface.php` - Interfaz del patrón
- `src/shared/infrastructure/PdoUnitOfWork.php` - Implementación con PDO
- `src/shared/domain/DOMAIN_EVENTS_GUIDE.md` - Guía de eventos de dominio
