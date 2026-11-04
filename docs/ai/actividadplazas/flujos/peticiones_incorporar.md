---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Incorporar las primeras peticiones"
flujo: "actividadplazas.peticiones_incorporar.gestionar.flujo"
preguntas: ["Como incorporar las primeras peticiones en Incorporar las primeras peticiones?"]
pantallas_principales: ["actividadplazas.pantalla.incorporar_peticion"]
fragmentos: []
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
source: "docs/catalogo/actividadplazas/flujos/peticiones_incorporar.md"
estado_revision: "generado"
---

# Ayuda IA - Incorporar las primeras peticiones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Incorporar las primeras peticiones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como incorporar las primeras peticiones en Incorporar las primeras peticiones?

## Donde Entrar

- Incorporar Peticion (`actividadplazas.pantalla.incorporar_peticion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Incorporar las primeras peticiones

1. Abrir **Incorporar peticiones de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Leer el texto explicativo y pulsar **Continuar**. El botón se desactiva mientras se ejecuta.
3. Al terminar se muestra cuántas peticiones se han incorporado, con el recordatorio de que no se incorpora a quien ya tiene actividad propia.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_incorporar`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.incorporar_peticion`

## Objetivo

Convertir de golpe las primeras peticiones de plaza de cada persona en asistencias propias con plaza, para un tipo de actividad y un colectivo. No se incorpora a quien ya tiene una actividad propia en ese periodo.

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
