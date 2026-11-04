---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dbextern"
titulo: "Ver la situación de la sincronización"
flujo: "dbextern.sincro_index.gestionar.flujo"
preguntas: ["Como ver la situación de la sincronización en Ver la situación de la sincronización?"]
pantallas_principales: ["dbextern.pantalla.sincro_index"]
fragmentos: ["dbextern.pantalla.sincro_index"]
endpoints: ["/src/dbextern/sincro_index_datos"]
source: "docs/catalogo/dbextern/flujos/sincro_index.md"
estado_revision: "generado"
---

# Ayuda IA - Ver la situación de la sincronización

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver la situación de la sincronización`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver la situación de la sincronización en Ver la situación de la sincronización?

## Donde Entrar

- Sincronizar con los datos de Listas (`dbextern.pantalla.sincro_index`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver la situación de la sincronización

1. Abrir **Actualizar datos desde BDU** y elegir el colectivo.
2. Aparece la tabla de 9 puntos con el número de casos de cada uno y los enlaces **ver** o **ejecutar**.
3. Si el usuario no tiene permiso o no se encuentra la delegación en listas, sale el aviso correspondiente.

Referencias tecnicas para verificar la respuesta:
- `/src/dbextern/sincro_index_datos`

## Pantallas Y Fragmentos Relacionados

- `dbextern.pantalla.sincro_index`
- `dbextern.pantalla.sincro_index`

## Objetivo

Al abrir la pantalla de sincronización, ver los contadores de la situación entre la BDU y Aquinate y entrar en cada subpantalla de resolución.

## Errores Documentados

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
