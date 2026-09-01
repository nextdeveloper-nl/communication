-- PostgreSQL

CREATE TABLE communication_thread_assignments (
    id                       bigint NOT NULL DEFAULT nextval('communication_thread_assignments_id_seq'::regclass),
    uuid                     uuid DEFAULT gen_random_uuid(),
    communication_thread_id  bigint NOT NULL,
    assigned_to_user_id      bigint, -- [alias:iam_user_id]
    assigned_by_user_id      bigint, -- [alias:iam_user_id]
    created_at               timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT communication_thread_assignments_assigned_by_fk FOREIGN KEY (assigned_by_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_thread_assignments_assigned_to_fk FOREIGN KEY (assigned_to_user_id) REFERENCES iam_users(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_thread_assignments_thread_fk FOREIGN KEY (communication_thread_id) REFERENCES communication_threads(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT communication_thread_assignments_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_communication_thread_assignments_thread ON public.communication_thread_assignments USING btree (communication_thread_id);
