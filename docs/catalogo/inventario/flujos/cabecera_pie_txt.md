---
id: "inventario.cabecera_pie_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Cabecera Pie Txt"
capacidad: "inventario.cabecera_pie_txt.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.cabecera_pie_txt", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar", "guardar"]
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/cabecera_pie_txt_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Cabecera Pie Txt

Propuesta generada automaticamente desde la capacidad `inventario.cabecera_pie_txt.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Editar textos globales de cabecera/pie para impresión de equipajes.

## Punto De Entrada

- `inventario.pantalla.cabecera_pie_txt`



## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.cabecera_pie_txt`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cabecera`
- `form.cabeceraB`
- `form.firma`
- `form.pie`
- `html.cabecera`
- `html.cabeceraB`
- `html.firma`
- `html.pie`
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Endpoints Del Flujo

- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/cabecera_pie_txt_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > tipos de texto
- **Pills2:** —
