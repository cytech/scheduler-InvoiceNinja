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

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtolower('schedule'), function (Blueprint $table) {
	        $table->increments('id');
	        $table->string('title',150);
	        $table->text('description')->nullable();
	        $table->tinyInteger('isRecurring')->unsigned()->default(0);
	        $table->string('rrule',300)->nullable();
	        $table->unsignedInteger('user_id')->index();
	        $table->integer('category_id')->default(1);
	        $table->string('url',300)->nullable();
	        $table->boolean('will_call')->default(0);
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('client_id')->index()->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_deleted')->default(false);

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            //$table->unsignedInteger('public_id')->index();
            //$table->unique( ['account_id', 'public_id'] );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(strtolower('schedule'));
	    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
