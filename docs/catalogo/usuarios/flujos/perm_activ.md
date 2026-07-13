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
estado_revision: "revisado"
---

# Flujo - Perm Activ

## Objetivo De Usuario

Gestión permisos actividad-proceso de un usuario (módulo procesos).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

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

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
