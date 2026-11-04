---
id: "ubiscamas.cama.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubiscamas"
nombre: "Flujo - Gestionar camas"
capacidad: "ubiscamas.cama.gestionar"
pantallas_principales: ["ubiscamas.pantalla.cama_form"]
fragmentos: ["ubiscamas.pantalla.cama_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/cama_delete", "/src/ubiscamas/cama_form_data", "/src/ubiscamas/cama_update"]
estado_revision: "revisado"
---

# Flujo - Cama

## Objetivo De Usuario

Crear, editar o eliminar camas individuales de una habitación.

## Punto De Entrada

Formulario de la habitación > Camas.

## Fragmentos O Pantallas Auxiliares

- `ubiscamas.pantalla.cama_form`

## Escenarios Inferidos

### Gestionar camas

Pasos propuestos:
1. Guardar antes la habitación y pulsar **Añadir cama**.
2. Rellenar **Descripción** y marcar **VIP** o **Larga**; pulsar **Guardar**.
3. Para cambiar o quitar una cama, usar **Editar** o **Eliminar** en su fila.

Endpoints asociados:
- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.descripcion`
- `html.larga`
- `html.vip`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Errores Conocidos

- `ID de cama no proporcionado`
- `No se encontró la cama a eliminar`
- `hay un error, no se ha eliminado la cama`
- `Error al eliminar la cama`
- `Habitación no válida`
- `Cama no válida`
- `Error al guardar la cama`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
