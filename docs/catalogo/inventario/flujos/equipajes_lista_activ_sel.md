---
id: "inventario.equipajes_lista_activ_sel.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Nombrar el equipaje"
capacidad: "inventario.equipajes_lista_activ_sel.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_form_nuevo"]
fragmentos: ["inventario.pantalla.equipajes_form_nuevo"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Lista Activ Sel

## Objetivo De Usuario

Ponerle nombre al equipaje formado con las actividades marcadas.

## Punto De Entrada

Se abre en una ventana al pulsar **seleccionar** en la lista de actividades de **nuevo equipaje**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_nuevo`

## Escenarios Inferidos

### Nombrar el equipaje

Pasos propuestos:
1. Marcar las actividades y pulsar **seleccionar**.
2. En la ventana aparece el **nombre del equipaje** ya propuesto como «casa (fechas)»; se puede cambiar.
3. Pulsar **guardar** (o **cancel** para cerrar sin crear nada).

Endpoints asociados:
- `/src/inventario/equipajes_lista_activ_sel`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.nom_equipaje`
- `html.nom_equipaje`
- `post.id_cdc`
- `post.nom_equip`
- `post.sel`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/inventario/equipajes_lista_activ_sel`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
