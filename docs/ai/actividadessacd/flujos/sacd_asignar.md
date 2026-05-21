---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacd Asignar"
flujo: "actividadessacd.sacd_asignar.gestionar.flujo"
preguntas: ["Como ejecutar en Sacd Asignar?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacd_asignar"]
source: "docs/catalogo/actividadessacd/flujos/sacd_asignar.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Asignar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Asignar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Sacd Asignar?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Gestiona SacdAsignar. Asigna un sacd a una actividad (y, si es sv, tambien crea la asistencia).

## Errores Documentados

- `No puede haber tantos cargos de sacd en una actividad`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
