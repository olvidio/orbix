---
id: "actividadestudios.pantalla.matriculas_lista_otras_r"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Lista Otras R"
controller: "frontend/actividadestudios/controller/matriculas_lista_otras_r.php"
vistas: ["frontend/actividadestudios/view/matriculas_otras_r.phtml"]
fragmentos_frontend: ["frontend/certificados/controller/certificado_emitido_imprimir.php"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
capacidades: ["actividadestudios.matriculas_lista_otras_r.gestionar"]
campos: ["form.apellido1", "html.apellido1", "html.btn", "html.mod", "html.pau", "html.refresh", "post.apellido1", "post.mod", "post.stack"]
acciones: ["fnjs_buscar", "fnjs_buscar_por_apellidos", "fnjs_enviar_formulario", "fnjs_imp_certificado", "fnjs_left_side_hide", "fnjs_solo_uno"]
estado_revision: "revisado"
---

# Matriculas Lista Otras R

Listado de alumnos de otras regiones para certificados STGR: búsqueda por apellido, alertas de
acta/región y emisión de certificado. Solo accesible en ámbito `rstgr` o `r`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_lista_otras_r.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matriculas_otras_r.phtml`

## Fragmentos Frontend Relacionados

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

1. Si el ámbito no es regional STGR, la pantalla termina con «Solamente lo pueden ver las regiones
   del stgr».
2. **Búsqueda por apellidos:** campo `apellido1` y botón buscar recargan vía
   `matriculas_lista_otras_r_data`.
3. La tabla muestra alumno, DL, alerta (`!` = sin acta firmada; ⚠ = falta región STGR de la DL),
   asignaturas e id.
4. **Imprimir certificado** (una fila) envía el formulario a
   `certificado_emitido_imprimir.php` en ventana de impresión.

Errores de configuración de delegaciones se muestran como aviso destacado sin bloquear la pantalla.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Actas y certificados > Envío información a otras r
