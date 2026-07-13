---
id: "personas.pantalla.personas_select"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "personas"
nombre: "Resultado búsqueda personas"
controller: "frontend/personas/controller/personas_select.php"
vistas: ["frontend/personas/view/personas_select.phtml"]
fragmentos_frontend: []
endpoints: ["/src/personas/personas_select_data"]
capacidades: ["personas.personas_select.gestionar"]
campos: ["form.sel", "form.que", "form.id_dossier"]
acciones: ["fnjs_home", "fnjs_ficha", "fnjs_dossiers", "fnjs_modificar", "fnjs_modificar_ctr", "fnjs_actividades", "fnjs_tessera", "fnjs_notas", "fnjs_matriculas", "fnjs_posibles_ca", "fnjs_peticion_activ", "fnjs_lista_activ", "fnjs_ficha_profe", "fnjs_imp_tessera", "fnjs_copiar_tessera", "fnjs_imp_certificado", "fnjs_upload_certificado"]
estado_revision: "revisado"
---

# Resultado búsqueda personas

Tabla `web\Lista` con personas que cumplen los criterios de `personas_que`. Botones contextuales
según colectivo, `permiso`, módulos instalados (`asistentes`, `notas`, `actividadestudios`, etc.)
y ámbito (`rstgr` simplifica botones).

## Tipo

- Subtipo: `pantalla_principal` (segunda pantalla del flujo búsqueda; también destino de otros módulos)
- Controller: `frontend/personas/controller/personas_select.php`

## Endpoints Usados

- `/src/personas/personas_select_data`

## Acciones principales

- `fnjs_home` / enlace → cabecera persona
- `fnjs_ficha` → alta (`nuevo=1`) o edición
- `fnjs_dossiers`, `fnjs_actividades`, tessera/notas/certificados (según permisos)
- `fnjs_modificar` → cambio STGR (`est`)
- `fnjs_modificar_ctr` → traslado centro (`sm`)

Token fila: `sel = id_nom#id_tabla`.

## Manual De Usuario

Pantalla revisada contra `frontend/personas/controller/personas_select.php`.

## Ruta de menú

Misma entrada que `personas_que` (resultado tras buscar). Sin entrada de menú directa independiente.
