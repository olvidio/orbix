---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacd Reordenar"
flujo: "actividadessacd.sacd_reordenar.gestionar.flujo"
preguntas: ["Como ejecutar en Sacd Reordenar?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacd_reordenar"]
source: "docs/catalogo/actividadessacd/flujos/sacd_reordenar.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Reordenar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Reordenar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Sacd Reordenar?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **más prioridad** o **menos prioridad**.
3. El sistema intercambia el orden y refresca la celda de sacd de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacd_reordenar`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

El usuario sube o baja la prioridad de un sacd ya asignado intercambiando su posición con el anterior o el siguiente en el listado de cargos `sacd` de la actividad.

## Errores Documentados

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
