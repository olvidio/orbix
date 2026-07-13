---
id: "dbextern.sincro_baja"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_baja"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_baja.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."]
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\BajaPersonaUseCase"]
tags: ["dbextern", "sincro", "baja"]
estado_revision: "revisado"
---

# Sincro Baja

Da de baja (situación `B`) una persona Aquinate cuya correspondencia BDU desapareció (punto 8).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Usa dominio `Trasladar` para cambiar situación a `B` con traslado al esquema destino `H-<dl>v|f`.
No elimina el `id_match`.

## Endpoint

- URL: `/src/dbextern/sincro_baja`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_baja.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_orbix` | `integer` | controller | Sí | |
| `tipo_persona` | `string` | controller | Sí | |
| `dl` | `string` | controller | Sí | DL de la ficha (del listado) |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false`, mensaje `_()`.

## Errores conocidos

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Permisos

- HashFront en `ver_desaparecidos_de_listas.phtml`.

## Casos De Uso

- `src\dbextern\application\BajaPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`
