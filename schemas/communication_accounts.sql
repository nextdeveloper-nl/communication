-- PostgreSQL

CREATE TABLE communication_accounts (
    id                       bigint NOT NULL DEFAULT nextval('communication_accounts_id_seq'::regclass),
    uuid                     uuid DEFAULT gen_random_uuid(),
    iam_account_id           bigint NOT NULL,
    plan                     text NOT NULL DEFAULT 'free'::text,
    max_contacts             integer,
    max_emails_per_month     integer,
    max_sms_per_month        integer,
    max_channels             integer,
    emails_sent_this_period  integer NOT NULL DEFAULT 0,
    sms_sent_this_period     integer NOT NULL DEFAULT 0,
    current_period_start     timestamp with time zone,
    current_period_end       timestamp with time zone,
    is_suspended             boolean NOT NULL DEFAULT false,
    suspension_reason        text,
    reputation_score         numeric(5,2),
    enabled_channel_types    text[],
    is_ai_bots_enabled       boolean NOT NULL DEFAULT false,
    is_dedicated_ip_enabled  boolean NOT NULL DEFAULT false,
    created_at               timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at               timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at               timestamp with time zone,
    CONSTRAINT communication_accounts_plan_check CHECK ((plan = ANY (ARRAY['free'::text, 'starter'::text, 'pro'::text, 'enterprise'::text]))),
    CONSTRAINT communication_accounts_iam_account_fk FOREIGN KEY (iam_account_id) REFERENCES iam_accounts(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_accounts_pkey PRIMARY KEY (id),
    CONSTRAINT communication_accounts_iam_account_id_key UNIQUE (iam_account_id)
);

CREATE INDEX idx_communication_accounts_iam_account ON public.communication_accounts USING btree (iam_account_id);
