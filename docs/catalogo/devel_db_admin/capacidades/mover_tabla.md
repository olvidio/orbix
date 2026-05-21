---
id: "devel_db_admin.mover_tabla.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Mover Tabla"
entidades: ["MoverTabla"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/mover_tabla"]
pantallas: ["frontend/devel_db_admin/controller/db_mover.php"]
casos_uso: ["src\\devel_db_admin\\application\\MoverTabla"]
tags: ["devel_db_admin", "mover", "mover_tabla", "tabla"]
estado_revision: "generado"
---

# Gestionar Mover Tabla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `mover_tabla`.

## Objetivo Funcional

Gestiona MoverTabla. Lista esquemas con la tabla y ejecuta {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/mover_tabla`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_mover.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\MoverTabla`

## Pistas Desde Endpoints

- Lista esquemas con la tabla y ejecuta {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
