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
estado_revision: "revisado"
---

# Flujo - Gestionar Mover Tabla

Propuesta generada automaticamente desde la capacidad `devel_db_admin.mover_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Mover tabla de sv a sv-e en todos los esquemas.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_mover`

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
- `post.tabla`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/mover_tabla`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sistema > DB > mover tabla a otra DB
- **Pills2:** sistema > DB > mover tabla a otra DB
