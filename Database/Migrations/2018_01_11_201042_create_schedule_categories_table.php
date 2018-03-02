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

class CreateScheduleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('schedule_categories'), function (Blueprint $table) {
	        $table->increments('id');
	        $table->string('name',50);
	        $table->string('text_color',10);
	        $table->string('bg_color',10);

        });

	    DB::table('schedule_categories')->insert(array('name' => 'Worker Schedule','text_color' => '#000000','bg_color' => '#aaffaa'));
	    DB::table('schedule_categories')->insert(array('name' => 'General Appointment','text_color' => '#000000','bg_color' => '#5656ff'));
	    DB::table('schedule_categories')->insert(array('name' => 'Client Appointment','text_color' => '#000000','bg_color' => '#d4aaff'));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(strtolower('schedule_categories'));
    }
}
