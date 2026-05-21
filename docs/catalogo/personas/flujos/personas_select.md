---
id: "personas.personas_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Personas Select"
capacidad: "personas.personas_select.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/personas_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Personas Select

Propuesta generada automaticamente desde la capacidad `personas.personas_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PersonasSelect. Endpoint JSON: datos crudos para la tabla personas_select.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.personas_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_dossier`
- `form.que`
- `form.sel`
- `html.id_dossier`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.cmb`
- `post.es_sacd`
- `post.exacto`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.que`
- `post.scroll_id`
- `post.stack`
- `post.tabla`
- `post.tipo`

Acciones JavaScript:
- `fnjs_actividades`
- `fnjs_copiar_tessera`
- `fnjs_dossiers`
- `fnjs_enviar_formulario`
- `fnjs_ficha`
- `fnjs_ficha_profe`
- `fnjs_home`
- `fnjs_imp_certificado`
- `fnjs_imp_tessera`
- `fnjs_lista_activ`
- `fnjs_matriculas`
- `fnjs_modificar`
- `fnjs_modificar_ctr`
- `fnjs_notas`
- `fnjs_peticion_activ`
- `fnjs_posibles_ca`
- `fnjs_solo_uno`
- `fnjs_tessera`
- `fnjs_update_div`
- `fnjs_upload_certificado`

## Endpoints Del Flujo

- `/src/personas/personas_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
