---
id: "devel_db_admin.migraciones.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Migraciones"
entidades: ["Migraciones"]
acciones: ["listar"]
endpoints: ["/src/devel_db_admin/migraciones_lista_data"]
pantallas: ["frontend/devel_db_admin/controller/migraciones_lista.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesListaData"]
tags: ["data", "devel_db_admin", "lista", "migraciones"]
estado_revision: "generado"
---

# Gestionar Migraciones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `migraciones`.

## Objetivo Funcional

Gestiona Migraciones. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/devel_db_admin/migraciones_lista_data`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/migraciones_lista.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\MigracionesListaData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
