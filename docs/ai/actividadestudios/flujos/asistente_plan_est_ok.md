---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Confirmar el plan de estudios"
flujo: "actividadestudios.asistente_plan_est_ok.gestionar.flujo"
preguntas: ["Como confirmar el plan de estudios en Confirmar el plan de estudios?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadestudios/asistente_plan_est_ok"]
source: "docs/catalogo/actividadestudios/flujos/asistente_plan_est_ok.md"
estado_revision: "generado"
---

# Ayuda IA - Confirmar el plan de estudios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Confirmar el plan de estudios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como confirmar el plan de estudios en Confirmar el plan de estudios?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Confirmar el plan de estudios

1. En el dossier de matrículas de una persona, marcar la casilla de plan de estudios confirmado.
2. Pulsar el botón de confirmar el plan.
3. La lista se refresca y el plan queda como confirmado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/asistente_plan_est_ok`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Marcar como confirmado el plan de estudios de un asistente.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
