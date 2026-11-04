---
id: "inventario.doc_asignar_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Guardar la asignación a centros"
capacidad: "inventario.doc_asignar_ctr.gestionar"
pantallas_principales: ["inventario.pantalla.doc_asignar_ctr"]
fragmentos: ["inventario.pantalla.doc_asignar_ctr"]
acciones: ["guardar"]
endpoints: ["/src/inventario/doc_asignar_ctr_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Doc Asignar Ctr

## Objetivo De Usuario

Guardar la asignación de un documento a los centros elegidos.

## Punto De Entrada

Se abre al pulsar **Asignar** en la lista de centros de **asignar documento**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_asignar_ctr`

## Escenarios Inferidos

### Guardar la asignación a centros

Pasos propuestos:
1. En **Asignar a centros (sin especificar lugar)**, marcar los centros y pulsar para continuar. En la pantalla siguiente completar, para cada centro, el número de registro (propone «último + 1») o de ejemplares y las fechas **recibido** y **asignado**.
2. Pulsar **guardar**.
3. Si hay algún problema aparece un aviso **respuesta**; si va bien, la pantalla vuelve atrás sola.

Endpoints asociados:
- `/src/inventario/doc_asignar_ctr_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.f_asignado`
- `html.f_recibido`
- `html.okay`
- `post.id_tipo_doc`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/inventario/doc_asignar_ctr_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
