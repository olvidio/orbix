---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Cambiar las fechas del curso"
flujo: "configuracion.periodo_calendario_escolar.gestionar.flujo"
preguntas: ["Como cambiar las fechas del curso en Cambiar las fechas del curso?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
source: "docs/catalogo/configuracion/flujos/periodo_calendario_escolar.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar las fechas del curso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar las fechas del curso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar las fechas del curso en Cambiar las fechas del curso?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar las fechas del curso

1. Abrir **config esquema** (ver «Cambiar un parámetro»).
2. Cambiar el periodo del curso del stgr o del crt y guardar; los listados de calendario usarán las nuevas fechas.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/periodo_calendario_escolar_data`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

No hay pantalla de usuario: es un dato interno. Los listados y filtros de calendario usan las fechas de inicio y fin del curso del stgr y del crt para calcular sus periodos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
