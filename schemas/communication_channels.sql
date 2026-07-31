-- PostgreSQL

CREATE TABLE communication_channels (
    id              bigint NOT NULL DEFAULT nextval('communication_channels_id_seq'::regclass),
    uuid            uuid DEFAULT gen_random_uuid(),
    name            text NOT NULL,
    type            text NOT NULL,
    configuration   json NOT NULL,
    credentials     json,
    is_active       boolean NOT NULL DEFAULT true,
    priority        integer NOT NULL DEFAULT 0,
    iam_account_id  bigint NOT NULL,
    created_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at      timestamp with time zone,
    CONSTRAINT communication_channels_type_check CHECK ((type = ANY (ARRAY['mattermost'::text, 'gmail'::text, 'pop3'::text, 'imap'::text, 'smtp'::text, 'sms'::text, 'google_workspace'::text, 'email'::text, 'whatsapp'::text, 'telegram'::text, 'slack'::text, 'internal'::text]))),
    CONSTRAINT communication_channels_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_channels_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_channels_account ON public.communication_channels USING btree (iam_account_id);
CREATE INDEX idx_communication_channels_type ON public.communication_channels USING btree (type);
