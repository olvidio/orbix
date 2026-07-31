---
id: "actividadestudios.e43_imprimir_mpdf_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/e43_imprimir_mpdf_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/e43_imprimir_mpdf_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_E43CertificadoDataData"
respuesta_data: ["msg_err:string", "nom:string", "txt_nacimiento:string", "dl_origen:string", "dl_destino:string", "txt_actividad:string", "matriculas:integer", "aAsignaturasMatriculadas:list<array{nom_asignatura: mixed, nota: string, f_acta: string, acta: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/e43_imprimir_mpdf.php"]
casos_uso: ["src\\actividadestudios\\application\\E43CertificadoData"]
tags: ["actividadestudios", "e43", "imprimir", "mpdf", "data"]
estado_revision: "revisado"
---

# E43 Imprimir Mpdf Data

Datos del certificado E43 orientados a la impresión mPDF: mismos datos que `e43_data`, con
`append_blank_footer` forzado a `true` en el controller (fila en blanco para el pie del PDF).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Igual que `E43CertificadoData` / `e43_data`: persona, nacimiento, DL origen/destino, actividad y
asignaturas matriculadas con nota/acta. El controller fija siempre `append_blank_footer => true`
(no lee el POST para ese flag).

## Endpoint

- URL: `/src/actividadestudios/e43_imprimir_mpdf_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/e43_imprimir_mpdf_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | Persona destinataria |
| `id_activ` | `integer` | controller | No | Actividad del certificado |

El controller extrae `id_nom` e `id_activ` de `$_POST` y llama a
`execute(['id_nom' => …, 'id_activ' => …, 'append_blank_footer' => true])`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_E43CertificadoDataData`):
  - `msg_err` (`string`): avisos (persona/actividad no encontrada, sin matrículas).
  - `nom` (`string`), `txt_nacimiento` (`string`).
  - `dl_origen` (`string`), `dl_destino` (`string`).
  - `txt_actividad` (`string`): `lugar, f_ini-f_fin`.
  - `matriculas` (`integer`).
  - `aAsignaturasMatriculadas` (`list`): `{nom_asignatura, nota, f_acta, acta}` más fila en blanco
    final por el footer.

## Errores conocidos

- Excepciones del caso de uso → `mensaje` del envelope (controller catch).
- Avisos no bloqueantes en `msg_err`: persona no encontrada, actividad no encontrada, sin matrículas.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`e43_imprimir_mpdf.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\E43CertificadoData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/e43_imprimir_mpdf.php`
