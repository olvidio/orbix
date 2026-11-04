---
id: "casas.ingreso_plazas_previstas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Corregir los asistentes previstos"
capacidad: "casas.ingreso_plazas_previstas.gestionar"
pantallas_principales: ["casas.pantalla.prevision_asistentes"]
fragmentos: ["casas.pantalla.prevision_asistentes"]
acciones: ["crear_actualizar"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ingreso Plazas Previstas

Propuesta generada automaticamente desde la capacidad `casas.ingreso_plazas_previstas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Cambiar el número de asistentes previstos de una actividad en la previsión.

## Punto De Entrada

Menú **adl > Nuevo Calendario > previsión asistentes**.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.prevision_asistentes`

## Escenarios Inferidos

### Corregir los asistentes previstos

Pasos propuestos:
1. Abrir **previsión asistentes**.
2. Hacer doble clic en la celda del número de asistentes previstos, escribir el valor y confirmar.
3. Se guarda al salir de la celda.

Endpoints asociados:
- `/src/casas/ingreso_plazas_previstas_update`

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

- `/src/casas/ingreso_plazas_previstas_update`

## Errores Conocidos

- ``Hay un error, no se ha guardado``
- ``no se encuentra el ingreso``
## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > previsión asistentes
- **Pills2:** Calendario > Nuevo calendario > previsión asistentes

