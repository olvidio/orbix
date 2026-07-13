---
id: "devel_db_admin.db_lugar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Db Lugar"
capacidad: "devel_db_admin.db_lugar.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.db_cambiar_nombre_que", "devel_db_admin.pantalla.db_crear_esquema_que", "devel_db_admin.pantalla.db_eliminar_esquema_que"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/db_lugar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Db Lugar

Propuesta generada automaticamente desde la capacidad `devel_db_admin.db_lugar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Recargar desplegable de delegación al cambiar región en formularios DB.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.db_cambiar_nombre_que`
- `devel_db_admin.pantalla.db_crear_esquema_que`
- `devel_db_admin.pantalla.db_eliminar_esquema_que`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Abrir la pantalla de entrada del flujo.
2. Completar parámetros (región, dl, flags).
3. Ejecutar y revisar avisos en pantalla.


Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.comun`
- `form.dl`
- `form.esquema`
- `form.esquema_origen`
- `form.region`
- `form.sf`
- `form.sv`
- `html.bcorregir`
- `html.bcrear`
- `html.bcrear_esquema`
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
- `fnjs_db_copiar`
- `fnjs_db_corregir_renombrar_esquema`
- `fnjs_db_crear_esquemas`
- `fnjs_db_crear_usuarios`
- `fnjs_db_eliminar`
- `fnjs_db_renombrar_esquema`
- `fnjs_db_verificar_renombrar_esquema`
- `fnjs_dl`
- `fnjs_enviar_formulario`
- `fnjs_html_verificacion`
- `fnjs_sincronizar_frm_verif`

## Endpoints Del Flujo

- `/src/devel_db_admin/db_lugar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
