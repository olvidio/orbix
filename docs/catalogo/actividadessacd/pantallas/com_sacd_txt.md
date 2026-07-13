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
estado_revision: "revisado"
---

# Com Sacd Txt

Fragmento de edición de los textos de comunicación a los sacd. Renderiza dos desplegables (`clave`,
`idioma`) y un `<textarea>` precargado con `com_sacd/es`. Lee y guarda por AJAX contra
`texto_comunicacion_data` y `texto_comunicacion_guardar`; el desplegable de idiomas se pobla desde
`locales_desplegable_data`.

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

1. Elegir la **clave** (comunicación a los sacerdotes o cada título de columna) y el **idioma**: el
   textarea se recarga con el texto guardado.
2. Editar el texto y pulsar **guardar**. Guardar con el textarea vacío elimina el texto de ese
   `{clave, idioma}`.

## Ruta de menú

- Sin entrada de menú en el índice: fragmento invocado desde la pantalla "Comunicación a los sacd"
  (`com_sacd_activ_periodo`) cuando el usuario tiene permiso de edición (`perm_mod_txt`).
