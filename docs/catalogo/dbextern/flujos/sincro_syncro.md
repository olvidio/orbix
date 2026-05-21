---
id: "dbextern.sincro_syncro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Syncro"
capacidad: "dbextern.sincro_syncro.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_syncro"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Syncro

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_syncro.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SincroPersonas. Sincroniza todas las personas unidas de una DL.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.sincro_index`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_listas`
- `form.que`
- `form.region`
- `form.tipo_persona`
- `post.tipo`

Acciones JavaScript:
- `fnjs_refrescar`
- `fnjs_sincronizar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/dbextern/sincro_syncro`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
