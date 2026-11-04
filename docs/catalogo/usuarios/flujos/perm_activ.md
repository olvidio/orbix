---
id: "usuarios.perm_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Añadir un permiso en actividades"
capacidad: "usuarios.perm_activ.gestionar"
pantallas_principales: ["usuarios.pantalla.perm_activ_lista"]
fragmentos: ["usuarios.pantalla.perm_activ_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_activ_eliminar", "/src/usuarios/perm_activ_guardar", "/src/usuarios/perm_activ_lista"]
estado_revision: "revisado"
---

# Flujo - Perm Activ

## Objetivo De Usuario

Dar o quitar a un usuario permisos sobre un tipo de actividad (ver, modificar, crear, borrar) según la fase en que esté la actividad.

## Punto De Entrada

Menú **sistema > usuarios web > lista usuarios**: buscar al usuario, pulsar **editar** en su fila.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.perm_activ_lista`

## Escenarios Inferidos

### Añadir un permiso en actividades

Pasos propuestos:
1. En la ficha del usuario, en «Permisos en actividades», pulsar **añadir permiso**. Se abre «Añadir nuevo permiso a …».
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo de actividad con **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (cuanto más concreto, más manda: «sv n crt» manda sobre «sv n»).
3. Marcar en **afecta a** lo que se controla: datos, economic, sacd, ctr, id_tarifa, cargos, asistentes o asistentesSacd.
4. Para cada uno elegir la **fase de referencia** y el **permiso off (sin marcar)** y **permiso on (marcado)**: nada, ocupado, ver, modificar, crear o borrar. «Ocupado» es un punto intermedio entre nada y ver: se ve que la actividad está ocupada, pero no se puede ver la información de qué es lo que ocupa. Si falta la fase de referencia, avisa «OJO! Comprobar la fase de referencia.» (el aviso es intencionado y puede salir más de una vez al cambiar los desplegables).
5. Pulsar **guardar**. La ficha vuelve a mostrar la tabla con dl propia, tipo de actividad, afecta a, fase de referencia, permiso off y permiso on.

Endpoints asociados:
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/perm_activ_lista`

### Modificar o eliminar un permiso en actividades

Pasos propuestos:
1. En la tabla «propios», marcar la fila.
2. Pulsar **modificar** para cambiarla, o **eliminar** y confirmar «¿Esta seguro que desea borrar este permiso?».
3. A igual nivel, los permisos propios mandan sobre los de los grupos del usuario.

Endpoints asociados:
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
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
