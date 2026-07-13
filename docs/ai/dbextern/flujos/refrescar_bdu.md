---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Refrescar copia BDU"
flujo: "dbextern.refrescar_bdu.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/refrescar_bdu"]
source: "docs/catalogo/dbextern/flujos/refrescar_bdu.md"
estado_revision: "generado"
---

# Ayuda IA - Refrescar copia BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Refrescar copia BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`

## Objetivo

Si los datos de listas cambiaron después de la fecha mostrada, refrescar la copia local antes de sincronizar (operación de varios minutos).

## Errores Documentados

- `Error al refrescar la BDU: …`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
