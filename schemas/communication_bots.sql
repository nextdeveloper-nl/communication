-- PostgreSQL

CREATE TABLE communication_bots (
    id              bigint NOT NULL DEFAULT nextval('communication_bots_id_seq'::regclass),
    uuid            uuid DEFAULT gen_random_uuid(),
    name            text NOT NULL,
    description     text,
    is_active       boolean NOT NULL DEFAULT true,
    iam_account_id  bigint,
    created_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at      timestamp with time zone,
    CONSTRAINT communication_bots_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_bots_pkey PRIMARY KEY (id)
);
