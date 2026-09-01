-- PostgreSQL

CREATE TABLE communication_notifications (
    id              bigint NOT NULL DEFAULT nextval('communication_notifications_id_seq'::regclass),
    uuid            uuid DEFAULT gen_random_uuid(),
    severity        text NOT NULL DEFAULT 'info'::text,
    object_id       bigint NOT NULL,
    object_type     text NOT NULL,
    data            text NOT NULL,
    read_at         timestamp with time zone,
    iam_user_id     bigint NOT NULL,
    iam_account_id  bigint NOT NULL,
    created_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at      timestamp with time zone,
    CONSTRAINT communication_notifications_severity_check CHECK ((severity = ANY (ARRAY['info'::text, 'warning'::text, 'error'::text]))),
    CONSTRAINT communication_notifications_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_notifications_user_fk FOREIGN KEY (iam_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_notifications_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_notifications_unread ON public.communication_notifications USING btree (iam_user_id) WHERE (read_at IS NULL);
CREATE INDEX idx_communication_notifications_user ON public.communication_notifications USING btree (iam_user_id);
