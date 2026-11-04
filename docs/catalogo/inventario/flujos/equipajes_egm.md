---
id: "inventario.equipajes_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Imprimir un equipaje"
capacidad: "inventario.equipajes_egm.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_imprimir"]
fragmentos: ["inventario.pantalla.equipajes_doc_casa", "inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_egm"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Egm

## Objetivo De Usuario

Preparar e imprimir el equipaje de una casa: el inventario de lo que hay en la casa más el material que lleva la delegación, con espacio para observaciones y firma.

## Punto De Entrada

Menú **Inventario > equipajes > imprimir equipajes**. También se ven las valijas (sin imprimir) en **hacer equipajes**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Imprimir un equipaje

Pasos propuestos:
1. Abrir **imprimir equipajes** y elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje**.
3. Se muestra la hoja «(casa): Inventario de publicaciones internas» con las actividades del equipaje y la cabecera.
4. Revisar el apartado **A) Documentación que pertenece a la casa y se queda siempre allí** (con los ejemplares y numeración de cada documento).
5. Si el equipaje tiene valijas, revisar el apartado **B) Documentación que envía la Delegación**, con cada valija y sus documentos.
6. Comprobar el pie y el bloque final de **Observacions** y **data i firma**.
7. Imprimir desde el navegador. Los lápices de **modificar texto** no salen en el papel.

Endpoints asociados:
- `/src/inventario/equipajes_egm`

### Ver las valijas de un equipaje

Pasos propuestos:
1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (nombre de la casa) se listan las valijas con sus documentos.

Endpoints asociados:
- `/src/inventario/equipajes_egm`

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

- `/src/inventario/equipajes_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
