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
use Modules\Scheduler\Models\Category;
use Modules\Scheduler\Models\Schedule;

class UpdateScheduleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //move user defined category ids
        $usercats = Category::where('id', '>', 3)->get();
        foreach ($usercats as $cats){
            $cats->id = $cats->id + 6;
            $cats->save();
        }

        //update schedule category ids
        $schedcats = Schedule::where('category_id', '>', 3)->get();
        foreach ($schedcats as $scats){
            $scats->category_id = $scats->category_id + 6;
            $scats->save();
        }

        //set autoincrement to max(id) +1
        $autoinc = Category::max('id') + 1;
        DB::statement("ALTER TABLE `ninja-dev`.`schedule_categories` AUTO_INCREMENT = $autoinc") ;



	    DB::table('schedule_categories')->insert(array('id'=> 4,'name' => 'Quote','text_color' => '#ffffff','bg_color' => '#716cb1'));
        DB::table('schedule_categories')->insert(array('id'=> 5,'name' => 'Invoice','text_color' => '#ffffff','bg_color' => '#377eb8'));
        DB::table('schedule_categories')->insert(array('id'=> 6,'name' => 'Payment','text_color' => '#ffffff','bg_color' => '#5fa213'));
        DB::table('schedule_categories')->insert(array('id'=> 7,'name' => 'Expense','text_color' => '#ffffff','bg_color' => '#d95d02'));
        DB::table('schedule_categories')->insert(array('id'=> 8,'name' => 'Project','text_color' => '#ffffff','bg_color' => '#676767'));
        DB::table('schedule_categories')->insert(array('id'=> 9,'name' => 'Task','text_color' => '#ffffff','bg_color' => '#a87821'));

        //insert new setting for coreevent display
        DB::table('schedule_settings')->insert(array('setting_key' => 'enabledCoreEvents','setting_value' => '7'));

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
