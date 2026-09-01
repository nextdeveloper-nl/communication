-- PostgreSQL

CREATE TABLE communication_threads (
    id                        bigint NOT NULL DEFAULT nextval('communication_threads_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    subject                   text,
    status                    text NOT NULL DEFAULT 'open'::text,
    communication_channel_id  bigint NOT NULL,
    communication_contact_id  bigint NOT NULL,
    communication_bot_id      bigint,
    assigned_to_user_id       bigint, -- [alias:iam_user_id]
    assigned_at               timestamp with time zone,
    resolved_at               timestamp with time zone,
    last_message_at           timestamp with time zone,
    iam_account_id            bigint NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                timestamp with time zone,
    CONSTRAINT communication_threads_status_check CHECK ((status = ANY (ARRAY['open'::text, 'pending'::text, 'resolved'::text, 'spam'::text, 'closed'::text]))),
    CONSTRAINT communication_threads_bot_fk FOREIGN KEY (communication_bot_id) REFERENCES communication_bots(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_threads_channel_fk FOREIGN KEY (communication_channel_id) REFERENCES communication_channels(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_threads_assigned_user_fk FOREIGN KEY (assigned_to_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_threads_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_threads_contact_fk FOREIGN KEY (communication_contact_id) REFERENCES communication_contacts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_threads_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_threads_account ON public.communication_threads USING btree (iam_account_id);
CREATE INDEX idx_communication_threads_assigned ON public.communication_threads USING btree (assigned_to_user_id) WHERE (assigned_to_user_id IS NOT NULL);
CREATE INDEX idx_communication_threads_channel ON public.communication_threads USING btree (communication_channel_id);
CREATE INDEX idx_communication_threads_contact ON public.communication_threads USING btree (communication_contact_id);
CREATE INDEX idx_communication_threads_last_message ON public.communication_threads USING btree (iam_account_id, last_message_at DESC) WHERE (deleted_at IS NULL);
CREATE INDEX idx_communication_threads_status ON public.communication_threads USING btree (iam_account_id, status) WHERE (deleted_at IS NULL);
