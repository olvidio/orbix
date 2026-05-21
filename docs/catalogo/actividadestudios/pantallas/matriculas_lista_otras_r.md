---
id: "actividadestudios.pantalla.matriculas_lista_otras_r"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Lista Otras R"
controller: "frontend/actividadestudios/controller/matriculas_lista_otras_r.php"
vistas: ["frontend/actividadestudios/view/matriculas_otras_r.phtml"]
fragmentos_frontend: ["frontend/actividadestudios/controller/matriculas_lista_otras_r.php", "frontend/certificados/controller/certificado_emitido_imprimir.php"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
capacidades: ["actividadestudios.matriculas_lista_otras_r.gestionar"]
campos: ["form.apellido1", "html.apellido1", "html.btn", "html.mod", "html.pau", "html.refresh", "post.apellido1", "post.mod", "post.stack"]
acciones: ["fnjs_buscar", "fnjs_buscar_por_apellidos", "fnjs_enviar_formulario", "fnjs_imp_certificado", "fnjs_left_side_hide", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Matriculas Lista Otras R

Para asegurar que inicia la sesion, y poder acceder a los permisos

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_lista_otras_r.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matriculas_otras_r.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/matriculas_lista_otras_r.php`
- `frontend/certificados/controller/certificado_emitido_imprimir.php`

## Endpoints Usados

- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Capacidades Relacionadas

- `actividadestudios.matriculas_lista_otras_r.gestionar`

## Campos Detectados

- `form.apellido1`
- `html.apellido1`
- `html.btn`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.apellido1`
- `post.mod`
- `post.stack`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_buscar_por_apellidos`
- `fnjs_enviar_formulario`
- `fnjs_imp_certificado`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
