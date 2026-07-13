---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "Zona Sacd"
flujo: "zonassacd.zona_sacd.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["zonassacd.pantalla.zona_sacd", "zonassacd.pantalla.zona_sacd_lista_ajax", "zonassacd.pantalla.zona_sacd_update_ajax"]
endpoints: ["/src/zonassacd/zona_sacd", "/src/zonassacd/zona_sacd_lista", "/src/zonassacd/zona_sacd_update"]
source: "docs/catalogo/zonassacd/flujos/zona_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Zona Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Zona Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd_lista_ajax`
- `zonassacd.pantalla.zona_sacd_update_ajax`

## Objetivo

Consultar y gestionar la asignación de sacerdotes (sacd) a zonas geográficas: listado por zona, cambio de zona propia, asignaciones iglesia/cgi y edición de días de atención semanal.

## Errores Documentados

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
