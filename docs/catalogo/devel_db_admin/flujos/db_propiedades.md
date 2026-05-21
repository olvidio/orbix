---
id: "devel_db_admin.db_propiedades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Db Propiedades"
capacidad: "devel_db_admin.db_propiedades.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.apptables", "devel_db_admin.pantalla.db_absorber_esquema_que", "devel_db_admin.pantalla.db_cambiar_nombre_que", "devel_db_admin.pantalla.db_crear_esquema_que", "devel_db_admin.pantalla.db_eliminar_esquema_que", "devel_db_admin.pantalla.db_mover_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Db Propiedades

Propuesta generada automaticamente desde la capacidad `devel_db_admin.db_propiedades.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DbPropiedades. JSON para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.apptables`
- `devel_db_admin.pantalla.db_absorber_esquema_que`
- `devel_db_admin.pantalla.db_cambiar_nombre_que`
- `devel_db_admin.pantalla.db_crear_esquema_que`
- `devel_db_admin.pantalla.db_eliminar_esquema_que`
- `devel_db_admin.pantalla.db_mover_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.comun`
- `form.dl`
- `form.esquema`
- `form.esquema_del`
- `form.esquema_matriz`
- `form.esquema_origen`
- `form.id_app`
- `form.region`
- `form.sf`
- `form.sv`
- `form.tabla`
- `html.bce`
- `html.bcg`
- `html.bcorregir`
- `html.bcrear`
- `html.bcrear_esquema`
- `html.bee`
- `html.beg`
- `html.beliminar`
- `html.bimportar`
- `html.bverif`
- `html.comun`
- `html.dl`
- `html.esquema_origen`
- `html.region`
- `html.sf`
- `html.sv`

Acciones JavaScript:
- `fnjs_absorber_dl`
- `fnjs_db`
- `fnjs_db_copiar`
- `fnjs_db_corregir_renombrar_esquema`
- `fnjs_db_crear_esquemas`
- `fnjs_db_crear_usuarios`
- `fnjs_db_eliminar`
- `fnjs_db_mover_tabla`
- `fnjs_db_renombrar_esquema`
- `fnjs_db_verificar_renombrar_esquema`
- `fnjs_dl`
- `fnjs_enviar_formulario`
- `fnjs_html_verificacion`
- `fnjs_sincronizar_frm_verif`

## Endpoints Del Flujo

- `/src/devel_db_admin/db_propiedades_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
