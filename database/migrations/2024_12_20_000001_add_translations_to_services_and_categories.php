<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add translation JSON columns to services and categories tables
     * for multi-language support.
     */
    public function up(): void
    {
        // Add translation columns to services
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'name_translations')) {
                    $table->json('name_translations')->nullable()->after('name');
                }
                if (!Schema::hasColumn('services', 'description_translations')) {
                    $table->json('description_translations')->nullable()->after('description');
                }
            });
        }

        // Add translation columns to categories
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'name_translations')) {
                    $table->json('name_translations')->nullable()->after('name');
                }
                if (!Schema::hasColumn('categories', 'description_translations')) {
                    $table->json('description_translations')->nullable()->after('name_translations');
                }
            });
        }

        // Add translation columns to cities
        if (Schema::hasTable('cities')) {
            Schema::table('cities', function (Blueprint $table) {
                if (!Schema::hasColumn('cities', 'name_translations')) {
                    $table->json('name_translations')->nullable()->after('name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (Schema::hasColumn('services', 'name_translations')) {
                    $table->dropColumn('name_translations');
                }
                if (Schema::hasColumn('services', 'description_translations')) {
                    $table->dropColumn('description_translations');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'name_translations')) {
                    $table->dropColumn('name_translations');
                }
                if (Schema::hasColumn('categories', 'description_translations')) {
                    $table->dropColumn('description_translations');
                }
            });
        }

        if (Schema::hasTable('cities')) {
            Schema::table('cities', function (Blueprint $table) {
                if (Schema::hasColumn('cities', 'name_translations')) {
                    $table->dropColumn('name_translations');
                }
            });
        }
    }
};

