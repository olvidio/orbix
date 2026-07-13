---
id: "devel_db_admin.migraciones_quitar_registro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Migraciones Quitar Registro"
capacidad: "devel_db_admin.migraciones_quitar_registro.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.migraciones_lista"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/migraciones_quitar_registro"]
estado_revision: "revisado"
---

# Flujo - Gestionar Migraciones Quitar Registro

Propuesta generada automaticamente desde la capacidad `devel_db_admin.migraciones_quitar_registro.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Quitar registro de migración aplicada para poder re-ejecutar.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.migraciones_lista`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.


Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`

Acciones JavaScript:
- `fnjs_migraciones_checked`
- `fnjs_migraciones_ejecutar_hasta`
- `fnjs_migraciones_ejecutar_seleccion`
- `fnjs_migraciones_enviar`
- `fnjs_migraciones_quitar_registro`

## Endpoints Del Flujo

- `/src/devel_db_admin/migraciones_quitar_registro`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
