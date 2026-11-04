---
id: "notas.persona_nota_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Modificar una nota"
capacidad: "notas.persona_nota_editar.gestionar"
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/persona_nota_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Persona Nota Editar

Propuesta generada automaticamente desde la capacidad `notas.persona_nota_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Corregir los datos de una nota ya apuntada a una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**): buscar a la persona, abrir su ficha y, en «Relación de dossiers», pulsar **Notas del stgr de una persona (tessera)**.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.form_notas_de_una_persona`

## Escenarios Inferidos

### Modificar una nota

Pasos propuestos:
1. En la tabla de notas marcar la fila y pulsar **modificar nota**.
2. Cambiar los datos que hagan falta (nota, acta, fecha, época…).
3. Pulsar **Guardar**.

Endpoints asociados:
- `/src/notas/persona_nota_editar`

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

- `/src/notas/persona_nota_editar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
