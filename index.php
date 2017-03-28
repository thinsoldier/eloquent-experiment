<?php
namespace GrapeSoda;

error_reporting( E_ALL );

ini_set('xdebug.var_display_max_children', 512);

require 'vendor/autoload.php';

// Illuminate Database Usage Instructions
// https://github.com/illuminate/database/blob/v5.0.33/README.md

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Eloquent;

$capsule = new Capsule;

$capsule->addConnection([
	'driver'    => 'mysql',
	'host'      => '10.0.2.2',
	'database'  => 'eloquent-experiment',
	'username'  => 'root',
	'password'  => '',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


//-----------------------------


class Category extends Eloquent
{
	protected $table = 'categories';

	public $timestamps = false;
}



class Island extends Eloquent 
{
	public $timestamps = false;
	
	public function children()
	{
		return $this->hasMany( Location::class, 'parent_id' );
	}
	
	public function locations(){ return $this->children(); }
	
	public function grandchildren()
	{
		return $this->hasManyThrough( District::class, Location::class, 'parent_id', 'parent_id');
	}
}




class Location extends Eloquent 
{
	public $timestamps = false;
	
	public function children()
	{
		return $this->hasMany( District::class, 'parent_id' );
	}
	
	public function districts(){ return $this->children(); }
	
	public function parent()
	{
		return $this->belongsTo( Island::class, 'parent_id' );
	}
	
	public function island(){ return $this->parent(); }	
}




class District extends Eloquent 
{
	public $timestamps = false;
		
	public function parent()
	{
		return $this->belongsTo( Location::class, 'parent_id' );
	}

	public function location(){ return $this->parent(); }	
	
	public function grandparent()
	{
		return $this->parent->parent->category;
	}
}




function varDumpColumn( $list , $column='category')
{
	var_dump( array_column( $list, $column) );
}




echo '<h1>Island</h1>';
$island = Island::whereCategory('Nassau/New Providence')->get()->first();
var_dump($island->toArray());


echo '<h1>Locations in island</h1>';
var_dump( $island->children->toArray() );


echo '<h1>Districts in last Location on island</h1>';
var_dump(  $island->children->last()->children->toArray() );

echo '<h1>Last District in last Location on island</h1>';
var_dump(  $island->children->last()->children->last()->toArray() );


echo '<h1>Parent Location of last district</h1>';
var_dump(  $island->children->last()->children->last()->parent->toArray() );


echo '<h1>Parent Island of above Location</h1>';
var_dump( $island -> children  -> last() -> children  ->last() -> parent   -> parent -> toArray() );
var_dump( $island -> locations -> last() -> districts ->last() -> location -> island -> toArray() );


echo '<h1>All Districts in Island - <code>$island->grandchildren</code></h1>';
var_dump( $island->grandchildren->toArray() );

echo '<h1>Last District in Island</h1>';
var_dump( $island->grandchildren->last()->toArray() );

echo '<p>The parent_id shown above should be 471, not 410. 410 is the Island of Nassau.<br>
The join is probably screwing up which column value to return because all column names are
the same in all 3 tables (views).
</p>';

echo '<h1>Grandparent Island of last District in Island:</h1>';
echo "<p>Fails with excessive chaining: ";
echo( $island->grandchildren->last()->grandparent() );
// Problem with the above: The grandchild (district) has its 
// parent's (location) parent_id value (an island id) 
// instead of its proper parent_id value (a location id).
// I'm guessing this is due to all of the tables having identical field names.
// There is no problem with the below:
echo "<p>Works with minimal chaining: ";
echo ( District::findOrFail(526)->grandparent() );

?>