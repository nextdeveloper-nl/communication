-- PostgreSQL

CREATE TABLE communication_smtp_servers (
    id                        bigint NOT NULL DEFAULT nextval('communication_smtp_servers_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    communication_channel_id  bigint NOT NULL,
    name                      text NOT NULL,
    host                      text NOT NULL,
    port                      integer NOT NULL,
    encryption                text NOT NULL,
    username                  text NOT NULL,
    password                  text NOT NULL,
    from_email                text NOT NULL,
    from_name                 text,
    reply_to                  text,
    is_verified               boolean NOT NULL DEFAULT false,
    verified_at               timestamp with time zone,
    last_checked_at           timestamp with time zone,
    last_check_status         text,
    last_check_message        text,
    iam_account_id            bigint NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                timestamp with time zone,
    CONSTRAINT communication_smtp_servers_encryption_check CHECK ((encryption = ANY (ARRAY['none'::text, 'ssl'::text, 'tls'::text, 'starttls'::text]))),
    CONSTRAINT communication_smtp_servers_channel_fk FOREIGN KEY (communication_channel_id) REFERENCES communication_channels(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_smtp_servers_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_smtp_servers_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_smtp_servers_channel ON public.communication_smtp_servers USING btree (communication_channel_id);
