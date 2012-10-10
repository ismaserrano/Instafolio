<?php
App::uses('AppModel', 'Model');
/**
 * Photo Model
 *
 * @property User $User
 */
class Photo extends AppModel {
	
	public $name = 'Photo';
	
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

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
