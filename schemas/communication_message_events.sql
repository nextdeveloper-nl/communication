-- PostgreSQL

CREATE TABLE communication_message_events (
    id                        bigint NOT NULL DEFAULT nextval('communication_message_events_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    communication_message_id  bigint NOT NULL,
    event_type                text NOT NULL,
    metadata                  json,
    occurred_at               timestamp with time zone NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT communication_message_events_message_fk FOREIGN KEY (communication_message_id) REFERENCES communication_messages(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_message_events_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_message_events_message ON public.communication_message_events USING btree (communication_message_id);
CREATE INDEX idx_communication_message_events_type ON public.communication_message_events USING btree (event_type, occurred_at DESC);
