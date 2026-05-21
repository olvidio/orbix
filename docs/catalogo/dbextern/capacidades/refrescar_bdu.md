---
id: "dbextern.refrescar_bdu.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Refrescar Bdu"
entidades: ["RefrescarBduUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/refrescar_bdu"]
pantallas: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\RefrescarBduUseCase"]
tags: ["bdu", "dbextern", "refrescar", "refrescar_bdu"]
estado_revision: "generado"
---

# Gestionar Refrescar Bdu

Propuesta generada automaticamente a partir de endpoints con prefijo comun `refrescar_bdu`.

## Objetivo Funcional

Gestiona RefrescarBduUseCase. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/refrescar_bdu`

## Pantallas Relacionadas

- `frontend/dbextern/controller/sincro_index.php`

## Casos De Uso Detectados

- `src\dbextern\application\RefrescarBduUseCase`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
