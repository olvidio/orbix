---
id: "asistentes.tabla_peticiones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Ver las otras peticiones de los asistentes"
capacidad: "asistentes.tabla_peticiones.gestionar"
pantallas_principales: ["asistentes.pantalla.tabla_peticiones"]
fragmentos: ["asistentes.pantalla.tabla_peticiones"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tabla Peticiones

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver las peticiones de plaza de las personas que quieren asistir a la actividad.

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **otras peticiones**.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.tabla_peticiones`

## Escenarios Inferidos

### Ver las otras peticiones de los asistentes

Pasos propuestos:
1. Marcar la actividad y pulsar **otras peticiones**.
2. Se muestra el título de la actividad y una tabla «nombre / peticiones (libres/concedidas)» con cada persona que ha pedido plaza y sus otras peticiones.
3. Si no hay peticiones, la tabla sale vacía.

Endpoints asociados:
- `/src/asistentes/tabla_peticiones_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_activ_old`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/tabla_peticiones_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
