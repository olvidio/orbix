---
id: "pasarela.contribucion_reserva.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar contribución reserva"
capacidad: "pasarela.contribucion_reserva.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.contribucion_reserva_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_reserva_lista"
  - "\/src\/pasarela\/contribucion_reserva_default_data"
  - "\/src\/pasarela\/contribucion_reserva_default_guardar"
  - "\/src\/pasarela\/contribucion_reserva_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_reserva_excepcion_eliminar"
estado_revision: "revisado"
---

# Flujo - Gestionar contribución reserva

## Objetivo De Usuario

Porcentaje de contribución en reserva de plaza.

## Punto De Entrada

`contribucion_reserva_lista.php` desde parámetros.

## Escenarios

Listar, default y excepciones (misma UX que no duerme).

## Endpoints Del Flujo

- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
