---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asignaturas"
titulo: "Datos de asignaturas"
flujo: "asignaturas.api_soporte.gestionar.flujo"
preguntas: ["Como datos de asignaturas en Datos de asignaturas?"]
pantallas_principales: ["asignaturas.pantalla.api_soporte"]
fragmentos: []
endpoints: ["/src/asignaturas/asignaturas_map_data", "/src/asignaturas/asignaturas_con_separador_data"]
source: "docs/catalogo/asignaturas/flujos/asignaturas_api.md"
estado_revision: "generado"
---

# Ayuda IA - Datos de asignaturas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Datos de asignaturas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como datos de asignaturas en Datos de asignaturas?

## Donde Entrar

- API soporte asignaturas (`asignaturas.pantalla.api_soporte`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Datos de asignaturas

1. Al elegir una asignatura en notas o profesores, la lista se carga automáticamente desde este servicio.

Referencias tecnicas para verificar la respuesta:
- `/src/asignaturas/asignaturas_map_data`
- `/src/asignaturas/asignaturas_con_separador_data`

## Pantallas Y Fragmentos Relacionados

- `asignaturas.pantalla.api_soporte`

## Objetivo

Datos de asignaturas que usan otros módulos (notas, profesores); no tiene pantalla propia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
