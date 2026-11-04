---
id: "notas.acta_listado_anual.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Listar las actas de un periodo"
capacidad: "notas.acta_listado_anual.gestionar"
pantallas_principales: ["notas.pantalla.acta_listado_anual"]
fragmentos: ["notas.pantalla.acta_listado_anual"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_listado_anual_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Listado Anual

Propuesta generada automaticamente desde la capacidad `notas.acta_listado_anual.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver las actas de un periodo de fechas, por año o trimestre.

## Punto De Entrada

Menú **vest > actas... > listado actas** (Pills2: ESTUDIOS > Actas y certificados > Listado de actas).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_listado_anual`

## Escenarios Inferidos

### Listar las actas de un periodo

Pasos propuestos:
1. Entrar en **listado actas**.
2. En **Periodo de selección** elegir el trimestre (o todo el año) y el año, o escribir dos fechas en **entre … y …**.
3. Se muestra la lista de actas del periodo.

Endpoints asociados:
- `/src/notas/acta_listado_anual_data`

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
