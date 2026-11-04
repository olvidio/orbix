---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Eliminar un encargo de zona"
flujo: "misas.eliminar_encargo_zona.gestionar.flujo"
preguntas: ["Como eliminar un encargo de zona en Eliminar un encargo de zona?"]
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/eliminar_encargo_zona"]
source: "docs/catalogo/misas/flujos/eliminar_encargo_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar un encargo de zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar un encargo de zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar un encargo de zona en Eliminar un encargo de zona?

## Donde Entrar

- Ver Encargos Zona (`misas.pantalla.ver_encargos_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar un encargo de zona

1. Abrir el encargo y pulsar **Eliminar encargo**.
2. Confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/eliminar_encargo_zona`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_zona`
- `misas.pantalla.ver_encargos_zona`

## Objetivo

Eliminar un encargo de zona.

## Errores Documentados

- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
