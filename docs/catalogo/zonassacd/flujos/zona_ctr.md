---
id: "zonassacd.zona_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar Zona Ctr"
capacidad: "zonassacd.zona_ctr.gestionar"
pantallas_principales: []
fragmentos: ["zonassacd.pantalla.zona_ctr", "zonassacd.pantalla.zona_ctr_lista_ajax", "zonassacd.pantalla.zona_ctr_update_ajax"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Zona Ctr

Propuesta generada automaticamente desde la capacidad `zonassacd.zona_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ZonaCtr, ZonaCtrLista, ZonaCtrPage. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_lista_ajax`
- `zonassacd.pantalla.zona_ctr_update_ajax`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/zonassacd/zona_ctr_update`

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/zonassacd/zona_ctr_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`
- `form.id_zona_new`
- `html.id_zona_new`
- `html.ok`
- `post.id_zona`
- `post.id_zona_new`
- `post.sel`

Acciones JavaScript:
- `fnjs_busca_ctrs`
- `fnjs_guardar`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
