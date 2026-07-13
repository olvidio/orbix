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

1. En una actividad con permiso, pulsar **nuevo** para ver los sacd candidatos.
2. Pulsar el sacd deseado (titular del centro o global según checkboxes de selección).
3. El sistema lo guarda como encargado y refresca la celda de sacd de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacd_asignar`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

El usuario asigna un sacd candidato (elegido en el desplegable de disponibles) a una actividad. El sacd queda en el primer hueco libre de cargos tipo `sacd`. Si la actividad es de sv (`id_tipo_activ` empieza por `1`), se crea además la fila de asistencia.

## Errores Documentados

- `No puede haber tantos cargos de sacd en una actividad`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
