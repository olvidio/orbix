---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Perm Menu"
flujo: "usuarios.perm_menu.gestionar.flujo"
preguntas: ["Como eliminar en Perm Menu?", "Como guardar en Perm Menu?", "Como consultar el listado en Perm Menu?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_form", "usuarios.pantalla.perm_menu_form"]
endpoints: ["/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_lista"]
source: "docs/catalogo/usuarios/flujos/perm_menu.md"
estado_revision: "generado"
---

# Ayuda IA - Perm Menu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Perm Menu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Perm Menu?
- Como guardar en Perm Menu?
- Como consultar el listado en Perm Menu?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_menu_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/perm_menu_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.grupo_form`
- `usuarios.pantalla.perm_menu_form`

## Objetivo

Gestión permisos menú DL de un usuario desde su ficha.

## Errores Documentados

- `no existe el registro`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
