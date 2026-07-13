---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Peticiones Incorporar"
flujo: "actividadplazas.peticiones_incorporar.gestionar.flujo"
preguntas: ["Como ejecutar en Peticiones Incorporar?"]
pantallas_principales: ["actividadplazas.pantalla.incorporar_peticion"]
fragmentos: []
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
source: "docs/catalogo/actividadplazas/flujos/peticiones_incorporar.md"
estado_revision: "generado"
---

# Ayuda IA - Peticiones Incorporar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Peticiones Incorporar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Peticiones Incorporar?

## Donde Entrar

- Incorporar Peticion (`actividadplazas.pantalla.incorporar_peticion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Abrir **Incorporar peticiones de plazas** desde el menú (según tipo y colectivo).
2. Leer el texto explicativo y pulsar **Continuar** (`fnjs_incorporar_peticiones`).
3. El botón se deshabilita mientras se ejecuta; el sistema envía `sactividad` y `sasistentes` a
4. Muestra en `#resultado` cuántas peticiones se incorporaron (`incorporadas`) y el aviso de que no

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_incorporar`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.incorporar_peticion`

## Objetivo

Ejecutar el proceso masivo que convierte las primeras peticiones de plaza (orden = 1) en asistencias propias con plaza, para un tipo y colectivo, sin incorporar personas que ya tienen actividad propia en el periodo.

## Errores Documentados

- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
