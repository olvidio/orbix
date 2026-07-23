-- Permisos app sobre mapa_prefijo_acta_esquema (si 211100 ya se aplicó sin GRANT).
-- Serie sf. Idempotente.

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE public.mapa_prefijo_acta_esquema TO orbixf;

SELECT public.migracion_aviso('mapa_prefijo_acta_esquema: GRANT a orbixf');
