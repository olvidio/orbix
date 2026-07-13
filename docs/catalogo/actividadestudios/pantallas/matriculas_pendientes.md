---
id: "actividadestudios.pantalla.matriculas_pendientes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Pendientes"
controller: "frontend/actividadestudios/controller/matriculas_pendientes.php"
vistas: []
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_pendientes_data"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas_pendientes.gestionar"]
campos: ["html.mod", "html.pau", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div", "fnjs_ver_ca"]
estado_revision: "revisado"
---

# Matriculas Pendientes

Listado de matrículas con examen pendiente de acta (sin nota definitiva), con las mismas acciones
de consulta y borrado que `matriculas_lista`. El HTML se genera en el controller (sin vista PHTML
separada).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_pendientes.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas (markup inline en el controller).

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/actividadestudios/matriculas_pendientes_data` (carga de la tabla)
- `/src/actividadestudios/matricula_eliminar` (`fnjs_borrar`)

## Capacidades Relacionadas

- `actividadestudios.matricula.gestionar`
- `actividadestudios.matriculas_pendientes.gestionar`

## Campos Detectados

- `html.mod`
- `html.pau`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Manual De Usuario

Al cargar, consulta `matriculas_pendientes_data` y muestra la tabla con columnas actividad,
asignatura, alumno y preceptor. Incluye aviso opcional del backend.

Acciones por fila seleccionada:

- **Ver asignaturas CA:** una fila → `dossiers_ver.php` con `pau=a`.
- **Borrar matrícula:** confirmación → `matricula_eliminar` → refresco AJAX de la propia pantalla
  (`fnjs_actualizar`).

No tiene filtro de periodo (a diferencia de `matriculas_lista`).

## Ruta de menú

- **Legacy:** vest > actas... > Matr. Pendientes
- **Pills2:** vest > actas... > Matr. Pendientes; ESTUDIOS > Actas y certificados > Exam. pendientes de acta; ESTUDIOS > Preparación planes estudio > Exam. pendientes acta
