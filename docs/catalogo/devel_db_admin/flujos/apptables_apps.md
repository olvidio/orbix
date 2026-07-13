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
estado_revision: "revisado"
---

# Flujo - Gestionar Apptables Apps

Propuesta generada automaticamente desde la capacidad `devel_db_admin.apptables_apps.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Cargar apps y ejecutar operaciones de tablas globales/esquema.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.apptables`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.


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

## Ruta de menú

- **Legacy:** sistema > Configuración > Tablas de apps
- **Pills2:** sistema > Configuración > Tablas de apps
