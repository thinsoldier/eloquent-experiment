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
}




class Island extends LocationLevelBase 
{
	protected $mySection = 'island';
	
	public function locations()
	{
		return $this->hasMany( Location::class, 'parent_id' );
	}
}




class Location extends LocationLevelBase 
{
	protected $mySection = 'location';
	
	public function districts()
	{
		return $this->hasMany( District::class, 'parent_id' );
	}
}




class District extends LocationLevelBase 
{
	protected $mySection = 'district';
}




function varDumpColumn( $list , $column='category')
{
	var_dump( array_column( $list, $column) );
}




echo '<h1>Island</h1>';
$island = Island::whereCategory('Eleuthera')->get()->first();
var_dump($island->toArray());


echo '<h1>Locations in island</h1>';
varDumpColumn( $island->locations->toArray() );


echo '<h1>Districts in last Location on island</h1>';
//var_dump( array_column() );
varDumpColumn(  $island->locations->last()->districts->toArray() );
?>