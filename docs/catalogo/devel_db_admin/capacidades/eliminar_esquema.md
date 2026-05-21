---
id: "devel_db_admin.eliminar_esquema.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Eliminar Esquema"
entidades: ["EliminarEsquemaDl"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/eliminar_esquema"]
pantallas: ["frontend/devel_db_admin/controller/db_eliminar.php"]
casos_uso: ["src\\devel_db_admin\\application\\EliminarEsquemaDl"]
tags: ["devel_db_admin", "eliminar", "eliminar_esquema", "esquema"]
estado_revision: "generado"
---

# Gestionar Eliminar Esquema

Propuesta generada automaticamente a partir de endpoints con prefijo comun `eliminar_esquema`.

## Objetivo Funcional

Gestiona EliminarEsquemaDl. Ejecuta {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/eliminar_esquema`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_eliminar.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\EliminarEsquemaDl`

## Pistas Desde Endpoints

- Ejecuta {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
