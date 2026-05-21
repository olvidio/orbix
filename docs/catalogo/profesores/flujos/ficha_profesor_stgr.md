---
id: "profesores.ficha_profesor_stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Gestionar Ficha Profesor Stgr"
capacidad: "profesores.ficha_profesor_stgr.gestionar"
pantallas_principales: []
fragmentos: ["profesores.pantalla.ficha_profesor_stgr"]
acciones: ["ejecutar"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
estado_revision: "generado"
---

# Flujo - Gestionar Ficha Profesor Stgr

Propuesta generada automaticamente desde la capacidad `profesores.ficha_profesor_stgr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona FichaProfesorStgr. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.ficha_profesor_stgr`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.depende`
- `post.id_nom`
- `post.id_pau`
- `post.id_tabla`
- `post.obj_pau`
- `post.permiso`
- `post.print`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/profesores/ficha_profesor_stgr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
