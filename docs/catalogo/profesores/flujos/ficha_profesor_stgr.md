---
id: "profesores.ficha_profesor_stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Ver ficha profesor STGR"
capacidad: "profesores.ficha_profesor_stgr.gestionar"
pantallas_principales: []
fragmentos: ["profesores.pantalla.ficha_profesor_stgr"]
acciones: ["consultar", "imprimir", "modificar_bloque"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
estado_revision: "revisado"
---

# Flujo - Ver ficha profesor STGR

Dossier académico del profesor desde la búsqueda de personas.

## Objetivo De Usuario

Consultar (e imprimir o modificar con permiso) la ficha STGR de un profesor: nombramientos,
curriculum, docencia, congresos, etc.

## Punto De Entrada

Botón **ficha profesor stgr** en `personas_select` (`fnjs_ficha_profe`), que abre
`ficha_profesor_stgr.php` con `sel=id_nom#id_tabla`.

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.ficha_profesor_stgr`

## Escenarios Inferidos

### Consultar

Pasos:
1. Buscar persona y abrir **ficha profesor stgr**.
2. Revisar bloques visibles según `aPerm`.

Endpoints asociados:
- `/src/profesores/ficha_profesor_stgr`

### Imprimir

Pasos:
1. Pulsar **[imprimir]** → recarga con `print=1` (forzado en RSTGR).

### Modificar bloque

Pasos:
1. Con permiso de escritura, pulsar **[modificar]** en un bloque → `tablaDB_lista_ver`.

## Errores Conocidos

- `No encuentro a nadie con id_nom: %s`

## Ruta de menú

sin entrada de menú en el índice (desde búsqueda de personas / `personas_select`)
