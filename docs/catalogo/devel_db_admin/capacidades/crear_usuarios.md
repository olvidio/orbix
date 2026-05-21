---
id: "devel_db_admin.crear_usuarios.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Crear Usuarios"
entidades: ["CrearUsuarios"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/crear_usuarios"]
pantallas: ["frontend/devel_db_admin/controller/db_crear_usuarios.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearUsuarios"]
tags: ["crear", "crear_usuarios", "devel_db_admin", "usuarios"]
estado_revision: "generado"
---

# Gestionar Crear Usuarios

Propuesta generada automaticamente a partir de endpoints con prefijo comun `crear_usuarios`.

## Objetivo Funcional

Gestiona CrearUsuarios. Ejecuta {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/crear_usuarios`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_crear_usuarios.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\CrearUsuarios`

## Pistas Desde Endpoints

- Ejecuta {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
