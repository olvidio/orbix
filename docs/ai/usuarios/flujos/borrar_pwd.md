---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Borrar Pwd"
flujo: "usuarios.borrar_pwd.gestionar.flujo"
preguntas: ["Como ejecutar en Borrar Pwd?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/borrar_pwd"]
source: "docs/catalogo/usuarios/flujos/borrar_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Borrar Pwd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Borrar Pwd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Borrar Pwd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Herramienta de pruebas: resetea contraseñas al login en todos los esquemas (excepto superadmin id_role=1). Solo WEBDIR=pruebas o Docker.

## Errores Documentados

- `No se pudieron obtener esquemas`
- `Sólo se puede borrar en la base de datos de pruebas`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
