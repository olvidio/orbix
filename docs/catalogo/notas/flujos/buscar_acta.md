---
id: "notas.buscar_acta.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Elegir el acta de una nota"
capacidad: "notas.buscar_acta.gestionar"
pantallas_principales: ["notas.pantalla.form_notas_de_una_persona"]
fragmentos: ["notas.pantalla.form_notas_de_una_persona"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/buscar_acta"]
estado_revision: "revisado"
---

# Flujo - Gestionar Buscar Acta

Propuesta generada automaticamente desde la capacidad `notas.buscar_acta.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ayudar a elegir un acta mientras se rellenan las notas de una persona.

## Punto De Entrada

Formulario de notas de una persona.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.form_notas_de_una_persona`

## Escenarios Inferidos

### Elegir el acta de una nota

Pasos propuestos:
1. En el formulario de nota, empezar a escribir el número del acta en **acta** (formato num/aa).
2. Elegir de la lista que aparece.

Endpoints asociados:
- `/src/notas/buscar_acta`

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

- `/src/notas/buscar_acta`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
