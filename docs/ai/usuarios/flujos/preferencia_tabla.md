---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Preferencia Tabla"
flujo: "usuarios.preferencia_tabla.gestionar.flujo"
preguntas: ["Como obtener en Preferencia Tabla?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/preferencia_tabla_get"]
source: "docs/catalogo/usuarios/flujos/preferencia_tabla.md"
estado_revision: "generado"
---

# Ayuda IA - Preferencia Tabla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preferencia Tabla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener en Preferencia Tabla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona PreferenciaTabla. Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front. Entrada: - id_tabla (opcional): identificador del grid. Si viene vacío, no se devolverán preferencias específicas del grid (útil cuando sólo se necesita saber si el usuario prefiere HTML o SlickGrid). Salida: array asociativo con la forma: [ 'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion' 'slickgrid' => null|array, // prefs 'slickGrid_<id_tabla>_<idioma>' ] Para slickgrid se busca primero la preferencia del usuario actual; si no existe, se usa la del usuario 44 (default).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
