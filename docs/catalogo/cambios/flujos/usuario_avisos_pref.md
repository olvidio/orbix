---
id: "cambios.usuario_avisos_pref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Usuario Avisos Pref"
capacidad: "cambios.usuario_avisos_pref.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["ver_formulario"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Avisos Pref

Propuesta generada automaticamente desde la capacidad `cambios.usuario_avisos_pref.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioAvisosPref. Endpoint JSON que devuelve la informacion necesaria para pintar el formulario usuario_avisos_pref (edicion de un aviso de usuario/grupo).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.usuario_avisos_pref`

## Escenarios Inferidos

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/cambios/usuario_avisos_pref_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.dl_propia`
- `html.id_tipo_activ`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.id_usuario`
- `post.quien`
- `post.salida`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_fases`
- `fnjs_actualizar_propiedades`
- `fnjs_cerrar`
- `fnjs_grabar_todo`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`
- `fnjs_modificar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/cambios/usuario_avisos_pref_form_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
