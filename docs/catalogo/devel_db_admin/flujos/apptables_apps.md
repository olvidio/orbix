---
id: "devel_db_admin.apptables_apps.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Apptables Apps"
capacidad: "devel_db_admin.apptables_apps.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.apptables"]
acciones: ["obtener_datos"]
endpoints: ["/src/devel_db_admin/apptables_apps_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Apptables Apps

Propuesta generada automaticamente desde la capacidad `devel_db_admin.apptables_apps.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ApptablesApps. JSON con el mapa id_app → nombre para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.apptables`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.esquema`
- `form.id_app`
- `html.bce`
- `html.bcg`
- `html.bee`
- `html.beg`

Acciones JavaScript:
- `fnjs_db`
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/devel_db_admin/apptables_apps_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
