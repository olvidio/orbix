---
id: "inventario.equipajes_lista_activ_equipaje.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar las actividades de un equipaje"
capacidad: "inventario.equipajes_lista_activ_equipaje.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_imprimir"]
fragmentos: ["inventario.pantalla.equipajes_imprimir"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_equipaje"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Lista Activ Equipaje

## Objetivo De Usuario

Mostrar, en el equipaje impreso, las actividades que lo forman.

## Punto De Entrada

Dentro de **imprimir equipajes**: aparece bajo el título del inventario al elegir el equipaje.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_imprimir`

## Escenarios Inferidos

### Ver las actividades del equipaje impreso

Pasos propuestos:
1. Abrir **imprimir equipajes** y elegir el equipaje.
2. Bajo el título «Inventario de publicaciones internas» aparecen las actividades del equipaje con sus fechas.
3. No hay nada que rellenar: se genera solo.

Endpoints asociados:
- `/src/inventario/equipajes_lista_activ_equipaje`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_equipaje`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
