---
id: "inventario.equipajes_doc_casa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los documentos de la casa y sus valijas"
capacidad: "inventario.equipajes_doc_casa.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_doc_casa"]
fragmentos: ["inventario.pantalla.equipajes_doc_casa", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_doc_casa"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Doc Casa

## Objetivo De Usuario

Ver los documentos que pertenecen a la casa (se quedan siempre allí) y las valijas del equipaje.

## Punto De Entrada

Menú **Inventario > equipajes > hacer equipajes**: aparece al elegir un equipaje en el desplegable **escoger un equipaje**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Consultar la documentación de la casa y sus valijas

Pasos propuestos:
1. Abrir **hacer equipajes**; elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje** (aparece como «casa (fechas)»).
3. En **documentos en** (nombre de la casa) se listan **Nombre**, **Identificador** y **Lugar** de la documentación que está en la casa.
4. En **maletas para** (nombre de la casa) aparecen las valijas del equipaje; **nueva valija** añade otra.

Endpoints asociados:
- `/src/inventario/equipajes_doc_casa`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_eliminar_grupo`
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_doc_casa`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
