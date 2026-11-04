---
id: "actividadplazas.plazas_ceder.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Ceder plazas a otra delegación"
capacidad: "actividadplazas.plazas_ceder.gestionar"
pantallas_principales: ["actividadplazas.pantalla.resumen_plazas"]
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/plazas_ceder"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plazas Ceder

Cede (o quita) plazas de mi delegación a otra delegación en una actividad concreta.

## Objetivo De Usuario

Desde el resumen de plazas de una actividad, indicar cuántas plazas se ceden a otra delegación y confirmar el cambio.

## Punto De Entrada

Bloque **Ceder** al final del resumen de plazas, que se abre desde el botón de plazas de una actividad.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.resumen_plazas`

## Escenarios Inferidos

### Ceder plazas a otra delegación

Pasos propuestos:
1. Abrir el resumen de plazas de la actividad.
2. En el bloque **Ceder**, escribir el número de plazas y elegir la delegación que las recibe.
3. Pulsar **Guardar**.
4. La pantalla se recarga y muestra el nuevo reparto.

Endpoints asociados:
- `/src/actividadplazas/plazas_ceder`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/actividadplazas/plazas_ceder`

## Errores Conocidos

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

## Ruta de menú

- Sin entrada de menú en el índice: se ejecuta desde el resumen de plazas de una actividad (menú
  «Plazas» de la actividad), no directamente desde un menú.
