-- PostgreSQL

CREATE TABLE communication_available_channels (
    id              integer NOT NULL DEFAULT nextval('communication_available_channels_id_seq'::regclass),
    uuid            uuid DEFAULT gen_random_uuid(),
    name            text NOT NULL,
    class           text NOT NULL,
    parameters      json,
    iam_user_id     bigint,
    iam_account_id  bigint,
    created_at      timestamp with time zone DEFAULT now(),
    updated_at      timestamp with time zone DEFAULT now(),
    deleted_at      timestamp with time zone,
    config          json NOT NULL,
    CONSTRAINT communication_available_channels_pkey PRIMARY KEY (id)
);
