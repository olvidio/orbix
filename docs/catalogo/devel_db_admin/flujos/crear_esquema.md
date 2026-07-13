---
id: "devel_db_admin.crear_esquema.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Crear Esquema"
capacidad: "devel_db_admin.crear_esquema.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_crear_esquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/crear_esquema"]
estado_revision: "revisado"
---

# Flujo - Gestionar Crear Esquema

Propuesta generada automaticamente desde la capacidad `devel_db_admin.crear_esquema.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Crear estructura PostgreSQL de un nuevo esquema DL (paso 2).


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_crear_esquema`

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
- `post.comun`
- `post.dl`
- `post.esquema`
- `post.region`
- `post.sf`
- `post.sv`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/crear_esquema`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
