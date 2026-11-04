---
id: "actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Grabar las notas definitivas"
capacidad: "actividadestudios.acta_notas_definitivas_grabar.gestionar"
pantallas_principales: ["actividadestudios.pantalla.acta_notas"]
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Notas Definitivas Grabar

Conversión del borrador del acta en notas definitivas (`PersonaNota` / tessera).

## Objetivo De Usuario

Confirmar las notas del acta como definitivas: las notas en borrador pasan a ser notas definitivas de cada persona, con su época, nivel y acta.

## Punto De Entrada

Botón de grabar las notas definitivas, en la pantalla del acta de notas de una asignatura.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Grabar las notas definitivas

Pasos propuestos:
1. En el acta de notas de una asignatura, revisar la nota y la situación de cada alumno.
2. Pulsar el botón de grabar las notas definitivas.
3. Si todo es correcto, las notas quedan grabadas en la tessera de cada alumno; si no, sale un aviso con el motivo.

Endpoints asociados:
- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta_nota`
- `form.form_preceptor`
- `form.id_nom`
- `form.nota_max`
- `form.nota_num`
- `html.form_preceptor[]`
- `html.id_nom[]`
- `html.que`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.opcional`
- `post.primary_key_s`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_guardar_tessera`

## Endpoints Del Flujo

- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde pantalla `acta_notas`, accesible desde
dossier 3005 o módulo de actas).

- **Legacy:** vest > actas... > actas (pantalla padre habitual).
- **Pills2:** ESTUDIOS > Actas y certificados > Actas.
