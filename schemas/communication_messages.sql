-- PostgreSQL

CREATE TABLE communication_messages (
    id                        bigint NOT NULL DEFAULT nextval('communication_messages_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    communication_thread_id   bigint,
    crm_campaign_id           bigint,
    direction                 smallint NOT NULL, -- [label:"0: inbound (contact to team/bot), 1: outbound (team/bot to contact)"]
    content_type              text NOT NULL DEFAULT 'text'::text,
    body                      text NOT NULL, -- [ui:html]
    attachments               text[],
    sent_by_user_id           bigint, -- [alias:iam_user_id]
    sent_by_bot_id            bigint, -- [alias:communication_bot_id]
    reply_to_id               bigint, -- [!model]
    external_message_id       text, -- [!model]
    status                    text NOT NULL DEFAULT 'queued'::text,
    deliver_at                timestamp with time zone,
    delivered_at              timestamp with time zone,
    read_at                   timestamp with time zone,
    failed_at                 timestamp with time zone,
    failure_reason            text,
    is_internal               boolean NOT NULL DEFAULT false, -- [label:"Internal team note — never sent to the contact"]
    metadata                  json,
    iam_account_id            bigint NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                timestamp with time zone,
    communication_channel_id  bigint,
    recipient                 text,
    CONSTRAINT communication_messages_content_type_check CHECK ((content_type = ANY (ARRAY['text'::text, 'html'::text, 'image'::text, 'file'::text, 'audio'::text, 'video'::text]))),
    CONSTRAINT communication_messages_status_check CHECK ((status = ANY (ARRAY['queued'::text, 'sent'::text, 'delivered'::text, 'read'::text, 'failed'::text, 'bounced'::text]))),
    CONSTRAINT message_belongs_to_one CHECK ((NOT ((communication_thread_id IS NOT NULL) AND (crm_campaign_id IS NOT NULL)))),
    CONSTRAINT communication_messages_campaign_fk FOREIGN KEY (crm_campaign_id) REFERENCES crm_campaigns(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_sent_by_user_fk FOREIGN KEY (sent_by_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_thread_fk FOREIGN KEY (communication_thread_id) REFERENCES communication_threads(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_sent_by_bot_fk FOREIGN KEY (sent_by_bot_id) REFERENCES communication_bots(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_reply_to_fk FOREIGN KEY (reply_to_id) REFERENCES communication_messages(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_messages_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_messages_campaign ON public.communication_messages USING btree (crm_campaign_id) WHERE (crm_campaign_id IS NOT NULL);
CREATE INDEX idx_communication_messages_scheduled ON public.communication_messages USING btree (deliver_at) WHERE ((deliver_at IS NOT NULL) AND (status = 'queued'::text));
CREATE INDEX idx_communication_messages_status ON public.communication_messages USING btree (status);
CREATE INDEX idx_communication_messages_thread ON public.communication_messages USING btree (communication_thread_id) WHERE (communication_thread_id IS NOT NULL);
