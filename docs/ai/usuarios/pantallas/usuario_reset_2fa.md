---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Usuario Reset 2fa"
pantalla: "usuarios.pantalla.usuario_reset_2fa"
preguntas: ["Que se puede hacer en Usuario Reset 2fa?", "Que campos tiene Usuario Reset 2fa?", "Que acciones hay en Usuario Reset 2fa?"]
capacidades: ["usuarios.usuario_2fa.gestionar"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
source: "docs/catalogo/usuarios/pantallas/usuario_reset_2fa.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Reset 2fa

## Resumen

Fragmento admin para resetear 2FA de un usuario.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_usuario`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `usuarios.usuario_2fa.gestionar`

## Endpoints Relacionados

- `/src/usuarios/usuario_2fa_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
