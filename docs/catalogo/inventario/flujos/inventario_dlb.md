---
id: "inventario.inventario_dlb.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Imprimir el inventario de la dlb/casa"
capacidad: "inventario.inventario_dlb.gestionar"
pantallas_principales: ["inventario.pantalla.doc_imprimir_dlb"]
fragmentos: ["inventario.pantalla.doc_imprimir_dlb"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/inventario_dlb"]
estado_revision: "revisado"
---

# Flujo - Gestionar Inventario Dlb

## Objetivo De Usuario

Obtener el inventario impreso de los documentos de uno o varios lugares de la dlb/casa, listo para firmar.

## Punto De Entrada

Desde **de centros o dlb > inventario de dlb/casa**, tras marcar los lugares y pulsar **Imprimir** o **Imprimir dl**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_dlb`

## Escenarios Inferidos

### Imprimir el inventario de la dlb/casa

Pasos propuestos:
1. Marcar los lugares en **inventario de dlb/casa**.
2. Pulsar **Imprimir** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por lugar titulada **Inventario de documentos de** (centro y lugar), con **documento**, **id** y **observaciones**.
4. El botón **seleccionar** y la primera columna (sin cabecera) de la hoja sirven solo para imprimir y marcar a mano en papel.
5. Imprimir desde el navegador. Cada hoja termina con «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

Endpoints asociados:
- `/src/inventario/inventario_dlb`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.sel`

Acciones JavaScript:
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/inventario_dlb`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —
