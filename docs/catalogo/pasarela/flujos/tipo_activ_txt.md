---
id: "pasarela.tipo_activ_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Tipo Activ Txt"
capacidad: "pasarela.tipo_activ_txt.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax", "pasarela.pantalla.contribucion_no_duerme_ajax", "pasarela.pantalla.contribucion_reserva_ajax", "pasarela.pantalla.nombre_ajax"]
acciones: ["obtener_datos"]
endpoints: ["/src/pasarela/tipo_activ_txt_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Activ Txt

Propuesta generada automaticamente desde la capacidad `pasarela.tipo_activ_txt.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoActivTxt. Devuelve el texto descriptivo (sfsv asistentes actividad) para un id_tipo_activ. Lo consumen los formularios form_modificar desde el frontend para mostrar a qué tipo de actividad corresponde la fila editada.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.activacion_ajax`
- `pasarela.pantalla.contribucion_no_duerme_ajax`
- `pasarela.pantalla.contribucion_reserva_ajax`
- `pasarela.pantalla.nombre_ajax`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.activacion`
- `form.contribucion`
- `form.default`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.nombre_actividad`
- `post.activacion`
- `post.contribucion`
- `post.default`
- `post.id_tipo_activ`
- `post.nombre_actividad`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

Acciones JavaScript:
- `fnjs_modificar`
- `fnjs_modificar_activacion`
- `fnjs_modificar_activacion_default`
- `fnjs_modificar_default`

## Endpoints Del Flujo

- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
