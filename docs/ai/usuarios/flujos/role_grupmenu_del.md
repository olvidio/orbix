---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Quitar un grupo de menús"
flujo: "usuarios.role_grupmenu_del.gestionar.flujo"
preguntas: ["Como quitar un grupo de menús en Quitar un grupo de menús?"]
pantallas_principales: ["usuarios.pantalla.role_form"]
fragmentos: ["usuarios.pantalla.role_form"]
endpoints: ["/src/usuarios/role_grupmenu_del"]
source: "docs/catalogo/usuarios/flujos/role_grupmenu_del.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar un grupo de menús

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar un grupo de menús`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar un grupo de menús en Quitar un grupo de menús?

## Donde Entrar

- Role Form (`usuarios.pantalla.role_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar un grupo de menús

1. En la ficha del rol, elegir el grupo en la tabla.
2. Pulsar quitar y confirmar.
3. Si el registro ya no existe aparece «no existe el registro».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_grupmenu_del`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_form`
- `usuarios.pantalla.role_form`

## Objetivo

Quitar un grupo de menús de un rol.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`
- `debe seleccionar uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
