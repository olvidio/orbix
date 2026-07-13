---
id: "devel_db_admin.verificar_renombrar_esquema.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Verificar Renombrar Esquema"
capacidad: "devel_db_admin.verificar_renombrar_esquema.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_verificar_renombrar_esquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/verificar_renombrar_esquema"]
estado_revision: "revisado"
---

# Flujo - Gestionar Verificar Renombrar Esquema

Propuesta generada automaticamente desde la capacidad `devel_db_admin.verificar_renombrar_esquema.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Comprobar estado del renombre antes/después.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_verificar_renombrar_esquema`

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
- `post.esquema_origen`
- `post.region`
- `post.sf`
- `post.sv`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/verificar_renombrar_esquema`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
