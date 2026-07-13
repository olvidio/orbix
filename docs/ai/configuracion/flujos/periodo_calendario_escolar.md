---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Periodo calendario escolar (interno)"
flujo: "configuracion.periodo_calendario_escolar.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: []
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
source: "docs/catalogo/configuracion/flujos/periodo_calendario_escolar.md"
estado_revision: "generado"
---

# Ayuda IA - Periodo calendario escolar (interno)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Periodo calendario escolar (interno)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

No hay pantalla de usuario: el frontend obtiene fechas de inicio/fin de curso STGR y CRT (caché en sesión o BD) para que `Periodo` calcule rangos de fechas en listados y filtros de calendario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
