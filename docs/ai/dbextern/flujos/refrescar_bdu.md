---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Refrescar la copia de la BDU"
flujo: "dbextern.refrescar_bdu.gestionar.flujo"
preguntas: ["Como refrescar la copia de la bdu en Refrescar la copia de la BDU?"]
pantallas_principales: ["dbextern.pantalla.sincro_index"]
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/refrescar_bdu"]
source: "docs/catalogo/dbextern/flujos/refrescar_bdu.md"
estado_revision: "generado"
---

# Ayuda IA - Refrescar la copia de la BDU

Usa este documento para responder preguntas de usuario sobre como trabajar con `Refrescar la copia de la BDU`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como refrescar la copia de la bdu en Refrescar la copia de la BDU?

## Donde Entrar

- Sincronizar con los datos de Listas (`dbextern.pantalla.sincro_index`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Refrescar la copia de la bdu

1. En la pantalla de sincronización, pulsar **refrescar**.
2. Esperar a que termine (varios minutos).
3. La pantalla se recarga con los contadores al día. Si falla, sale «Error al refrescar la BDU» con el motivo.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/refrescar_bdu`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`
- `dbextern.pantalla.sincro_index`

## Objetivo

Si los datos de las listas han cambiado después de la fecha que se muestra, actualizar la copia local de la BDU antes de sincronizar. Tarda varios minutos.

## Errores Documentados

- `Error al refrescar la BDU: …`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
