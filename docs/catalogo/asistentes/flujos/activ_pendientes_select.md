---
id: "asistentes.activ_pendientes_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Consultar quién tiene pendiente el ca o el crt"
capacidad: "asistentes.activ_pendientes_select.gestionar"
pantallas_principales: ["asistentes.pantalla.activ_pendientes_select"]
fragmentos: ["asistentes.pantalla.activ_pendientes_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/activ_pendientes_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Activ Pendientes Select

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Saber quién tiene pendiente hacer el ca o el crt.

## Punto De Entrada

Menú **vest > ca n > pendientes** (también vsm > crt > pendientes, dagd > cv > pendientes, dre > personas > ca pendientes / crt pendientes).

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.activ_pendientes_select`

## Escenarios Inferidos

### Consultar quién tiene pendiente el ca o el crt

Pasos propuestos:
1. Entrar en la opción **pendientes** del colectivo que interese (numerarios, agregados o sacd).
2. Se muestra «Personas» con la lista de quienes no han hecho todavía la actividad en el curso, o «no hay ninguna fila» si no queda nadie.
3. Pulsar el nombre de una persona abre su ficha.

Endpoints asociados:
- `/src/asistentes/activ_pendientes_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.any`
- `html.ok`
- `html.sactividad`
- `html.tipo_personas`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/asistentes/activ_pendientes_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vsm/vest/dagd/dre > pendientes según `sactividad` y `tipo_personas`
- **Pills2:** ACTIVIDADES > Listados > Listado de personas sin ca/crt
