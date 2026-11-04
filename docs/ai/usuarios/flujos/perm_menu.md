---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Añadir un permiso de menú"
flujo: "usuarios.perm_menu.gestionar.flujo"
preguntas: ["Como añadir un permiso de menú en Añadir un permiso de menú?", "Como quitar un permiso de menú en Añadir un permiso de menú?"]
pantallas_principales: ["usuarios.pantalla.perm_menu_form"]
fragmentos: ["usuarios.pantalla.grupo_form", "usuarios.pantalla.perm_menu_form"]
endpoints: ["/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_lista"]
source: "docs/catalogo/usuarios/flujos/perm_menu.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir un permiso de menú

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir un permiso de menú`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir un permiso de menú en Añadir un permiso de menú?
- Como quitar un permiso de menú en Añadir un permiso de menú?

## Donde Entrar

- Perm Menu Form (`usuarios.pantalla.perm_menu_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir un permiso de menú

1. Abrir la ficha del usuario.
2. Pulsar **añadir grup menu** y elegir el grupo de menús.
3. Guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

## Quitar un permiso de menú

1. Elegir la fila en la tabla de la ficha y pulsar quitar.
2. Confirmar «¿Esta seguro que desea borrar este permiso?».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.perm_menu_form`
- `usuarios.pantalla.grupo_form`
- `usuarios.pantalla.perm_menu_form`

## Objetivo

Ver, añadir y quitar los permisos sobre menús de un usuario desde su ficha.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
