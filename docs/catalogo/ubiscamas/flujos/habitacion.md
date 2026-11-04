---
id: "ubiscamas.habitacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Ver las habitaciones de una casa"
capacidad: "ubiscamas.habitacion.gestionar"
pantallas_principales: ["ubiscamas.pantalla.habitacion_form"]
fragmentos: ["ubiscamas.pantalla.habitacion_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/habitacion_delete", "/src/ubiscamas/habitacion_form_data", "/src/ubiscamas/habitacion_update"]
estado_revision: "revisado"
---

# Flujo - Habitacion

## Objetivo De Usuario

Dar de alta, modificar o eliminar las habitaciones de una casa, y crear sus camas.

## Punto De Entrada

Ficha de la casa (Direcciones > buscar, tipo casa) > dossier **Habitaciones (camas) de una cdc**. Solo está disponible si el módulo de camas está instalado en la delegación.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.habitacion_form`

## Escenarios Inferidos

### Ver las habitaciones de una casa

Pasos propuestos:
1. Buscar la casa en **direcciones > buscar** y abrir su ficha.
2. En «Relación de dossiers» pulsar **Habitaciones (camas) de una cdc**.
3. Aparece la **Relación de habitaciones** de la casa (o «no hay ninguna fila» si aún no hay).

Endpoints asociados:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

### Crear una habitación

Pasos propuestos:
1. En la relación de habitaciones, pulsar **crear una nueva habitación**.
2. Se abre «Nueva habitación» con los campos **Orden**, **Nombre**, **Número de camas**, **Número de camas VIP**, **Planta**, **Tipo de lavabo** (sin especificar, NO, completo, sin ducha o exterior), **Sillón**, **Adaptada**, **Observaciones** y **Despacho**.
3. Debajo, en **Camas**, el botón **Añadir cama** permite añadir camas una a una.
4. Pulsar **Guardar *** para guardar: el asterisco indica que se añaden automáticamente las camas según el **Número de camas** puesto arriba. **Cancelar** sale sin guardar.

Endpoints asociados:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

### Modificar o eliminar una habitación

Pasos propuestos:
1. Desde la relación de habitaciones, seleccionar la habitación para modificarla o eliminarla (no probado en pantalla: la casa de prueba no tenía habitaciones).

Endpoints asociados:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.adaptada`
- `html.despacho`
- `html.new_camas_desc[${rowIdx}]`
- `html.new_camas_larga[${rowIdx}]`
- `html.new_camas_vip[${rowIdx}]`
- `html.nombre`
- `html.numero_camas`
- `html.numero_camas_vip`
- `html.observaciones`
- `html.orden`
- `html.planta`
- `html.refresh`
- `html.sillon`
- `html.tipoLavabo`
- `post.refresh`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_anadir_cama_dinamica`
- `fnjs_cancelar`
- `fnjs_editar_cama`
- `fnjs_eliminar_cama`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nueva_cama`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Errores Conocidos

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
