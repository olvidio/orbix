---
id: "procesos.usuario_perm_activ.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Usuario Perm Activ"
entidades: ["UsuarioPermActiv"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/usuario_perm_activ_data"]
pantallas: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivData"]
tags: ["activ", "data", "perm", "procesos", "usuario", "usuario_perm_activ"]
estado_revision: "generado"
---

# Gestionar Usuario Perm Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_perm_activ`.

## Objetivo Funcional

Gestiona UsuarioPermActiv. Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion de permisos de actividad para un usuario). Agrupa la resolucion de repositorios para que el controlador frontend no acceda directamente al contenedor ni a use src\.... El frontend recibe arrays serializables y construye los frontend\shared\web\Desplegable.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/procesos/usuario_perm_activ_data`

## Pantallas Relacionadas

- `frontend/procesos/controller/usuario_perm_activ.php`

## Casos De Uso Detectados

- `src\procesos\application\UsuarioPermActivData`

## Pistas Desde Endpoints

- Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion de permisos de actividad para un usuario). Agrupa la resolucion de repositorios para que el controlador frontend no acceda directamente al contenedor ni a `use src\...`. El frontend recibe arrays serializables y construye los `frontend\shared\web\Desplegable`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
