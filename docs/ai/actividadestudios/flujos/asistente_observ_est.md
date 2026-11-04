---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Guardar las observaciones del plan de estudios"
flujo: "actividadestudios.asistente_observ_est.gestionar.flujo"
preguntas: ["Como guardar las observaciones del plan de estudios en Guardar las observaciones del plan de estudios?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadestudios/asistente_observ_est"]
source: "docs/catalogo/actividadestudios/flujos/asistente_observ_est.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar las observaciones del plan de estudios

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar las observaciones del plan de estudios`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar las observaciones del plan de estudios en Guardar las observaciones del plan de estudios?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar las observaciones del plan de estudios

1. En el dossier de matrículas de una persona, escribir el texto en las observaciones del plan de estudios.
2. Pulsar el botón para grabar las observaciones.
3. Si se guarda bien, la lista se refresca con el texto nuevo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/asistente_observ_est`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Guardar las observaciones sobre el plan de estudios de un asistente en su actividad.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
