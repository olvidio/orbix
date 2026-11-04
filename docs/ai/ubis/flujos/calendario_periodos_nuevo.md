---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Crear un periodo nuevo"
flujo: "ubis.calendario_periodos_nuevo.gestionar.flujo"
preguntas: ["Como crear un periodo nuevo en Crear un periodo nuevo?"]
pantallas_principales: ["ubis.pantalla.calendario_periodos_nuevo"]
fragmentos: ["ubis.pantalla.calendario_periodos_nuevo"]
endpoints: ["/src/ubis/calendario_periodos_nuevo_data"]
source: "docs/catalogo/ubis/flujos/calendario_periodos_nuevo.md"
estado_revision: "generado"
---

# Ayuda IA - Crear un periodo nuevo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear un periodo nuevo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear un periodo nuevo en Crear un periodo nuevo?

## Donde Entrar

- Calendario Periodos Nuevo (`ubis.pantalla.calendario_periodos_nuevo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear un periodo nuevo

1. En **Definir periodos**, elegir la casa y el año y pulsar el botón de nuevo periodo.
2. El formulario propone como inicio el día siguiente al último periodo y su misma sección (sf/sv).
3. Ajustar las fechas y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/calendario_periodos_nuevo_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.calendario_periodos_nuevo`
- `ubis.pantalla.calendario_periodos_nuevo`

## Objetivo

Abrir el formulario de alta de un periodo ya relleno con la fecha siguiente al último.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
