---
id: "usuarios.perm_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Perm Activ"
capacidad: "usuarios.perm_activ.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.perm_activ_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_activ_eliminar", "/src/usuarios/perm_activ_guardar", "/src/usuarios/perm_activ_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Perm Activ

Propuesta generada automaticamente desde la capacidad `usuarios.perm_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PermActiv. Descripcion funcional pendiente de revisar. Para la tabla slickGrid, el width debe ser en pixels No hay que poner unidades, pues da un error de javascript.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.perm_activ_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/usuarios/perm_activ_eliminar`

### Guardar

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
- `/src/usuarios/perm_activ_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.que`
- `form.sel`
- `html.que`
- `post.id_usuario`
- `post.olvidar`
- `post.quien`

Acciones JavaScript:
- `fnjs_add_perm_activ`
- `fnjs_del_perm_activ`
- `fnjs_enviar_formulario`
- `fnjs_mod_perm_activ`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/perm_activ_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
