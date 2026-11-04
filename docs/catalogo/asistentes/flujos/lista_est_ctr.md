---
id: "asistentes.lista_est_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Ver los estudios matriculados por centro"
capacidad: "asistentes.lista_est_ctr.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_est_ctr"]
fragmentos: ["asistentes.pantalla.lista_est_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Est Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver, centro por centro, los estudios en los que están matriculadas sus personas.

## Punto De Entrada

Menú **vest > ca n > estudios x ctr** (o cv agd).

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_est_ctr`

## Escenarios Inferidos

### Ver los estudios matriculados por centro

Pasos propuestos:
1. Elegir los centros y el curso y pulsar **Buscar**.
2. Debería aparecer, por centro, la lista de personas con sus estudios matriculados (pantalla no comprobada en la instancia).

Endpoints asociados:
- `/src/asistentes/lista_est_ctr_data`

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
