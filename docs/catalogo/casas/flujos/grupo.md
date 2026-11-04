---
id: "casas.grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar los grupos de casas"
capacidad: "casas.grupo.gestionar"
pantallas_principales: ["casas.pantalla.grupo"]
fragmentos: ["casas.pantalla.grupo_form", "casas.pantalla.grupo_lista"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_form_data", "/src/casas/grupo_lista_data", "/src/casas/grupo_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Grupo

Propuesta generada automaticamente desde la capacidad `casas.grupo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Agrupar casas: indicar qué casa es la madre y cuáles dependen de ella.

## Punto De Entrada

Menú **adl > Gestión casas > grupos** (Pills2: CASAS Y CTR > Gestión casas > grupos).

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

## Escenarios Inferidos

### Gestionar los grupos de casas

Pasos propuestos:
1. Entrar en **grupos**: aparece la lista de **grupos de casas**.
2. Para crear uno, pulsar nuevo; elegir la **Casa Madre** y la **Casa dependiente** (deben ser dos casas distintas) y pulsar **guardar**.
3. Para cambiarlo, abrir la relación, modificar y **guardar**; para quitarlo, eliminarla.
4. Aviso: en la estadística económica las casas de un mismo grupo se recalculan conjuntamente.

Endpoints asociados:
- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.id_ubi_hijo`
- `form.id_ubi_padre`
- `html.cancelar`
- `html.id_item`
- `html.ok`
- `post.id_item`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_eliminar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/grupo_eliminar`
- `/src/casas/grupo_form_data`
- `/src/casas/grupo_lista_data`
- `/src/casas/grupo_update`

## Errores Conocidos

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `debe indicar el grupo a eliminar`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`
## Ruta de menú

- **Legacy:** adl > Gestión casas > grupos
- **Pills2:** CASAS Y CTR > Gestión casas > grupos

