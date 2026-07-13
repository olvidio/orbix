---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Persistir registro tabla genérica"
flujo: "shared.tablaDB.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver", "shared.pantalla.tablaDB_lista_ver"]
endpoints: ["/src/shared/tablaDB_update"]
source: "docs/catalogo/shared/flujos/tablaDB.md"
estado_revision: "generado"
---

# Ayuda IA - Persistir registro tabla genérica

Usa este documento para responder preguntas de usuario sobre como trabajar con `Persistir registro tabla genérica`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_lista_ver`

## Objetivo

Dar de alta, modificar o eliminar un registro en cualquier tabla mantenida con el patrón `Info*` + repositorio CRUD.

## Errores Documentados

- `no se ha ejecutado la acción`
- `Errores de repositorio y validación de módulos (ver API tablaDB_update).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
