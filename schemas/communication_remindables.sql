-- PostgreSQL

CREATE TABLE communication_remindables (
    id               bigint NOT NULL DEFAULT nextval('communication_remindables_id_seq'::regclass),
    uuid             uuid DEFAULT gen_random_uuid(),
    object_id        bigint NOT NULL,
    object_type      text NOT NULL,
    remind_datetime  timestamp with time zone,
    snooze_datetime  timestamp with time zone,
    note             text,
    is_reminded      boolean NOT NULL DEFAULT false,
    is_acknowledged  boolean NOT NULL DEFAULT false,
    is_cancelled     boolean NOT NULL DEFAULT false,
    iam_user_id      bigint,
    iam_account_id   bigint NOT NULL,
    created_at       timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at       timestamp with time zone,
    CONSTRAINT communication_remindables_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_remindables_user_fk FOREIGN KEY (iam_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_remindables_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_remindables_pending ON public.communication_remindables USING btree (remind_datetime) WHERE ((is_reminded = false) AND (is_cancelled = false));
CREATE INDEX idx_communication_remindables_user ON public.communication_remindables USING btree (iam_user_id);
