<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Allows accounts created through the upcoming Assisted Service to exist
 * without an email address (e.g. UMKM owners without a smartphone/email).
 *
 * The UNIQUE index on `users.email` is intentionally kept: MySQL, SQLite,
 * and PostgreSQL all allow multiple NULL values under a unique index, so
 * existing users and the Self-Service registration flow are unaffected.
 * Authentication remains email-based; this change only makes the column
 * optional for administrator-created accounts.
 *
 * NOTE: SQLite cannot alter a column in place, so Laravel rebuilds the
 * table and drops the enum CHECK constraint on `users.status`. For SQLite
 * the table is therefore rebuilt manually with the constraint preserved.
 */
return new class extends Migration
{
    private const STATUSES = ['pending', 'approved', 'needs_revision', 'rejected', 'suspended'];

    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildUsersTable(emailNullable: true);

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildUsersTable(emailNullable: false);

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }

    private function rebuildUsersTable(bool $emailNullable): void
    {
        $email = $emailNullable ? 'varchar' : 'varchar not null';
        $enum = implode(', ', array_map(
            fn (string $status): string => "'".$status."'",
            self::STATUSES
        ));

        DB::transaction(function () use ($email, $enum): void {
            // The parent table is never renamed: SQLite would rewrite foreign
            // key clauses in other tables (e.g. `umkms.user_id`) to point at
            // the temporary name and break them once it is dropped.
            DB::statement(
                'CREATE TABLE "users_new" ("id" integer primary key autoincrement not null, '
                .'"name" varchar not null, "email" '.$email.', "email_verified_at" datetime, '
                .'"password" varchar not null, "remember_token" varchar, "created_at" datetime, '
                .'"updated_at" datetime, "phone" varchar, '
                .'"status" varchar check ("status" in ('.$enum.')) not null default \'pending\')'
            );
            DB::statement(
                'INSERT INTO "users_new" ("id", "name", "email", "email_verified_at", "password", '
                .'"remember_token", "created_at", "updated_at", "phone", "status") '
                .'SELECT "id", "name", "email", "email_verified_at", "password", '
                .'"remember_token", "created_at", "updated_at", "phone", "status" FROM "users"'
            );
            DB::statement('DROP TABLE "users"');
            DB::statement('ALTER TABLE "users_new" RENAME TO "users"');
            DB::statement('CREATE UNIQUE INDEX "users_email_unique" ON "users" ("email")');
        });
    }
};