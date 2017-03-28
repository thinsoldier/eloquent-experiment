<?php
namespace GrapeSoda;

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




class LocationLevelBase extends Category
{
	protected $mySection;
	/*
	I believe newQuery() is called in any instance where you use the query builder for that model, 
	so this should cause all queries on this model to be limited to records that are islands.
	https://laravel.io/index.php/forum/02-13-2014-eloquent-model-default-scope
	*/
	public function newQuery($excludeDeleted = true)
	{
		return parent::newQuery($excludeDeleted)->whereSection( $this->mySection );
	}
	
	public function children(){}
}




class Island extends LocationLevelBase 
{
	protected $mySection = 'island';
	
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




class Location extends LocationLevelBase 
{
	protected $mySection = 'location';
	
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




class District extends LocationLevelBase 
{
	protected $mySection = 'district';
	
	public function parent()
	{
		return $this->belongsTo( Location::class, 'parent_id' );
	}

	public function location(){ return $this->parent(); }	
}




function varDumpColumn( $list , $column='category')
{
	var_dump( array_column( $list, $column) );
}




echo '<h1>Island - Eleuthera</h1>';
$island = Island::whereCategory('Eleuthera')->get()->first();
var_dump($island->toArray());


echo '<h1>Locations in island</h1>';
varDumpColumn( $island->children->toArray() );


echo '<h1>Districts in last Location on island</h1>';
varDumpColumn(  $island->children->last()->children->toArray() );

echo '<h1>Parent Location of last district</h1>';
var_dump(  $island->children->last()->children->last()->parent->category );

echo '<h1>Parent Island of above Location</h1>';
var_dump( $island -> children  -> last() -> children  ->last() -> parent   -> parent -> category );
var_dump( $island -> locations -> last() -> districts ->last() -> location -> island -> category );

// todo: has-many-through: get all districts in island.
echo '<h1>All Districts in Eleuthera</h1>';
var_dump( $island -> grandchildren );
?>