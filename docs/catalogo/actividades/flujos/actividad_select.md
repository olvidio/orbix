---
id: "actividades.actividad_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Listar resultados buscar actividad"
capacidad: "actividades.actividad_select.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_select_datos"]
estado_revision: "revisado"
---

# Flujo - Listar resultados buscar actividad

Consulta de actividades que cumplen filtros y construcción de la tabla de resultados.

## Objetivo De Usuario

Ver listado tras buscar, con enlaces a ficha, importar, publicar o seleccionar según modo.

## Punto De Entrada

`actividad_select.php` tras POST desde `actividad_que` o refinamiento de filtros.

## Escenarios

### Obtener Datos

1. Controller reenvía filtros (`id_tipo_activ`, periodo, fases, `modo`, etc.).
2. Si >200 filas y sin `continuar`, muestra aviso; si no, llama `actividad_select_datos`.
3. Monta tabla `Lista` con `link_spec` firmados.

## Endpoints Del Flujo

- `/src/actividades/actividad_select_datos`

## Ruta de menú

Destino de `actividad_que` — ver rutas de buscar actividad en pantalla `actividad_select`.
