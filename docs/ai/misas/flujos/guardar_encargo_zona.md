---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Guardar un encargo de zona"
flujo: "misas.guardar_encargo_zona.gestionar.flujo"
preguntas: ["Como guardar un encargo de zona en Guardar un encargo de zona?"]
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/guardar_encargo_zona"]
source: "docs/catalogo/misas/flujos/guardar_encargo_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar un encargo de zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar un encargo de zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar un encargo de zona en Guardar un encargo de zona?

## Donde Entrar

- Ver Encargos Zona (`misas.pantalla.ver_encargos_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar un encargo de zona

1. Rellenar el formulario del encargo (el botón **generar** propone el nombre «tipo (lugar)») y pulsar **Grabar**.
2. Si el encargo ya no existe aparece «No se encuentra el encargo».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/guardar_encargo_zona`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_zona`
- `misas.pantalla.ver_encargos_zona`

## Objetivo

Crear o actualizar un encargo de zona.

## Errores Documentados

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
