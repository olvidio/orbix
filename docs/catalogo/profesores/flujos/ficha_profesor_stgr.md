---
id: "profesores.ficha_profesor_stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Consultar la ficha de un profesor"
capacidad: "profesores.ficha_profesor_stgr.gestionar"
pantallas_principales: ["profesores.pantalla.ficha_profesor_stgr"]
fragmentos: ["profesores.pantalla.ficha_profesor_stgr"]
acciones: ["consultar", "imprimir", "modificar_bloque"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
estado_revision: "revisado"
---

# Flujo - Ver ficha profesor STGR

Dossier académico del profesor desde la búsqueda de personas.

## Objetivo De Usuario

Consultar la ficha stgr de un profesor (nombramientos, curriculum, docencia, congresos…) y, con permiso, imprimirla o modificarla.

## Punto De Entrada

Botón **ficha profesor stgr** de la lista de personas.

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.ficha_profesor_stgr`

## Escenarios Inferidos

### Consultar la ficha de un profesor

Pasos propuestos:
1. Buscar a la persona y pulsar **ficha profesor stgr**.
2. Se muestran los bloques de la ficha que el usuario tiene permiso para ver.

Endpoints asociados:
- `/src/profesores/ficha_profesor_stgr`

### Imprimir la ficha

Pasos propuestos:
1. Pulsar **[imprimir]**: la ficha se vuelve a mostrar en formato de impresión (en las regiones del stgr siempre sale así).

Endpoints asociados:
- `/src/profesores/ficha_profesor_stgr`

### Modificar un bloque de la ficha

Pasos propuestos:
1. Con permiso de escritura, pulsar **[modificar]** en el bloque que se quiere cambiar.
2. Se abre la tabla de ese bloque para editarla.

Endpoints asociados:
- `/src/profesores/ficha_profesor_stgr`

## Errores Conocidos

- `No encuentro a nadie con id_nom: %s`

## Ruta de menú

sin entrada de menú en el índice (desde búsqueda de personas / `personas_select`)
