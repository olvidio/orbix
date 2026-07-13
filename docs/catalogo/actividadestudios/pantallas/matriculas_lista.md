---
id: "actividadestudios.pantalla.matriculas_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Lista"
controller: "frontend/actividadestudios/controller/matriculas_lista.php"
vistas: ["frontend/actividadestudios/view/matriculas.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_lista_data"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "html.mod", "html.pau", "html.refresh", "post.empiezamax", "post.empiezamin", "post.mod", "post.periodo", "post.stack", "post.year"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_solo_uno", "fnjs_update_div", "fnjs_ver_ca"]
estado_revision: "revisado"
---

# Matriculas Lista

Listado de matrículas realizadas en un periodo, con filtro de fechas y acciones sobre filas
seleccionadas (ver asignaturas del CA, borrar matrícula).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_lista.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matriculas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/actividadestudios/matriculas_lista_data` (`fnjs_buscar` / carga inicial)
- `/src/actividadestudios/matricula_eliminar` (`fnjs_borrar`)

## Capacidades Relacionadas

- `actividadestudios.matricula.gestionar`
- `actividadestudios.matriculas.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mod`
- `post.periodo`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Manual De Usuario

Pantalla de menú con dos bloques:

1. **Filtro de periodo** (`PeriodoQue`): por defecto `curso_ca`. **Buscar** recarga la lista vía
   `matriculas_lista_data` con el rango ISO calculado.
2. **Tabla de matrículas** con columnas alumno, centro, DL, actividad, asignatura, preceptor y
   nota. Botones por selección:
   - **Ver asignaturas CA:** exige una fila; abre `dossiers_ver.php` con `pau=a`.
   - **Borrar matrícula:** confirma y llama a `matricula_eliminar`; al éxito refresca con
     `fnjs_actualizar`.

Los hidden del listado apuntan al dossier 3005 con permiso 3 y `queSel=asig`.

## Ruta de menú

- **Legacy:** vest > actas... > Matrículas
- **Pills2:** vest > actas... > Matrículas; ESTUDIOS > Actas y certificados > Matrículas realizadas; ESTUDIOS > Preparación planes estudio > Matrículas realizadas
