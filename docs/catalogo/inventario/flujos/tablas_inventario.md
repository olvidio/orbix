---
id: "inventario.tablas_inventario.mantener.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar y mantener las tablas del inventario"
capacidad: "inventario.tablas_inventario.mantener"
pantallas_principales: []
fragmentos: []
acciones: ["buscar"]
endpoints: []
estado_revision: "revisado"
---

# Flujo - Consultar y mantener las tablas del inventario

## Objetivo De Usuario

Buscar, consultar y corregir los datos de base del inventario: las colecciones, los tipos de documento, qué documentos tiene cada centro, los documentos por sigla, los centros con inventario y los lugares dentro de cada centro.

## Punto De Entrada

Menú **Inventario**, entradas **colecciones**, **documentos > tipos de documentos**, **documentos > por centros**, **documentos > por sigla**, **centros** y **lugares**. Todas abren el gestor genérico de tablas, con un formulario de búsqueda distinto según la tabla.

## Fragmentos O Pantallas Auxiliares

La modificación de varios documentos a la vez se describe en el flujo `inventario.documentos.gestionar.flujo`.

## Escenarios Inferidos

### Buscar en una tabla del inventario

Pasos propuestos:
1. Abrir la entrada de menú que interesa (por ejemplo **colecciones**).
2. Rellenar el formulario de búsqueda: en **colecciones** se busca en «nombre colección», en **tipos de documentos** en «detalle», en **por sigla** se elige el **tipo de documento**, en **por centros** se elige el **centro** (con la opción **realizar una búsqueda exacta** no/sí). En **centros** y **lugares** basta con pulsar el botón de buscar.
3. Pulsar **buscar** (**Buscar** en «por centros» y «por sigla»).
4. Se muestra el listado de la tabla; desde él se abre cada fila para consultarla o modificarla.

Endpoints asociados:
- Ninguno directo (gestor genérico de tablas).

## Errores Conocidos

No se han documentado errores propios.

## Ruta de menú

- **Legacy:** scdl > Inventario > colecciones
- **Legacy:** scdl > Inventario > documentos > tipos de documentos / por centros / por sigla
- **Legacy:** scdl > Inventario > centros
- **Legacy:** scdl > Inventario > lugares
- **Pills2:** sin entrada en el índice de menús.
