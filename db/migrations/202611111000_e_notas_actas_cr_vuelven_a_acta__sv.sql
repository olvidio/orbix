-- Corrige 202611101300_e_notas_regiones_sin_esquema_a_certificado: pasó a
-- certificado (tipo_acta 2) las notas con acta de crAfom, crB, crCam y crCoe.
-- Un acta con sigla «crX» es un acta: vuelven a tipo_acta 1.
-- Límite: las que ya eran certificado antes de aquella migración no se pueden
-- distinguir; con la regla anterior también deben ser acta.
-- «cg» es siempre certificado (tipo_acta 2); se asegura aquí por si la 1300
-- no llegó a aplicarse en esta base de datos.
-- «crs+» es la excepción entre las «crX»: también es siempre certificado.
-- Solo cambia tipo_acta; el texto del acta no se toca.
-- Sobre la tabla padre: corrige todas las hijas. Idempotente.

WITH a_acta AS (
    UPDATE publicv.e_notas
    SET tipo_acta = 1
    WHERE tipo_acta = 2
      AND lower(split_part(btrim(acta), ' ', 1)) ~ '^(crafom|crb|crcam|crcoe)f?$'
    RETURNING 1
),
a_certificado AS (
    UPDATE publicv.e_notas
    SET tipo_acta = 2
    WHERE tipo_acta IS DISTINCT FROM 2
      AND lower(split_part(btrim(acta), ' ', 1)) ~ '^(cg|crs\+)f?$'
    RETURNING 1
)
SELECT public.migracion_aviso(format(
    'e_notas: %s notas crAfom/crB/crCam/crCoe devueltas a acta; %s notas cg/crs+ pasadas a certificado',
    (SELECT count(*) FROM a_acta),
    (SELECT count(*) FROM a_certificado)
));
