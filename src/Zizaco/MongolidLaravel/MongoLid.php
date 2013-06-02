<?php namespace Zizaco\MongolidLaravel;

/**
 * This class extends the Zizaco\Mongolid\Model, so, in order
 * to understand the ODM implementation make sure to check the
 * base class.
 *
 * The Zizaco\MongolidLaravel\MongoLid simply extends the original
 * and framework agnostic model of MongoLid and implements some
 * validation rules using Laravel validation components.
 *
 * Remember, this package is meant to be used with Laravel while
 * the "zizaco\mongolid" is meant to be used with other frameworks
 * or even without any.
 *
 * @license MIT
 * @author  Zizaco Zizuini <zizaco@gmail.com>
 */
abstract class MongoLid extends \Zizaco\Mongolid\Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = null;

    /**
     * Error message bag
     *
     * @var Illuminate\Support\MessageBag
     */
    public $errors;

    /**
     * Save the model to the database if it's valid
     *
     * @param $force Force save even if the object is invalid
     * @return bool
     */
    public function save($force = false)
    {

        if ($this->isValid() || $force)
        {
            return parent::save();
        }
        else
        {
            return false;
        }
    }

    /**
     * Verify if the model is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if(! is_array(static::$rules) )
            return true;

        $validator = \Validator::make(
            $this->attributes,
            static::$rules
        );

        if ($validator->fails())
        {
            $this->errors = $validator->errors();
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Sets the database and the cache component of the model
     * If you extend the __construct() method, please don't forget
     * to call parent::__construct()
     */
    public function __construct()
    {
        if ($this->database == 'mongolid')
        {
            $this->database = \Config::get(
                'database.mongodb.default.database', 'mongolid'
            );    
        }
        
        static::$cacheComponent = \App::make('cache');
    }
}