---
id: "notas.acta_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Ver"
capacidad: "notas.acta_ver.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ver_formulario"]
endpoints: ["/src/notas/acta_ver_form_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Ver

Propuesta generada automaticamente desde la capacidad `notas.acta_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver y editar cabecera de acta, tribunal, PDF y vínculo a actividad CA.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/notas/acta_ver_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta_pdf`
- `form.search`
- `html.acta`
- `html.acta_pdf`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.refresh`

Acciones JavaScript:
- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_eliminar_pdf`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_nueva_convocatoria`
- `fnjs_upload_pdf`

## Endpoints Del Flujo

- `/src/notas/acta_ver_form_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)
