---
id: "encargossacd.encargo_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Encargo Ver"
capacidad: "encargossacd.encargo_ver.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_select", "encargossacd.pantalla.encargo_ver"]
acciones: ["crear", "eliminar", "obtener_datos"]
endpoints: ["/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_eliminar", "/src/encargossacd/encargo_ver_nuevo"]
estado_revision: "generado"
---

# Flujo - Gestionar Encargo Ver

Propuesta generada automaticamente desde la capacidad `encargossacd.encargo_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoVer. Alta de encargo desde el formulario de encargo_ver (antes encargo_ajax.php que=nuevo). Borrado desde lista encargo_select (antes encargo_ajax.php que=eliminar). Datos para la pantalla encargo_ver (nuevo / editar encargo). El frontend arma los frontend\shared\web\Desplegable a partir de los arrays devueltos.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_select`
- `encargossacd.pantalla.encargo_ver`

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/encargossacd/encargo_ver_eliminar`

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.desc_enc`
- `form.desc_lugar`
- `form.filtro_ctr`
- `form.grupo`
- `form.id_activ`
- `form.id_enc`
- `form.id_nom`
- `form.id_tipo_enc`
- `form.id_zona`
- `form.idioma_enc`
- `form.lst_ctrs`
- `form.nom_tipo`
- `form.observ`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.desc_lugar`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.desc_lugar`
- `post.filtro_ctr`
- `post.grupo`
- `post.id_enc`
- `post.id_tipo_enc`
- `post.id_zona`
- `post.que`
- `post.refresh`
- `post.sel`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_construir_desplegable`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_generarNomEnc`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_lista_ctrs`
- `fnjs_lista_ctrs_por_zona`
- `fnjs_lista_zonas`
- `fnjs_lst_tipo_enc`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_params`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
