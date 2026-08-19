<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // GP - 19-08-2026 code comment - Up migration for PriceWatch core schema
        
        // Workspaces table
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('timezone')->default('UTC');
            
            // Laravel Cashier fields for Workspace billable
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            
            $table->timestamps();
        });

        // Workspace User pivots
        Schema::create('workspace_user', function (Blueprint $table) {
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->default('member'); // owner, admin, member, viewer
            $table->timestamps();
            $table->primary(['workspace_id', 'user_id']);
        });

        // Competitors table
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('website_url');
            $table->string('pricing_url');
            $table->string('logo_url')->nullable();
            $table->string('status')->default('active'); // active, paused, error
            $table->string('check_frequency')->default('daily'); // hourly, every_6_hours, daily, weekly
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('next_check_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['workspace_id', 'status']);
            $table->index(['next_check_at', 'status']);
            $table->unique(['workspace_id', 'pricing_url']);
        });

        // Pricing snapshots
        Schema::create('pricing_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained('competitors')->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('content_hash');
            $table->text('raw_content')->nullable();
            $table->jsonb('normalized_data'); // List of plans extracted
            $table->integer('http_status');
            $table->timestamp('captured_at');
            $table->timestamp('created_at')->useCurrent();
        });

        // Pricing plans
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained('competitors')->onDelete('cascade');
            $table->foreignId('snapshot_id')->nullable()->constrained('pricing_snapshots')->onDelete('set null');
            $table->string('name');
            $table->string('slug');
            $table->decimal('monthly_price', 10, 2)->nullable();
            $table->decimal('annual_price', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->string('billing_period')->default('monthly'); // monthly, annual, custom
            $table->text('description')->nullable();
            $table->integer('user_limit')->nullable();
            $table->jsonb('features')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Price changes
        Schema::create('price_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->foreignId('competitor_id')->constrained('competitors')->onDelete('cascade');
            $table->foreignId('snapshot_id')->constrained('pricing_snapshots')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('pricing_plans')->onDelete('cascade');
            $table->string('change_type'); // price_increased, price_decreased, plan_added, plan_removed, feature_changed, limit_changed, billing_changed, unknown
            $table->string('field');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->decimal('percentage_change', 8, 2)->nullable();
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->timestamp('detected_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Alert rules
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->jsonb('change_types'); // List of active trigger events
            $table->decimal('minimum_percentage_change', 5, 2)->default(0.00);
            $table->jsonb('competitor_ids')->nullable(); // Scope to specific competitors if not null
            $table->jsonb('channels'); // [email, slack, webhook]
            $table->timestamps();
        });

        // Alert destinations
        Schema::create('alert_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('type'); // email, slack, webhook
            $table->string('name');
            $table->text('configuration'); // Encrypted JSON configuration
            $table->boolean('enabled')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Notification logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->foreignId('price_change_id')->nullable()->constrained('price_changes')->onDelete('cascade');
            $table->string('channel');
            $table->string('status')->default('sent'); // sent, failed
            $table->string('recipient');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Audit logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->string('resource');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Cashier Subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained('workspaces')->onDelete('cascade');
            $table->string('name');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'stripe_status']);
        });

        // Cashier Subscription Items table
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->string('stripe_id')->unique();
            $table->string('stripe_product');
            $table->string('stripe_price');
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'stripe_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // GP - 19-08-2026 code comment - Down migration for PriceWatch core schema
        
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('alert_destinations');
        Schema::dropIfExists('alert_rules');
        Schema::dropIfExists('price_changes');
        Schema::dropIfExists('pricing_plans');
        Schema::dropIfExists('pricing_snapshots');
        Schema::dropIfExists('competitors');
        Schema::dropIfExists('workspace_user');
        Schema::dropIfExists('workspaces');
    }
};
