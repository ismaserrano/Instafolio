<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {
    
        public $name = 'User';
        
        public $actsAs = array
	              (
			'Containable',
			//'Sluggable' => array
			//	       (
			//			'label'       => 'name',
			//			'translation' => 'utf-8',
			//			'overwrite'   => true
			//		)
	);
    
    
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'instagram_user';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		//'email' => array(
		//	'email' => array(
		//		'rule' => array('email'),
		//		//'message' => 'Your custom message here',
		//		//'allowEmpty' => false,
		//		//'required' => false,
		//		//'last' => false, // Stop validation after this rule
		//		//'on' => 'create', // Limit validation to 'create' or 'update' operations
		//	),
		//),
		'date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
        
        
        public $hasMany = array(
		'Photo' => array(
			'className' => 'Photo',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
