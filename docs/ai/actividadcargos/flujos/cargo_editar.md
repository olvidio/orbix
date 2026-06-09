---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Cargo Editar"
flujo: "actividadcargos.cargo_editar.gestionar.flujo"
preguntas: ["Como ejecutar en Cargo Editar?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadcargos/cargo_editar"]
source: "docs/catalogo/actividadcargos/flujos/cargo_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Cargo Editar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cargo Editar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Cargo Editar?

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

Guardar cambios en un cargo existente (tipo, AGD, observaciones y sincronización de asistente). Plantilla de redacción revisada en `docs/manual/actividadcargos.md` (sección Cargo Editar).

## Errores Documentados

- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
