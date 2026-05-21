---
id: "cambios.usuario_form_avisos.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Usuario Form Avisos"
entidades: ["UsuarioFormAvisos"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/usuario_form_avisos_data"]
pantallas: ["frontend/cambios/controller/usuario_form_avisos.php"]
casos_uso: ["src\\cambios\\application\\UsuarioFormAvisosData"]
tags: ["avisos", "cambios", "data", "form", "usuario", "usuario_form_avisos"]
estado_revision: "generado"
---

# Gestionar Usuario Form Avisos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_form_avisos`.

## Objetivo Funcional

Gestiona UsuarioFormAvisos. Datos para el listado de avisos de un usuario.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cambios/usuario_form_avisos_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/usuario_form_avisos.php`

## Casos De Uso Detectados

- `src\cambios\application\UsuarioFormAvisosData`

## Pistas Desde Endpoints

- Endpoint backend: datos para el listado de avisos de un usuario.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
