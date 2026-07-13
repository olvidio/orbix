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
estado_revision: "revisado"
---

# Flujo - Gestionar Absorber Esquema

Propuesta generada automaticamente desde la capacidad `devel_db_admin.absorber_esquema.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Unir un esquema DL disuelto en otro esquema matriz.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_absorber_esquema`

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
- `post.esquema_del`
- `post.esquema_matriz`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/devel_db_admin/absorber_esquema`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sistema > DB > DB unir esquemas
