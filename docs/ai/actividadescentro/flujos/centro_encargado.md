---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centro Encargado"
flujo: "actividadescentro.centro_encargado.gestionar.flujo"
preguntas: ["Como eliminar en Centro Encargado?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centro_encargado_eliminar"]
source: "docs/catalogo/actividadescentro/flujos/centro_encargado.md"
estado_revision: "generado"
---

# Ayuda IA - Centro Encargado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centro Encargado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Centro Encargado?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **borrar**.
3. El sistema lo elimina y refresca la celda de centros de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centro_encargado_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`

## Objetivo

El usuario quita un centro de la lista de encargados de una actividad.

## Errores Documentados

- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`
- `no se sabe cual borrar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
