---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Añadir grupos de menús a un rol"
flujo: "usuarios.role_grupmenu_add.gestionar.flujo"
preguntas: ["Como añadir grupos de menús a un rol en Añadir grupos de menús a un rol?"]
pantallas_principales: ["usuarios.pantalla.role_grupmenu"]
fragmentos: ["usuarios.pantalla.role_grupmenu"]
endpoints: ["/src/usuarios/role_grupmenu_add"]
source: "docs/catalogo/usuarios/flujos/role_grupmenu_add.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir grupos de menús a un rol

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir grupos de menús a un rol`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir grupos de menús a un rol en Añadir grupos de menús a un rol?

## Donde Entrar

- Role Grupmenu (`usuarios.pantalla.role_grupmenu`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir grupos de menús a un rol

1. Pulsar **añadir grup menu** en la ficha del rol.
2. Marcar uno o varios grupos de la lista.
3. Guardar. Si no se marca ninguno aparece «debe seleccionar uno».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/role_grupmenu_add`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.role_grupmenu`
- `usuarios.pantalla.role_grupmenu`

## Objetivo

Añadir a un rol los grupos de menús marcados.

## Errores Documentados

- `hay un error, no se ha guardado`
- `debe seleccionar uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
