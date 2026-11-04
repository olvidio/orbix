---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Guardar las ausencias de un sacerdote"
flujo: "encargossacd.sacd_ausencias.gestionar.flujo"
preguntas: ["Como guardar las ausencias de un sacerdote en Guardar las ausencias de un sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_update"]
endpoints: ["/src/encargossacd/sacd_ausencias_update"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar las ausencias de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar las ausencias de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar las ausencias de un sacerdote en Guardar las ausencias de un sacerdote?

## Donde Entrar

- Sacd Ausencias (`encargossacd.pantalla.sacd_ausencias`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar las ausencias de un sacerdote

1. Elegir el tipo y el sacd.
2. Rellenar fechas de inicio y fin y el tipo de ausencia; para borrar una, dejar las dos fechas en blanco.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/sacd_ausencias_update`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias`
- `encargossacd.pantalla.sacd_ausencias_update`

## Objetivo

Guardar o modificar las ausencias y otras tareas de un sacerdote.

## Errores Documentados

- `no se ha encontrado el encargo del sacd`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
