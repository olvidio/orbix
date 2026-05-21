---
id: "actividadessacd.pantalla.com_sacd_txt"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadessacd"
nombre: "Com Sacd Txt"
controller: "frontend/actividadessacd/controller/com_sacd_txt.php"
vistas: ["frontend/actividadessacd/view/com_sacd_txt.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadessacd/locales_desplegable_data", "/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
capacidades: ["actividadessacd.locales_desplegable.gestionar", "actividadessacd.texto_comunicacion.gestionar"]
campos: ["html.comunicacion"]
acciones: ["fnjs_cancelar", "fnjs_get_texto", "fnjs_guardar", "fnjs_parse_rta_txt"]
estado_revision: "generado"
---

# Com Sacd Txt

Pantalla de edicion de textos de comunicacion a los sacd.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadessacd/controller/com_sacd_txt.php`

## Vistas Relacionadas

- `frontend/actividadessacd/view/com_sacd_txt.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadessacd/locales_desplegable_data`
- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

## Capacidades Relacionadas

- `actividadessacd.locales_desplegable.gestionar`
- `actividadessacd.texto_comunicacion.gestionar`

## Campos Detectados

- `html.comunicacion`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_get_texto`
- `fnjs_guardar`
- `fnjs_parse_rta_txt`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
