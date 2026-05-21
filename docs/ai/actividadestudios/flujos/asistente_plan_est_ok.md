---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Asistente Plan Est Ok"
flujo: "actividadestudios.asistente_plan_est_ok.gestionar.flujo"
preguntas: ["Como ejecutar en Asistente Plan Est Ok?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadestudios/asistente_plan_est_ok"]
source: "docs/catalogo/actividadestudios/flujos/asistente_plan_est_ok.md"
estado_revision: "generado"
---

# Ayuda IA - Asistente Plan Est Ok

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asistente Plan Est Ok`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Asistente Plan Est Ok?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona AsistentePlanEstOk. Marca el flag est_ok (plan de estudios confirmado) de un Asistente. Sustituye al case plan de update_3103.php.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
