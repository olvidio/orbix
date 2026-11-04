---
id: "actividades.actividad_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver y trabajar con la lista de actividades encontradas"
capacidad: "actividades.actividad_select.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select"]
fragmentos: ["actividades.pantalla.actividad_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_select_datos"]
estado_revision: "revisado"
---

# Flujo - Listar resultados buscar actividad

Consulta de actividades que cumplen filtros y construcción de la tabla de resultados.

## Objetivo De Usuario

Ver las actividades que cumplen la búsqueda y trabajar con las que se marquen: abrir su ficha, duplicarlas, borrarlas, cambiar su tipo o ir a sus asistentes, cargos, plazas o proceso.

## Punto De Entrada

Menú **ACTIVIDADES > Buscar actividad** (o la entrada del colectivo, por ejemplo vsm > ca > buscar ca); la lista aparece al pulsar **buscar**.

## Escenarios Inferidos

### Ver y trabajar con la lista de actividades encontradas

Pasos propuestos:
1. Tras **buscar** aparece el título «N actividades encontradas (M sin permiso) entre fecha y fecha» y una tabla con inicio, fin, actividad, horas, sf/sv, tarifa, Sacd, dl org, centro, observaciones e idioma.
2. Si hay muchas filas, antes de la tabla aparece un aviso y hay que confirmar que se quiere continuar.
3. Marcar la casilla de la actividad (o de varias, según el botón) y pulsar una de las acciones: **datos**, **duplicar**, **borrar**, **cambiar tipo**, **cargos**, **lista cl**, **asistentes**, **otras peticiones**, **lista**, **plazas**, **proceso**, **asignaturas**, **plan estudios**, **listas de clase** o **posibles asignaturas**.
4. Pulsar el nombre de una actividad abre directamente su ficha.
5. Debajo de la tabla, **nueva actividad del mismo tipo** y **nueva actividad sv n** (el tipo que se buscó) crean una actividad nueva.
6. **realizar otra búsqueda** (arriba) vuelve al formulario de búsqueda.

Endpoints asociados:
- `/src/actividades/actividad_select_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_select_datos`

## Ruta de menú

Destino de `actividad_que` — ver rutas de buscar actividad en pantalla `actividad_select`.
