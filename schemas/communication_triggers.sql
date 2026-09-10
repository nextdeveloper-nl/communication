-- PostgreSQL
-- TRIGGERS (introspected live from leo_v4 via pg_trigger/pg_proc; no triggers
-- are defined directly on any communication_* table. This trigger lives on
-- iam_accounts and seeds communication_accounts on account creation,
-- mirroring the equivalent pattern used by the Accounting/IAAS/CRM modules.)

create or replace function fn_create_communication_account()
    returns trigger
    language plpgsql
as
$function$
begin
    insert into communication_accounts (iam_account_id,
                                        plan,
                                        current_period_start,
                                        current_period_end)
    values (new.id,
            'free',
            now(),
            now() + interval '1 month');
    return new;
end;
$function$;

create trigger trg_create_communication_account
    after insert
    on iam_accounts
    for each row
execute function fn_create_communication_account();
