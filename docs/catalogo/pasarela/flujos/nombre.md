---
id: "pasarela.nombre.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Nombre"
capacidad: "pasarela.nombre.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.nombre_ajax"]
acciones: ["listar"]
endpoints: ["/src/pasarela/nombre_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Nombre

Propuesta generada automaticamente desde la capacidad `pasarela.nombre.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona NombreLista. Devuelve el listado del parámetro nombre listo para serializar. Estructura: {excepciones: [{id_tipo_activ, etiqueta, valor}]}. (El parámetro nombre no tiene valor por defecto.).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.nombre_ajax`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/pasarela/nombre_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.nombre_actividad`
- `post.id_tipo_activ`
- `post.nombre_actividad`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

Acciones JavaScript:
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/pasarela/nombre_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
