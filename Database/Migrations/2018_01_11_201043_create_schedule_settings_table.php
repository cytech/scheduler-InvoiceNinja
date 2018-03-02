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

class CreateScheduleSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('schedule_settings'), function (Blueprint $table) {
	        $table->increments('id');
	        $table->timestamps();
	        $table->string('setting_key');
	        $table->text('setting_value');

	        $table->index('setting_key');

        });

	    //insert scheduler settings
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'pastdays', 'setting_value' => '60']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'eventLimit','setting_value' => '5']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'createWorkorder','setting_value' => '0']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'version', 'setting_value' => '2.0.0']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'fcThemeSystem', 'setting_value' => 'standard']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'fcAspectRatio', 'setting_value' => '1.35']);
	    DB::table('schedule_settings')->insert([ 'setting_key' => 'timestep', 'setting_value' => '15']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(strtolower('schedule_settings'));
    }
}
