---
id: "casas.prevision_asistentes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar la previsión de asistentes"
capacidad: "casas.prevision_asistentes.gestionar"
pantallas_principales: ["casas.pantalla.prevision_asistentes"]
fragmentos: ["casas.pantalla.prevision_asistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/prevision_asistentes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Prevision Asistentes

Propuesta generada automaticamente desde la capacidad `casas.prevision_asistentes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver la previsión de asistentes de las casas.

## Punto De Entrada

Menú **adl > Nuevo Calendario > previsión asistentes** (Pills2: ACTIVIDADES > Estadísticas económicas > Previsión asistentes).

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.prevision_asistentes`

## Escenarios Inferidos

### Consultar la previsión de asistentes

Pasos propuestos:
1. Entrar en **previsión asistentes**.
2. Elegir el periodo y las casas.
3. Se muestra la tabla con los asistentes previstos.

Endpoints asociados:
- `/src/casas/prevision_asistentes_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.mi_of`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mi_of`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/casas/prevision_asistentes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > previsión asistentes
- **Pills2:** Calendario > Nuevo calendario > previsión asistentes

