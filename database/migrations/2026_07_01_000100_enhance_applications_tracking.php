<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'source')) {
                $table->string('source')->default('formulaire_complet')->after('status');
            }

            if (! Schema::hasColumn('applications', 'priority')) {
                $table->string('priority')->default('normale')->after('source');
            }

            if (! Schema::hasColumn('applications', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('priority')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('applications', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('applications', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by');
            }

            if (! Schema::hasColumn('applications', 'last_contacted_at')) {
                $table->timestamp('last_contacted_at')->nullable()->after('processed_at');
            }

            if (! Schema::hasColumn('applications', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('last_contacted_at');
            }
        });

        if (! Schema::hasTable('application_comments')) {
            Schema::create('application_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('body');
                $table->boolean('is_important')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('application_activities')) {
            Schema::create('application_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->text('description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('application_activities');
        Schema::dropIfExists('application_comments');

        Schema::table('applications', function (Blueprint $table) {
            foreach (['processed_by', 'assigned_to'] as $column) {
                if (Schema::hasColumn('applications', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach (['next_follow_up_at', 'last_contacted_at', 'processed_at', 'priority', 'source'] as $column) {
                if (Schema::hasColumn('applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
