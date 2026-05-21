---
id: "devel_db_admin.absorber_esquema.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Absorber Esquema"
entidades: ["AbsorberEsquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/absorber_esquema"]
pantallas: ["frontend/devel_db_admin/controller/db_absorber_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\AbsorberEsquema"]
tags: ["absorber", "absorber_esquema", "devel_db_admin", "esquema"]
estado_revision: "generado"
---

# Gestionar Absorber Esquema

Propuesta generada automaticamente a partir de endpoints con prefijo comun `absorber_esquema`.

## Objetivo Funcional

Gestiona AbsorberEsquema. JSON { "lines": string[] } para la absorción de esquema (POST esquema_matriz, esquema_del).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/absorber_esquema`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_absorber_esquema.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\AbsorberEsquema`

## Pistas Desde Endpoints

- JSON `{ "lines": string[] }` para la absorción de esquema (POST `esquema_matriz`, `esquema_del`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
