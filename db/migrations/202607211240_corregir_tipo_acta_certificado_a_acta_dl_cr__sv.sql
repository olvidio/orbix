-- Corregir notas mal marcadas como certificado (tipo_acta = 2) que en realidad
-- son actas (tipo_acta = 1): el nº de acta empieza por «dl…» o «cr…»
-- (p. ej. «dlb 12/18», «crnig 3/20»).
--
-- Tras este cambio, 211250 ya no las trata como certificado y 211300/222000
-- las repatrián / mueven al e_notas_dl del mapa de prefijos.
--
-- Afecta a todas las hijas de publicv.e_notas (e_notas_dl y
-- e_notas_otra_region_stgr). No toca placeholders (id_situacion = 13).
-- Idempotente.
--
-- Orden: después de 211140/211150, antes de 211250/211300.
-- Serie sv. Ver docs/dev/notas_modelo_acta.md

DO $$
DECLARE
    n_upd bigint;
BEGIN
    UPDATE publicv.e_notas AS n
    SET tipo_acta = 1
    WHERE COALESCE(n.tipo_acta, 1) = 2
      AND n.id_situacion IS DISTINCT FROM 13
      AND lower(trim(split_part(trim(coalesce(n.acta, '')), ' ', 1))) ~ '^(dl|cr)';

    GET DIAGNOSTICS n_upd = ROW_COUNT;

    PERFORM public.migracion_aviso(format(
        'corregir tipo_acta 2→1 (prefijo dl/cr) sv: actualizadas=%s',
        n_upd
    ));
END $$;
