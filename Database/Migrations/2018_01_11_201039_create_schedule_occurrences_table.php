<?php
/**
 * *
 *  * This file is part of Schedule Addon for InvoiceNinja.
 *  * (c) Cytech <cytech@cytech-eng.com>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *  
 *
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleOccurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('schedule_occurrences'), function (Blueprint $table) {
	        $table->increments('oid');
	        $table->integer('schedule_id')->length(10)->unsigned();
	        $table->dateTime('start_date')->nullable();
	        $table->dateTime('end_date')->nullable();

	        $table->timestamps();

	        $table->index('schedule_id', 'schedule_occurrence_schedule_id_foreign');
		    $table->foreign('schedule_id')->references('id')->on('schedule')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(strtolower('schedule_occurrences'));
    }
}
