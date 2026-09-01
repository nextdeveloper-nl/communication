-- PostgreSQL

CREATE TABLE communication_contact_identifiers (
    id                        bigint NOT NULL DEFAULT nextval('communication_contact_identifiers_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    communication_contact_id  bigint NOT NULL,
    channel_type              text NOT NULL,
    identifier                text NOT NULL,
    is_primary                boolean NOT NULL DEFAULT false,
    is_suppressed             boolean NOT NULL DEFAULT false,
    suppressed_at             timestamp with time zone,
    suppressed_reason         text,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                timestamp with time zone,
    CONSTRAINT communication_contact_identifiers_contact_fk FOREIGN KEY (communication_contact_id) REFERENCES communication_contacts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_contact_identifiers_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_contact_identifiers_active ON public.communication_contact_identifiers USING btree (communication_contact_id) WHERE ((is_suppressed = false) AND (deleted_at IS NULL));
CREATE INDEX idx_communication_contact_identifiers_contact ON public.communication_contact_identifiers USING btree (communication_contact_id);
CREATE INDEX idx_communication_contact_identifiers_identifier ON public.communication_contact_identifiers USING btree (identifier);
