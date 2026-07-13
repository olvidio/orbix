---
id: "cambios.usuario_form_avisos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar avisos del usuario"
capacidad: "cambios.usuario_form_avisos.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_form_avisos"]
acciones: ["listar", "nuevo", "modificar", "eliminar"]
endpoints: ["/src/cambios/usuario_form_avisos_data", "/src/cambios/cambio_usuario_objeto_pref_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar avisos del usuario

## Objetivo De Usuario

Consultar y mantener las reglas de aviso configuradas para un usuario web.

## Punto De Entrada

Fragmento `usuario_form_avisos` embebido en la ficha de usuario (`quien=usuario`).

## Escenarios

### Listar

1. Abrir pestaña de avisos del usuario.
2. `usuario_form_avisos_data` carga la tabla de preferencias.

### Nuevo / Modificar

1. Pulsar añadir o modificar (una fila seleccionada).
2. Se abre `usuario_avisos_pref` con `salida=nuevo` o `modificar`.

### Eliminar

1. Seleccionar fila y pulsar eliminar.
2. `cambio_usuario_objeto_pref_eliminar` borra la preferencia.

## Errores Conocidos

- `No tiene permiso`

## Ruta de menú

sin entrada de menú en el índice
