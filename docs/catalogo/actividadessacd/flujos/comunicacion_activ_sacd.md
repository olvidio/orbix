---
id: "actividadessacd.comunicacion_activ_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Comunicacion Activ Sacd"
capacidad: "actividadessacd.comunicacion_activ_sacd.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Comunicacion Activ Sacd

Propuesta generada automaticamente desde la capacidad `actividadessacd.comunicacion_activ_sacd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ComunicacionActividadesSacd. Construye el listado de atencion de actividades a comunicar a los sacd (incluidas las de los "sacd de paso" cuando procede).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `post.id_nom`
- `post.periodo`
- `post.propuesta`
- `post.que`
- `post.sel`
- `post.year`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_construir_listado`
- `fnjs_enviar_mails`
- `fnjs_esc_html`
- `fnjs_left_side_hide`
- `fnjs_parse_rta_txt`
- `fnjs_pintar_sacds`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/actividadessacd/comunicacion_activ_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
