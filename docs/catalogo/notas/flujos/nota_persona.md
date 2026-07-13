---
id: "notas.nota_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Nota Persona"
capacidad: "notas.nota_persona.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/nota_persona_form_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Nota Persona

Propuesta generada automaticamente desde la capacidad `notas.nota_persona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Formulario completo de nota: carga (`nota_persona_form_data`) y mutaciones.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.form_notas_de_una_persona`

## Escenarios Inferidos

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/notas/nota_persona_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.dl_org`
- `form.f_acta_iso`
- `form.id_nom`
- `html.acta`
- `html.detalle`
- `html.epoca`
- `html.f_acta`
- `html.id_asignatura`
- `html.nota_max`
- `html.nota_num`
- `html.preceptor`
- `html.tipo_acta`
- `post.id_asignatura_real`
- `post.id_pau`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.sel`

Acciones JavaScript:
- `fnjs_buscar_acta`
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_nota`
- `fnjs_update_activ`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/nota_persona_form_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
