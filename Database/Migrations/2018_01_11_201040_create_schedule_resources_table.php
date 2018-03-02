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

class CreateScheduleResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('schedule_resources'), function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('schedule_id')->length(10)->unsigned();
	        $table->integer('fid');
	        $table->string('resource_table',45)->nullable();
	        $table->integer('resource_id')->nullable();
	        $table->string('value')->nullable();
	        $table->integer('qty')->nullable();
	        $table->index('schedule_id', 'schedule_resource_schedule_id_foreign');
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
        Schema::dropIfExists(strtolower('schedule_resources'));
    }
}
