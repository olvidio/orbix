---
id: "asistentes.lista_est_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Lista Est Ctr"
capacidad: "asistentes.lista_est_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.lista_est_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Est Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver estudios matriculados por centro.


## Punto De Entrada

Pantalla `lista_est_ctr` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_est_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.n_agd`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_est_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest/dagd > estudios x ctr
- **Pills2:** ACTIVIDADES > Listados > Mejores ca para n/agd
