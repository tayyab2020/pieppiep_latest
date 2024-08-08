<?php

class pap_product extends ObjectModel
{
    public $id_product;
    public $id_pap_product;
    public $id_shop;
    public $id_pap_unit_default = 0;
    public $enabled;
    public $dynamic_price = 0;
    public $calculation_type = 'normal';
    public $unit_conversion_enabled;
    public $unit_conversion_operator;
    public $unit_conversion_value;
    public $pack_area;
    public $roll_height;
    public $roll_width;
    public $pattern_repeat = 0;
    public $coverage;
    public $area_price;
    public $wastage_options;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
	    'table' => 'pap_product',
	    'primary' => 'id_pap_product',
	    'fields' => array(
		    	'id_product' =>	array('type' => self::TYPE_INT),
            		'id_shop' => array('type' => self::TYPE_INT),
            		'id_pap_unit_default' => array('type' => self::TYPE_INT),
            		'enabled' => array('type' => self::TYPE_INT),
            		'calculation_type' => array('type' => self::TYPE_STRING),
            		'dynamic_price' => array('type' => self::TYPE_INT),
            		'unit_conversion_enabled' => array('type' => self::TYPE_INT),
            		'unit_conversion_operator' => array('type' => self::TYPE_STRING),
            		'unit_conversion_value' => array('type' => self::TYPE_FLOAT),
            		'pack_area' => array('type' => self::TYPE_FLOAT),
            		'roll_height' => array('type' => self::TYPE_FLOAT),
            		'roll_width' => array('type' => self::TYPE_FLOAT),
            		'pattern_repeat' => array('type' => self::TYPE_FLOAT),
            		'coverage' => array('type' => self::TYPE_FLOAT),
            		'area_price' => array('type' => self::TYPE_FLOAT),
            		'wastage_options' => array('type' => self::TYPE_STRING)
		)
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'product',
        'objectsNodeName' => 'products',
        'fields' => array(
            'id_product' => array('required' => true),
            'id_shop' => array('required' => true),
            'id_pap_unit_default' => array('required' => true),
            'enabled' => array('required' => true),
            'calculation_type' => array(),
            'dynamic_price' => array('required' => true),
            'unit_conversion_enabled' => array('required' => true),
            'unit_conversion_operator' => array('required' => true),
            'unit_conversion_value' => array('required' => true),
            'pack_area' => array('required' => true),
            'roll_height' => array('required' => true),
            'roll_width' => array('required' => true),
            'pattern_repeat' => array('required' => true),
            'coverage' => array('required' => true),
            'area_price' => array('required' => true),
            'wastage_options' => array(),
        )
    );
}