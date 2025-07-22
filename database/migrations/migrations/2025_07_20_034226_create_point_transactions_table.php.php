<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePointTransactionsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();

                // Consumer reference with explicit foreign key
                $table->unsignedBigInteger('consumer_id');
                $table->foreign('consumer_id')
                      ->references('id')
                      ->on('consumers')
                      ->onDelete('cascade');

                // Seller reference with explicit foreign key
                $table->unsignedBigInteger('seller_id');
                $table->foreign('seller_id')
                      ->references('id')
                      ->on('sellers')
                      ->onDelete('cascade');

                // QR Code reference with explicit foreign key
                $table->unsignedBigInteger('qr_code_id');
                $table->foreign('qr_code_id')
                      ->references('id')
                      ->on('qr_codes')
                      ->onDelete('cascade');

                // Transaction data
                $table->integer('units_scanned');
                $table->integer('points');
                $table->enum('type', ['earn', 'spend'])->default('earn');
                $table->string('description')->nullable();
                $table->timestamp('scanned_at')->useCurrent();
                $table->timestamps();
            });

            // Create trigger to always add points to seller
            DB::unprepared('DROP TRIGGER IF EXISTS `trg_point_tx_after_insert`;');
            DB::unprepared(<<<SQL
CREATE TRIGGER `trg_point_tx_after_insert`
AFTER INSERT ON `point_transactions`
FOR EACH ROW
BEGIN
    IF NEW.seller_id IS NOT NULL THEN
        UPDATE `sellers`
        SET `total_points` = `total_points` + NEW.points
        WHERE `id` = NEW.seller_id;
    END IF;
END;
SQL
            );
        }
    }

    public function down(): void
    {
        // Remove trigger then drop table
        DB::unprepared('DROP TRIGGER IF EXISTS `trg_point_tx_after_insert`;');
        Schema::dropIfExists('point_transactions');
    }
}
