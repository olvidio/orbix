---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Asistente Observ"
flujo: "actividadestudios.asistente_observ.gestionar.flujo"
preguntas: ["Como ejecutar en Asistente Observ?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadestudios/asistente_observ"]
source: "docs/catalogo/actividadestudios/flujos/asistente_observ.md"
estado_revision: "generado"
---

# Ayuda IA - Asistente Observ

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asistente Observ`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Asistente Observ?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Desde el contexto de un asistente en una actividad, editar el campo `observ`.
2. Enviar `id_activ`, `id_nom` (o `id_pau`) y `observ` al endpoint.
3. El sistema localiza al asistente y persiste el texto.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/asistente_observ`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

El usuario guarda el texto de observaciones generales (`observ`) de un asistente en una actividad. Sustituye al case `observ` de `update_3103.php`.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
