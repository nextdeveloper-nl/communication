-- PostgreSQL

CREATE TABLE communication_unsubscribes (
    id                        bigint NOT NULL DEFAULT nextval('communication_unsubscribes_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    communication_contact_id  bigint NOT NULL,
    channel_type              text NOT NULL,
    identifier                text NOT NULL,
    reason                    text,
    source                    text,
    iam_account_id            bigint NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT communication_unsubscribes_contact_fk FOREIGN KEY (communication_contact_id) REFERENCES communication_contacts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_unsubscribes_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_unsubscribes_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_unsubscribes_contact ON public.communication_unsubscribes USING btree (communication_contact_id);
CREATE INDEX idx_communication_unsubscribes_identifier ON public.communication_unsubscribes USING btree (identifier);
