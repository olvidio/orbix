---
id: "devel_db_admin.verificar_renombrar_esquema.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Verificar Renombrar Esquema"
entidades: ["RenombrarEsquemaVerificacionContexto", "VerificarEstadoRenombrarEsquema"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/verificar_renombrar_esquema"]
pantallas: ["frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\RenombrarEsquemaVerificacionContexto", "src\\devel_db_admin\\application\\VerificarEstadoRenombrarEsquema"]
tags: ["devel_db_admin", "esquema", "renombrar", "verificar", "verificar_renombrar_esquema"]
estado_revision: "generado"
---

# Gestionar Verificar Renombrar Esquema

Propuesta generada automaticamente a partir de endpoints con prefijo comun `verificar_renombrar_esquema`.

## Objetivo Funcional

Gestiona RenombrarEsquemaVerificacionContexto, VerificarEstadoRenombrarEsquema. Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/verificar_renombrar_esquema`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto`
- `src\devel_db_admin\application\VerificarEstadoRenombrarEsquema`

## Pistas Desde Endpoints

- Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
