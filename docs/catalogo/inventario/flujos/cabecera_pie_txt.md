---
id: "inventario.cabecera_pie_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Editar los textos tipo"
capacidad: "inventario.cabecera_pie_txt.gestionar"
pantallas_principales: ["inventario.pantalla.cabecera_pie_txt"]
fragmentos: ["inventario.pantalla.cabecera_pie_txt", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar", "guardar"]
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/cabecera_pie_txt_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Cabecera Pie Txt

## Objetivo De Usuario

Fijar los textos tipo que llevan por defecto los equipajes impresos: cabecera, cabecera para el material que envía la dl y pie de firma.

## Punto De Entrada

Menú **Inventario > equipajes > tipos de texto** (pantalla **Textos tipo para las cabeceras y pies de los equipajes**).

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.cabecera_pie_txt`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Editar los textos tipo

Pasos propuestos:
1. Abrir **tipos de texto**.
2. Modificar la **cabecera tipo para equipajes** (instrucciones para comprobar el inventario de la casa al llegar y al marchar).
3. Modificar la **cabecera para equipajes tipo B) (que envía la dl)** (instrucciones para el material que lleva la delegación y se devuelve al final).
4. Modificar el **pie tipo para equipajes** (lista de lo que se deja en la casa) y el texto **para la firma en equipajes** (observaciones, fecha y firma).
5. Pulsar **guardar**; aparece «textos guardados correctamente».

Endpoints asociados:
- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/cabecera_pie_txt_guardar`

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
