---
id: "encargossacd.sacd_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Sacd Select"
capacidad: "encargossacd.sacd_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Sacd Select

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdSelect. Opciones para el desplegable de SACDs filtrados por tabla (sacd_ficha_ajax?que=get_select).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ficha_ajax`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `form.enc_num`
- `form.id_tipo_enc`
- `form.mas`
- `form.observ`
- `html.dedic_m[<?= $j ?>]`
- `html.dedic_t[<?= $j ?>]`
- `html.dedic_v[<?= $j ?>]`
- `html.enc_num`
- `html.ok`
- `post.filtro_sacd`
- `post.id_nom`
- `post.que`

Acciones JavaScript:
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_mas_enc`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
