---
id: "devel_db_admin.crear_esquema.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Crear Esquema"
entidades: ["CrearEsquema", "CrearEsquemaPrecondicionException"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/crear_esquema"]
pantallas: ["frontend/devel_db_admin/controller/db_crear_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearEsquema", "src\\devel_db_admin\\application\\CrearEsquemaPrecondicionException"]
tags: ["crear", "crear_esquema", "devel_db_admin", "esquema"]
estado_revision: "generado"
---

# Gestionar Crear Esquema

Propuesta generada automaticamente a partir de endpoints con prefijo comun `crear_esquema`.

## Objetivo Funcional

Gestiona CrearEsquema, CrearEsquemaPrecondicionException. Ejecuta {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/crear_esquema`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_crear_esquema.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\CrearEsquema`
- `src\devel_db_admin\application\CrearEsquemaPrecondicionException`

## Pistas Desde Endpoints

- Ejecuta {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
