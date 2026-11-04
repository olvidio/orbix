---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Anotar ausencias de los sacerdotes de la zona"
flujo: "encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo"
preguntas: ["Como anotar ausencias de los sacerdotes de la zona en Anotar ausencias de los sacerdotes de la zona?"]
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias_jefe_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Anotar ausencias de los sacerdotes de la zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anotar ausencias de los sacerdotes de la zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como anotar ausencias de los sacerdotes de la zona en Anotar ausencias de los sacerdotes de la zona?

## Donde Entrar

- Sacd Ausencias Jefe Zona (`encargossacd.pantalla.sacd_ausencias_jefe_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Anotar ausencias de los sacerdotes de la zona

1. Entrar en **Ausencias**.
2. Elegir el **sacd** de la lista (los de la zona del jefe; el oficial de la dl ve todos).
3. Rellenar sus ausencias como en «Anotar una ausencia».

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias_jefe_zona`
- `encargossacd.pantalla.sacd_ausencias_jefe_zona`

## Objetivo

Que el jefe de zona anote las ausencias de los sacerdotes de su zona.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
