---
id: "actividadtarifas.tipo_tarifa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadtarifas"
nombre: "Flujo - Mantener los tipos de tarifa"
capacidad: "actividadtarifas.tipo_tarifa.gestionar"
pantallas_principales: ["actividadtarifas.pantalla.tarifa"]
fragmentos: ["actividadtarifas.pantalla.tarifa_form", "actividadtarifas.pantalla.tarifa_lista"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tipo Tarifa

Mantener el catálogo maestro de tipos de tarifa (letra, modo, observaciones).

## Objetivo De Usuario

Mantener el catálogo de tipos de tarifa: cada uno con su letra, su modo y observaciones.

## Punto De Entrada

Menú **adl > Tarifas > definir tarifa**.

## Fragmentos O Pantallas Auxiliares

- `actividadtarifas.pantalla.tarifa_form`
- `actividadtarifas.pantalla.tarifa_lista`

## Escenarios Inferidos

### Mantener los tipos de tarifa

Pasos propuestos:
1. Entrar en **definir tarifa**: aparece el listado de tarifas.
2. Para crear una, pulsar nuevo; escribir la **letra**, el **modo** y las **observaciones**, y pulsar **guardar** (o **cancel**).
3. Para modificar, pulsar la tarifa, cambiar los datos y **guardar**.
4. Para quitarla, abrirla y pulsar **eliminar**, confirmando.

Endpoints asociados:
- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tarifa`
- `form.letra`
- `form.modo`
- `form.observ`
- `html.id_tarifa`
- `html.letra`
- `html.observ`
- `post.id_tarifa`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Errores Conocidos

- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`

## Permisos

- Edición por fila: `have_perm_oficina('adl')` y sección coincidente.
- Alta: `adl`, `pr` o `calendario`.

## Ruta de menú

- **Legacy:** adl > Tarifas > definir tarifa; dre/Calendario/exterior > Tarifas > definir tarifa.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Definir tarifa.
