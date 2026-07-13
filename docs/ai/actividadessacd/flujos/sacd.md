---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacd"
flujo: "actividadessacd.sacd.gestionar.flujo"
preguntas: ["Como eliminar en Sacd?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacd_eliminar"]
source: "docs/catalogo/actividadessacd/flujos/sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Sacd?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **borrar** en el menú contextual.
3. El sistema elimina cargo y asistencia (si aplica) y refresca la celda de sacd de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacd_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

El usuario quita un sacd ya asignado a una actividad. El sistema elimina el cargo (`ActividadCargo`) y, si existe, la fila de asistencia asociada (`Asistencia`).

## Errores Documentados

- `no se sabe cual borrar`
- `hay un error, no se ha eliminado el cargo`
- `hay un error, no se ha eliminado la asistencia`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
