---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "las zonas de un sacerdote"
flujo: "zonassacd.zona_sacd_ajax.gestionar.flujo"
preguntas: ["Como gestionar las zonas de un sacerdote en las zonas de un sacerdote?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/zonassacd/zona_sacd_ajax"]
source: "docs/catalogo/zonassacd/flujos/zona_sacd_ajax.md"
estado_revision: "generado"
---

# Ayuda IA - las zonas de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `las zonas de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar las zonas de un sacerdote en las zonas de un sacerdote?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar las zonas de un sacerdote

1. Elegir la zona en «Lista de sacd de la zona».
2. Usar **modificar**, **cambiar asignación zona** o **añadir asignación iglesia/cgi** sobre los sacerdotes marcados.
3. La tabla se vuelve a cargar con los cambios.

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_sacd_ajax`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Cargar la lista de sacerdotes de la zona elegida y guardar los cambios de días y de zona.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
