---
id: "pasarela.parametros_menu.consultar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Menú de parámetros de la pasarela"
capacidad: "pasarela.parametros_menu.consultar"
pantallas_principales: ["pasarela.pantalla.parametros_menu"]
fragmentos: ["pasarela.pantalla.activacion_lista", "pasarela.pantalla.nombre_lista", "pasarela.pantalla.contribucion_no_duerme_lista", "pasarela.pantalla.contribucion_reserva_lista"]
acciones: ["elegir"]
endpoints: []
estado_revision: "revisado"
---

# Flujo - Menú de parámetros de la pasarela

Índice de los cuatro parámetros que se configuran para la pasarela exterior.

## Objetivo De Usuario

La pasarela es el sistema web externo donde las personas se inscriben en las actividades y pagan; Orbix le publica las actividades. Elegir qué parámetro de la pasarela se quiere revisar o cambiar. Cada opción abre su propio listado en la misma ventana. Los importes de contribución se expresan en euros.

## Punto De Entrada

Menú **Pasarela > parámetros**. Muestra una lista de cuatro enlaces:

| Enlace | Para qué sirve | Flujo |
|--------|----------------|-------|
| **fecha de activación** | Cuándo se publica cada tipo de actividad en la pasarela | `pasarela.activacion.gestionar.flujo` |
| **nombres de actividades particulares** | Nombre exportado distinto al del tipo genérico | `pasarela.nombre.gestionar.flujo` |
| **Contribución no duerme** | Importe de contribución para quien no pernocta | `pasarela.contribucion_no_duerme.gestionar.flujo` |
| **Contribución reserva** | Importe de contribución al reservar plaza | `pasarela.contribucion_reserva.gestionar.flujo` |

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.activacion_lista`
- `pasarela.pantalla.nombre_lista`
- `pasarela.pantalla.contribucion_no_duerme_lista`
- `pasarela.pantalla.contribucion_reserva_lista`

## Escenarios Inferidos

### Abrir un parámetro

Pasos propuestos:
1. Entrar en **Pasarela > parámetros** desde el menú.
2. Pulsar el enlace del parámetro que interesa (por ejemplo **fecha de activación**).
3. Trabajar con el listado que aparece; los pasos de cada uno están en su propio apartado de este manual.

Endpoints asociados:
- Ninguno directo (cada enlace carga su listado).

## Campos Y Acciones Detectadas En Pantalla

Acciones JavaScript:
- `fnjs_update_div` (carga el listado elegido en la zona principal)

## Errores Conocidos

La pantalla es solo un índice y no genera errores propios.

## Ruta de menú

- **Legacy:** dre > Pasarela > parámetros
- **Pills2:** ACTIVIDADES > Pasarela > parámetros
