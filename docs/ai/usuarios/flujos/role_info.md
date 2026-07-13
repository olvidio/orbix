---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Role Info"
flujo: "usuarios.role_info.gestionar.flujo"
preguntas: ["Como ejecutar en Role Info?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_form"]
endpoints: ["/src/usuarios/role_info"]
source: "docs/catalogo/usuarios/flujos/role_info.md"
estado_revision: "generado"
---

# Ayuda IA - Role Info

Usa este documento para responder preguntas de usuario sobre como trabajar con `Role Info`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Role Info?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_form`

## Objetivo

Carga ficha rol: datos, permiso de edición y tabla grupmenus ya asignados.

## Errores Documentados

- `Usuario no encontrado`
- `Rol no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
