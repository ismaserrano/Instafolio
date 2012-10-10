<?php
App::uses('AppController', 'Controller');
/**
 * Photos Controller
 *
 * @property Photo $Photo
 */
class PhotosController extends AppController {
	
	var $name = 'Photo';
	

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Photo->recursive = 0;
		$this->set('photos', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		$this->set('photo', $this->Photo->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Photo->create();
			if ($this->Photo->save($this->request->data)) {
				$this->flash(__('Photo saved.'), array('action' => 'index'));
			} else {
			}
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Photo->save($this->request->data)) {
				$this->flash(__('The photo has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->Photo->read(null, $id);
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		if ($this->Photo->delete()) {
			$this->flash(__('Photo deleted'), array('action' => 'index'));
		}
		$this->flash(__('Photo was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Photo->recursive = 0;
		$this->set('photos', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		$this->set('photo', $this->Photo->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Photo->create();
			if ($this->Photo->save($this->request->data)) {
				$this->flash(__('Photo saved.'), array('action' => 'index'));
			} else {
			}
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Photo->save($this->request->data)) {
				$this->flash(__('The photo has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->Photo->read(null, $id);
		}
		$users = $this->Photo->User->find('list');
		$this->set(compact('users'));
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Photo->id = $id;
		if (!$this->Photo->exists()) {
			throw new NotFoundException(__('Invalid photo'));
		}
		if ($this->Photo->delete()) {
			$this->flash(__('Photo deleted'), array('action' => 'index'));
		}
		$this->flash(__('Photo was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
?>