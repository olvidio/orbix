---
id: "cambios.usuario_form_avisos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar los avisos de cambios de un usuario"
capacidad: "cambios.usuario_form_avisos.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_form_avisos"]
fragmentos: ["cambios.pantalla.usuario_form_avisos"]
acciones: ["listar", "nuevo", "modificar", "eliminar"]
endpoints: ["/src/cambios/usuario_form_avisos_data", "/src/cambios/cambio_usuario_objeto_pref_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar avisos del usuario

## Objetivo De Usuario

Ver y gestionar los avisos de cambios en actividades que recibe un usuario.

## Punto De Entrada

Menú **sistema > usuarios web > lista usuarios**: buscar al usuario, pulsar **editar** en su fila. También desde las preferencias del propio usuario («gestionar avisos en cambios actividades»).

## Escenarios Inferidos

### Gestionar los avisos de cambios de un usuario

Pasos propuestos:
1. En la ficha del usuario, apartado «avisos de cambios», se ve la tabla de avisos (o «no hay ninguna fila»).
2. Pulsar **añadir aviso** para crear uno, o marcar una fila y modificarla o eliminarla.

Endpoints asociados:
- `/src/cambios/usuario_form_avisos_data`
- `/src/cambios/cambio_usuario_objeto_pref_eliminar`

## Errores Conocidos

- `No tiene permiso`

## Ruta de menú

sin entrada de menú en el índice
