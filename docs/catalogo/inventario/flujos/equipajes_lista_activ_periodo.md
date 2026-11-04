---
id: "inventario.equipajes_lista_activ_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir las actividades del equipaje"
capacidad: "inventario.equipajes_lista_activ_periodo.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_lista_activ_periodo"]
fragmentos: ["inventario.pantalla.equipajes_lista_activ_periodo"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Lista Activ Periodo

## Objetivo De Usuario

Ver las actividades de una casa en el periodo elegido, para marcar las que irán en el mismo equipaje.

## Punto De Entrada

Dentro de **nuevo equipaje**, tras elegir la casa.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_lista_activ_periodo`

## Escenarios Inferidos

### Marcar las actividades del equipaje

Pasos propuestos:
1. En **nuevo equipaje**, elegir periodo y casa.
2. Leer la lista: **empieza**, **termina**, **actividad** y **observaciones**.
3. Marcar las actividades que viajan juntas.
4. Pulsar **seleccionar** para ponerle nombre al equipaje.

Endpoints asociados:
- `/src/inventario/equipajes_lista_activ_periodo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc`
- `post.inicio`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_nombrar_equipaje`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_periodo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
