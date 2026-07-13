---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Role Grupmenu Add"
flujo: "usuarios.role_grupmenu_add.gestionar.flujo"
preguntas: ["Como ejecutar en Role Grupmenu Add?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_grupmenu"]
endpoints: ["/src/usuarios/role_grupmenu_add"]
source: "docs/catalogo/usuarios/flujos/role_grupmenu_add.md"
estado_revision: "generado"
---

# Ayuda IA - Role Grupmenu Add

Usa este documento para responder preguntas de usuario sobre como trabajar con `Role Grupmenu Add`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Role Grupmenu Add?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_grupmenu`

## Objetivo

Asocia grupmenu a rol (tokens sel `id_role#id_grupmenu`).

## Errores Documentados

- `hay un error, no se ha guardado`
- `debe seleccionar uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
