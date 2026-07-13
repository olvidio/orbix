---
id: "actividadestudios.pantalla.acta_notas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Acta Notas"
controller: "frontend/actividadestudios/controller/acta_notas.php"
vistas: ["frontend/actividadestudios/view/acta_notas.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_ver.php"]
endpoints: ["/src/actividadestudios/acta_notas_data", "/src/actividadestudios/acta_notas_definitivas_grabar", "/src/actividadestudios/acta_notas_matricula_guardar"]
capacidades: ["actividadestudios.acta_notas.gestionar", "actividadestudios.acta_notas_definitivas_grabar.gestionar", "actividadestudios.acta_notas_matricula.gestionar"]
campos: ["form.acta_nota", "form.form_preceptor", "form.id_nom", "form.nota_max", "form.nota_num", "html.form_preceptor[]", "html.id_nom[]", "html.que", "post.id_activ", "post.id_asignatura", "post.id_nivel", "post.id_pau", "post.opcional", "post.primary_key_s", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_enviar_formulario", "fnjs_guardar_nota", "fnjs_guardar_tessera", "fnjs_imprimir", "fnjs_nota"]
estado_revision: "revisado"
---

# Acta Notas

Pantalla del acta de notas de una asignatura concreta dentro de una actividad: incluye el formulario
del acta (módulo `notas`) y, debajo, la tabla de alumnos matriculados con nota, preceptor y
situación de acta. Sucesor de `apps/actividadestudios/controller/acta_notas.php`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/acta_notas.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/acta_notas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_imprimir.php`
- `frontend/notas/controller/acta_ver.php`

## Endpoints Usados

- `/src/actividadestudios/acta_notas_data` (carga inicial)
- `/src/actividadestudios/acta_notas_matricula_guardar` (`fnjs_guardar_nota`, borrador por fila)
- `/src/actividadestudios/acta_notas_definitivas_grabar` (`fnjs_guardar_tessera`, notas definitivas)

## Capacidades Relacionadas

- `actividadestudios.acta_notas.gestionar`
- `actividadestudios.acta_notas_definitivas_grabar.gestionar`
- `actividadestudios.acta_notas_matricula.gestionar`

## Campos Detectados

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

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_guardar_nota`
- `fnjs_guardar_tessera`
- `fnjs_imprimir`
- `fnjs_nota`

## Manual De Usuario

Se abre desde el dossier de una actividad/asignatura con `id_activ` e `id_asignatura`. El
controller carga `acta_notas_data` y, si hay matriculados, incluye `acta_ver.php` para el bloque
superior del acta.

Flujo habitual:

1. Si el acta está en modo `nueva`, las acciones de notas avisan de que primero hay que guardar el
   acta.
2. Con permiso de edición (`permiso == 3`), cada fila permite editar preceptor, nota numérica/máxima
   y desplegable de acta; los cambios se guardan al vuelo con `fnjs_guardar_nota` (borrador en
   matrícula).
3. **Grabar notas en tessera** convierte las notas en definitivas vía
   `acta_notas_definitivas_grabar`.
4. **Imprimir** envía el form del acta a `acta_imprimir.php` (solo si el acta ya no es `nueva`).

Sin permiso de edición la tabla es de solo lectura.

## Ruta de menú

sin entrada de menú en el índice (se abre desde dossiers / navegación de actividad-asignatura)
