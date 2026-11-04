---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Modificar un encargo"
flujo: "encargossacd.encargo_ver.gestionar.flujo"
preguntas: ["Como modificar un encargo en Modificar un encargo?", "Como crear un encargo nuevo en Modificar un encargo?", "Como eliminar un encargo en Modificar un encargo?"]
pantallas_principales: ["encargossacd.pantalla.encargo_ver"]
fragmentos: ["encargossacd.pantalla.encargo_select", "encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_eliminar", "/src/encargossacd/encargo_ver_nuevo"]
source: "docs/catalogo/encargossacd/flujos/encargo_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar un encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar un encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar un encargo en Modificar un encargo?
- Como crear un encargo nuevo en Modificar un encargo?
- Como eliminar un encargo en Modificar un encargo?

## Donde Entrar

- Encargo Ver (`encargossacd.pantalla.encargo_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar un encargo

1. Abrir el encargo desde **ver encargo**.
2. Cambiar el **grupo**, el **tipo encargo**, el **centro** o la **zona**, el **lugar**, la **descripción** y el **idioma**.
3. Pulsar **modificar encargo**.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Crear un encargo nuevo

1. En **ver encargo** pulsar **añadir encargo**.
2. Elegir el **tipo de encargo** («escoger el tipo de encargo»), el centro o zona y la descripción.
3. Pulsar **nuevo encargo** (o **generar**).

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Eliminar un encargo

1. En la tabla de encargos, marcar el encargo y eliminarlo; confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_ver`
- `encargossacd.pantalla.encargo_select`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Crear, ver, modificar o eliminar un encargo (atención de un centro, zona, etc.).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
