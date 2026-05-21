---
id: "procesos.usuario_perm_activ_ajax.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Usuario Perm Activ Ajax"
entidades: ["UsuarioPermActivFases"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/usuario_perm_activ_ajax"]
pantallas: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivFases"]
tags: ["activ", "ajax", "perm", "procesos", "usuario", "usuario_perm_activ_ajax"]
estado_revision: "generado"
---

# Gestionar Usuario Perm Activ Ajax

Propuesta generada automaticamente a partir de endpoints con prefijo comun `usuario_perm_activ_ajax`.

## Objetivo Funcional

Gestiona UsuarioPermActivFases. Caso de uso: devuelve las opciones disponibles para el desplegable fase_ref[] de la pantalla usuario_perm_activ, filtradas por el tipo de actividad y la delegacion.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/usuario_perm_activ_ajax`

## Pantallas Relacionadas

- `frontend/procesos/controller/usuario_perm_activ.php`

## Casos De Uso Detectados

- `src\procesos\application\UsuarioPermActivFases`

## Pistas Desde Endpoints

- Caso de uso: devuelve las opciones disponibles para el desplegable `fase_ref[]` de la pantalla usuario_perm_activ, filtradas por el tipo de actividad y la delegacion.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
