---
id: "actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Acta Notas Definitivas Grabar"
capacidad: "actividadestudios.acta_notas_definitivas_grabar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Notas Definitivas Grabar

Conversión del borrador del acta en notas definitivas (`PersonaNota` / tessera).

## Objetivo De Usuario

El usuario confirma las notas del acta como definitivas: el sistema convierte las
matrículas/notas borrador en registros `PersonaNota` definitivos, asignando época,
nivel y acta correspondiente. Sustituye la rama `que=3` del legacy
`apps/actividadestudios/controller/acta_notas_update.php`.

## Punto De Entrada

Pantalla `acta_notas` (`frontend/actividadestudios/controller/acta_notas.php`): la función
`fnjs_guardar_tessera` envía el formulario por AJAX a este endpoint (solo si `mod` no es
`nueva`).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En el acta de notas de una asignatura, revisar notas y situaciones de cada alumno.
2. Pulsar la acción de grabar definitivas (`fnjs_guardar_tessera`).
3. El sistema serializa `#f_1303` con `que=3` y llama al endpoint.
4. Si la respuesta es correcta, las notas quedan grabadas en tessera; si no, se muestra alerta.

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
