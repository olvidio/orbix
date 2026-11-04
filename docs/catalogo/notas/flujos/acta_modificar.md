---
id: "notas.acta_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Modificar un acta"
capacidad: "notas.acta_modificar.gestionar"
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/notas/acta_modificar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Modificar

Propuesta generada automaticamente desde la capacidad `notas.acta_modificar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar los cambios de un acta existente.

## Punto De Entrada

Ficha del acta (**actas** > abrir).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Modificar un acta

Pasos propuestos:
1. Abrir el acta desde **actas**.
2. Cambiar la fecha, libro, página, línea, lugar, observaciones, la asignatura o los examinadores; el número de acta ya no se cambia. La fecha es obligatoria.
3. Pulsar **Guardar cambios acta**. Un acta que ya tiene el PDF firmado adjunto no se puede modificar.

Endpoints asociados:
- `/src/notas/acta_modificar`

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

- `/src/notas/acta_modificar`

## Errores Conocidos

- ``No se encuentra el acta``

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
