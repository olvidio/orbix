---
id: "devel_db_admin.crear_usuarios.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Crear Usuarios"
capacidad: "devel_db_admin.crear_usuarios.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_crear_usuarios"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/crear_usuarios"]
estado_revision: "revisado"
---

# Flujo - Gestionar Crear Usuarios

Propuesta generada automaticamente desde la capacidad `devel_db_admin.crear_usuarios.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Crear roles PostgreSQL para nuevo esquema (paso 1).


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_crear_usuarios`

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
- `post.dl`
- `post.region`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/crear_usuarios`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
