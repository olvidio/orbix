---
id: "pasarela.activacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Activacion"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax"]
acciones: ["listar"]
endpoints: ["/src/pasarela/activacion_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Activacion

Propuesta generada automaticamente desde la capacidad `pasarela.activacion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActivacionLista. Devuelve el listado del parámetro fecha_activacion listo para serializar: - default: valor por defecto. - excepciones: array de filas {id_tipo_activ, etiqueta, valor}. El frontend renderiza la tabla a partir de estos datos; este caso de uso no genera HTML.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.activacion_ajax`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/pasarela/activacion_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.activacion`
- `form.default`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.activacion`
- `post.default`
- `post.id_tipo_activ`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

Acciones JavaScript:
- `fnjs_modificar_activacion`
- `fnjs_modificar_activacion_default`

## Endpoints Del Flujo

- `/src/pasarela/activacion_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
