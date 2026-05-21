---
id: "actividadestudios.pantalla.matriculas_pendientes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Pendientes"
controller: "frontend/actividadestudios/controller/matriculas_pendientes.php"
vistas: []
fragmentos_frontend: ["frontend/actividadestudios/controller/matriculas_pendientes.php", "frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_pendientes_data"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas_pendientes.gestionar"]
campos: ["html.mod", "html.pau", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div", "fnjs_ver_ca"]
estado_revision: "generado"
---

# Matriculas Pendientes

Para asegurar que inicia la sesión, y poder acceder a los permisos

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_pendientes.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/matriculas_pendientes.php`
- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matriculas_pendientes_data`

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
