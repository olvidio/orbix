---
id: "devel_db_admin.absorber_esquema.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Absorber Esquema"
capacidad: "devel_db_admin.absorber_esquema.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_absorber_esquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/absorber_esquema"]
estado_revision: "generado"
---

# Flujo - Gestionar Absorber Esquema

Propuesta generada automaticamente desde la capacidad `devel_db_admin.absorber_esquema.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AbsorberEsquema. JSON { "lines": string[] } para la absorción de esquema (POST esquema_matriz, esquema_del).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_absorber_esquema`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.esquema_del`
- `post.esquema_matriz`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/absorber_esquema`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
