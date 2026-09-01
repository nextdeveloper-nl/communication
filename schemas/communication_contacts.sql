-- PostgreSQL

CREATE TABLE communication_contacts (
    id              bigint NOT NULL DEFAULT nextval('communication_contacts_id_seq'::regclass),
    uuid            uuid DEFAULT gen_random_uuid(),
    full_name       text,
    notes           text,
    tags            text[],
    iam_account_id  bigint NOT NULL,
    created_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at      timestamp with time zone,
    CONSTRAINT communication_contacts_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_contacts_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_contacts_account ON public.communication_contacts USING btree (iam_account_id);
