---
id: "devel_db_admin.copiar_esquema.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Copiar Esquema"
entidades: ["CopiarEsquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/copiar_esquema"]
pantallas: ["frontend/devel_db_admin/controller/db_copiar.php"]
casos_uso: ["src\\devel_db_admin\\application\\CopiarEsquema"]
tags: ["copiar", "copiar_esquema", "devel_db_admin", "esquema"]
estado_revision: "generado"
---

# Gestionar Copiar Esquema

Propuesta generada automaticamente a partir de endpoints con prefijo comun `copiar_esquema`.

## Objetivo Funcional

Gestiona CopiarEsquema. Ejecuta {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/copiar_esquema`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_copiar.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\CopiarEsquema`

## Pistas Desde Endpoints

- Ejecuta {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
