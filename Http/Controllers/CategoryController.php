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

namespace Modules\Scheduler\Http\Controllers;

use Modules\Scheduler\Models\Category;
use Modules\Scheduler\Models\Schedule;
use Illuminate\Http\Request;

class CategoryController extends Controller {
	public function index() {
		$categories = Category::all();

		return view( 'Scheduler::categories.index' )->with( 'categories', $categories );
	}

	public function create() {
		return view( 'Scheduler::categories.create' );
	}

	public function store( Request $request ) {
		$categories             = new Category;
		$categories->name       = $request->name;
		$categories->text_color = $request->text_color;
		$categories->bg_color   = $request->bg_color;
		$categories->save();

		return redirect()->route( 'scheduler.categories.index' )->with( 'alertSuccess', 'Successfully Created category!' );
	}

	public function show( $id ) {
		$categories = Category::find( $id );

		return view('Scheduler::schedule.categories.show', compact( 'categories' ) );
	}

	public function edit( $id ) {
		$categories = Category::find( $id );

		return view( 'Scheduler::categories.edit', compact( 'categories' ) );
	}

	public function update( Request $request, $id ) {
		$categories             = Category::find( $id );
		$categories->name       = $request->name;
		$categories->text_color = $request->text_color;
		$categories->bg_color   = $request->bg_color;
		$categories->save();

		return redirect()->route( 'scheduler.categories.index' )->with( 'alertSuccess', 'Successfully Edited category!' );
	}

	public function delete( $id ) {
		//check if category is assigned

		$catexist = Schedule::where('category_id',$id)->count();
		if ($catexist){
			return response()->json(['error' => 'Category is assigned to '. $catexist.
			                                    ' event(s) and cannot be deleted!'], 422);
		}
		$category = Category::find( $id );
		$category->delete();

		return redirect()->route( 'scheduler.categories.index' )->with( 'alertSuccess', 'Successfully Deleted category!' );
	}
}