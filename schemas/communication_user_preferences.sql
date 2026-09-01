-- PostgreSQL

CREATE TABLE communication_user_preferences (
    id                         bigint NOT NULL DEFAULT nextval('communication_user_preferences_id_seq'::regclass),
    uuid                       uuid DEFAULT gen_random_uuid(),
    is_system_email_optout     boolean DEFAULT false,
    is_phone_optout            boolean DEFAULT false,
    is_marketing_email_optout  boolean DEFAULT false,
    iam_user_id                bigint,
    created_at                 timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                 timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                 timestamp with time zone,
    CONSTRAINT communication_user_preferences_user_fk FOREIGN KEY (iam_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_user_preferences_pkey PRIMARY KEY (id)
);
