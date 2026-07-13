---
id: "notas.acta_listado_anual.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Listado Anual"
capacidad: "notas.acta_listado_anual.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_listado_anual"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_listado_anual_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Listado Anual

Propuesta generada automaticamente desde la capacidad `notas.acta_listado_anual.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Consultar actas por rango de fechas en vista anual.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_listado_anual`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/acta_listado_anual_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > listado actas
- **Pills2:** ESTUDIOS > Actas y certificados > Listado de actas; vest > actas... > listado actas
