---
id: "cambios.usuario_avisos_pref.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Usuario Avisos Pref"
entidades: ["UsuarioAvisosPref"]
acciones: ["ver_formulario"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data"]
pantallas: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\UsuarioAvisosPrefFormData"]
tags: ["avisos", "cambios", "data", "form", "pref", "usuario", "usuario_avisos_pref"]
estado_revision: "generado"
---

# Gestionar Usuario Avisos Pref

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_avisos_pref`.

## Objetivo Funcional

Gestiona UsuarioAvisosPref. Endpoint JSON que devuelve la informacion necesaria para pintar el formulario usuario_avisos_pref (edicion de un aviso de usuario/grupo).

## Acciones Detectadas

- `ver_formulario`

## Endpoints

- `/src/cambios/usuario_avisos_pref_form_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/usuario_avisos_pref.php`

## Casos De Uso Detectados

- `src\cambios\application\UsuarioAvisosPrefFormData`

## Pistas Desde Endpoints

- Endpoint JSON que devuelve la informacion necesaria para pintar el formulario `usuario_avisos_pref` (edicion de un aviso de usuario/grupo).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
