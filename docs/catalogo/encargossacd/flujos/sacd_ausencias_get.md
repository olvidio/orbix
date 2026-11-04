---
id: "encargossacd.sacd_ausencias_get.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Anotar una ausencia"
capacidad: "encargossacd.sacd_ausencias_get.gestionar"
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias_get"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ausencias Get

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias_get.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver y anotar las ausencias o actividades personales de un sacerdote.

## Punto De Entrada

Menú **dre > ausencias > sacd**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_get`

## Escenarios Inferidos

### Anotar una ausencia

Pasos propuestos:
1. Entrar en **ausencias > sacd**.
2. Elegir el **tipo** y el **sacd**.
3. En **ausencias/tareas** (columnas **fechas** y **horario**), elegir en **añadir actividades personales** el tipo (Clases del stgr, Ausencia, cv de un dia, 1ª Confesiones, boda, confirmación, 1ª comunión, bautizo, Tarea, rtm): aparece una fila con **inicio** y **fin** (al poner el inicio, el fin se rellena con la misma fecha). Con **ver anteriores** se ve el historial.
4. Pulsar **Guardar**; la pantalla vuelve atrás sola. Para borrar una, dejar en blanco la fecha de inicio y la de fin.
5. **añadir actividades personales** y **crear horario** permiten añadir otras tareas.

Endpoints asociados:
- `/src/encargossacd/sacd_ausencias_get_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.fin`
- `form.id_enc`
- `form.id_item`
- `form.inicio`
- `html.ok`
- `post.filtro_sacd`
- `post.historial`
- `post.id_nom`

Acciones JavaScript:
- `fnjs_date_fin`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_mas_enc`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_get_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

