---
id: "cambios.cambio_usuario_objeto_pref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Crear un aviso de cambios"
capacidad: "cambios.cambio_usuario_objeto_pref.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["guardar"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_guardar"]
estado_revision: "revisado"
---

# Flujo - Guardar objeto de aviso

## Objetivo De Usuario

Guardar el objeto, tipo de actividad, fase y avisos de una preferencia de aviso de cambios.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Crear un aviso de cambios

Pasos propuestos:
1. Entrar en **gestionar avisos en cambios actividades**.
2. Elegir el objeto a vigilar, el tipo de actividad y la fase.
3. Marcar los campos y condiciones y guardar todo.

Endpoints asociados:
- `/src/cambios/cambio_usuario_objeto_pref_guardar`

## Errores Conocidos

- `falta id_usuario`, `id_tipo_activ invalido`, `Hay un error, no se ha guardado`

## Ruta de menú

sin entrada de menú en el índice
