---
id: "devel_db_admin.mover_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Mover Tabla"
capacidad: "devel_db_admin.mover_tabla.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_mover"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/mover_tabla"]
estado_revision: "generado"
---

# Flujo - Gestionar Mover Tabla

Propuesta generada automaticamente desde la capacidad `devel_db_admin.mover_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MoverTabla. Lista esquemas con la tabla y ejecuta {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_mover`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.tabla`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/mover_tabla`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
