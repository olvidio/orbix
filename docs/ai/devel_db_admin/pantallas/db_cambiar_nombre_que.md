---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "devel_db_admin"
titulo: "Db Cambiar Nombre Que"
pantalla: "devel_db_admin.pantalla.db_cambiar_nombre_que"
preguntas: ["Que se puede hacer en Db Cambiar Nombre Que?", "Que campos tiene Db Cambiar Nombre Que?", "Que acciones hay en Db Cambiar Nombre Que?"]
capacidades: ["devel_db_admin.db_lugar.gestionar", "devel_db_admin.db_propiedades.gestionar"]
endpoints: ["/src/devel_db_admin/db_lugar", "/src/devel_db_admin/db_propiedades_data"]
source: "docs/catalogo/devel_db_admin/pantallas/db_cambiar_nombre_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Db Cambiar Nombre Que

## Resumen

Asistente para renombrar un esquema DL: origen, región/dl destino y flags comun/sv/sf.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.comun`
- `form.dl`
- `form.esquema_origen`
- `form.region`
- `form.sf`
- `form.sv`
- `html.bcorregir`
- `html.bcrear`
- `html.bverif`
- `html.comun`
- `html.dl`
- `html.esquema_origen`
- `html.region`
- `html.sf`
- `html.sv`

## Acciones Detectadas

- `fnjs_db_corregir_renombrar_esquema`
- `fnjs_db_renombrar_esquema`
- `fnjs_db_verificar_renombrar_esquema`
- `fnjs_dl`
- `fnjs_enviar_formulario`
- `fnjs_html_verificacion`
- `fnjs_sincronizar_frm_verif`

## Capacidades Relacionadas

- `devel_db_admin.db_lugar.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Endpoints Relacionados

- `/src/devel_db_admin/db_lugar`
- `/src/devel_db_admin/db_propiedades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
