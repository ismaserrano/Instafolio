<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	
	var $name = 'Users';

	public $helpers = array('Html','Form','Session','Js');//helpers empleados
	public $components = array('InstagramApi','RequestHandler','Session','Thumbnail');//componentes empleados
	public $uses = array('User','Photo');
	
	/**
	 * InstagramApiComponentの設定
	 * @var unknown_type
	 */
	var $instagramApi = array(
		'clientId' => 'c0eb2bfc20474995bfcb3efa9a40e263',
		'clientSecret' => '266cd61c8c8445ef886bcdaba2706824',
		'oauthCallbackUrl' => 'http://www.instafolio.net/users/oauthCallback',
	);
	
	var $nextMedia = array();
	
	
	
	
	public function beforeRender(){
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // // HTTP/1.1
		header("Pragma: no-cache");
		header("Expires: Mon, 17 Dec 2007 00:00:00 GMT"); // Date in the past
		header("Content-Type: text/html; charset=UTF-8");
	}

	/**
	 * OAuthをスタートさせる
	 */
	function oauthStart() {
		$this->InstagramApi->oauthStart();
	}
	
	/**
	 * callback
	 */
	function oauthCallback() {
		$result = $this->InstagramApi->oauthCallback();
		
		// accessTokenだけを取得する
		//var_dump($this->InstagramApi->readAccessToken());
		if ($this->InstagramApi->readAccessToken()){
			$this->userId = $this->InstagramApi->readUserId();
			$result = $this->InstagramApi->getUser($this->userId);
			$user = json_decode($result);
			$user_ddbb['User']['instagram_id'] = $this->userId;
			$user_ddbb['User']['profile_picture'] = $user->data->profile_picture;
			$user_ddbb['User']['bio'] = $user->data->bio;
			$user_ddbb['User']['website'] = $user->data->website;
			$user_ddbb['User']['instagram_user'] = $user->data->username;
			$user_ddbb['User']['date'] = date("Y-m-d H:i:s");
			$user_ddbb['User']['access_token'] = $this->InstagramApi->readAccessToken();
			$exists = $this->User->find('first', array('conditions'=>array('User.instagram_id'=>$user->data->id)));
			if (!$exists){
				$this->User->create();
				$this->User->save($user_ddbb);
			} else {
				if ($exists['User']['access_token']!=$this->InstagramApi->readAccessToken()){
					$sql = 'UPDATE users SET access_token = "'.$this->InstagramApi->readAccessToken().'"
						WHERE instagram_id = "'.$user->data->id.'"';
					$this->User->query($sql);
				}
			}
			//Session var logged
			$this->Session->write('isLogged', true);
			////Adding media to database if not exists
			//$userId = $this->User->find('first', array('conditions'=>array('User.instagram_id'=>$this->userId)));
			//$userId = $userId['User']['id'];
			//$media = $this->InstagramApi->getUserRecent($this->userId);
			//$result = json_decode($media);
			//foreach ($result->data as $value){
			//	$idParts = explode("_", $value->id);
			//	$exists = $this->Photo->find('first', array('conditions'=>array('Photo.photo_id'=>$idParts[0])));
			//	if (!$exists){
			//		$this->Photo->create();
			//		$media_ddbb['Photo']['photo_id'] = $idParts[0];
			//		$media_ddbb['Photo']['picture'] = $value->images->standard_resolution->url;
			//		$media_ddbb['Photo']['thumb'] = $value->images->thumbnail->url;
			//		$media_ddbb['Photo']['low'] = $value->images->low_resolution->url;
			//		$media_ddbb['Photo']['caption'] = utf8_decode($value->caption->text);
			//		$media_ddbb['Photo']['user_id'] = $userId;
			//		$media_ddbb['Photo']['filter'] = $value->filter;
			//		$media_ddbb['Photo']['link'] = $value->link;
			//		$media_ddbb['Photo']['latitude'] = $value->location->latitude;
			//		$media_ddbb['Photo']['longitude'] = $value->location->longitude;
			//		$media_ddbb['Photo']['date'] = date("Y-m-d H:i:s", $value->created_time);
			//		$this->Photo->save($media_ddbb);
			//	}
			//}
			////Recursive method to get all media
			//foreach ($this->checkNextMedia($result->pagination->next_url) as $value){
			//	foreach ($value as $value2){
			//		$idParts = explode("_", $value2->id);
			//		$exists = $this->Photo->find('count', array('conditions'=>array('Photo.photo_id'=>$idParts[0])));
			//		if (!$exists){
			//			$this->Photo->create();
			//			$media_ddbb['Photo']['photo_id'] = $idParts[0];
			//			$media_ddbb['Photo']['picture'] = $value2->images->standard_resolution->url;
			//			$media_ddbb['Photo']['thumb'] = $value2->images->thumbnail->url;
			//			$media_ddbb['Photo']['low'] = $value2->images->low_resolution->url;
			//			$media_ddbb['Photo']['caption'] = utf8_decode($value2->caption->text);
			//			$media_ddbb['Photo']['user_id'] = $userId;
			//			$media_ddbb['Photo']['filter'] = $value2->filter;
			//			$media_ddbb['Photo']['link'] = $value2->link;
			//			$media_ddbb['Photo']['latitude'] = $value2->location->latitude;
			//			$media_ddbb['Photo']['longitude'] = $value2->location->longitude;
			//			$media_ddbb['Photo']['date'] = date("Y-m-d H:i:s", $value2->created_time);
			//			$this->Photo->save($media_ddbb);
			//		}
			//	}
			//}
			$this->redirect("/users");
		}
	}


/**
 * index method
 *
 * @return void
 */
	public function index($slug=null,$arg1=null,$arg2=null) {
		if(!is_null($slug)){
			$this->$slug($arg1,$arg2);
		} else {
			//$this->User->recursive = 0;
			//$this->set('users', $this->paginate());
			if ($this->Session->check('isLogged') && $this->Session->check($this->InstagramApi->sessionBaseName.'.accessToken')){
				$instagramUserId = $this->InstagramApi->readUserId();
				$personal = $this->InstagramApi->getUser($instagramUserId);
				$result = json_decode($personal);
				$this->set('user', $result->data);
				$media = $this->InstagramApi->getUserRecent($instagramUserId);
				$result = json_decode($media);
				$this->set('media', $result);
				
				$userDDBB = $this->User->find('first', array('conditions'=>array('User.instagram_id'=>$instagramUserId)));
				$this->set('userDDBB', $userDDBB);
				
				//$this->render('index');
				
			} else {
				$this->redirect("/users/login");
			}
		}
	}
	
	
	/* Recursive function to get all media from next_url pagination */
	public function checkNextMedia($url=null){
		$this->autoRender = false;
		if (!is_null($url)){
			App::uses('HttpSocket', 'Network/Http');
			$this->HttpSocket = new HttpSocket();
			$resultNext = $this->HttpSocket->get($url);
			$mediaNext = json_decode($resultNext);
			array_push($this->nextMedia, $mediaNext->data);
			//debug($mediaNext->pagination);
			if (count((array)$mediaNext->pagination)<=0){
				$mediaNext->pagination->next_url = null;
			}
			$this->checkNextMedia($mediaNext->pagination->next_url);
		}
		return $this->nextMedia;
	}
	
	
	public function login(){
		$this->oauthStart();
	}
	
	
	public function logout(){
		$this->autoRender = false;
		$this->Session->write($this->InstagramApi->sessionBaseName.'.accessToken', null);
		$this->Session->write($this->InstagramApi->sessionBaseName.'.userId', null);
		$this->Session->write('isLogged', null);
		//$script = '<script src="'.Router::url("/").'js/libs/jquery-1.6.2.min.js"></script><script type="text/javascript">$.get("https://instagram.com/accounts/logout/",function(){document.href="'.Router::url("/").'";});</script>';
		$script = '<iframe src="https://instagram.com/accounts/logout/" width="0" height="0" frameborder="no" scrolling="no"></iframe>';
		echo $script;
	}
	
	public function social(){
		if ($_SERVER['REMOTE_ADDR']!='85.53.183.213' && $_SERVER['REMOTE_ADDR']!='85.54.182.184'){
			exit(0);
		}
		if ($this->Session->check($this->InstagramApi->sessionBaseName.'.accessToken')){
			//Configure::write('debug', 1);
			$userId = $this->InstagramApi->readUserId();
			$personal = $this->InstagramApi->getUser($userId);
			$result = json_decode($personal);
			$this->set('user', $result->data);
			$follows = $this->InstagramApi->getUserFollows($userId);
			$result = json_decode($follows);
			$recent = $this->InstagramApi->getUserFeed();
			$feed = json_decode($recent);
			$this->set('media', $feed);
			//$resultMedia = array();
			//foreach ($result->data as $key=>$value){
			//	$recent = $this->InstagramApi->getUserRecent($value->id);
			//	$resultRecent = json_decode($recent);
			//	array_push($resultMedia, $resultRecent);
			//	break;
			//}
			//$media = $this->InstagramApi->getPopularMedia();
		} else {
			$this->redirect("/users/login");
		}
		$this->render('social');
	}
	
	
	/* Likes function, to post and delete */
	public function likeMedia($slug=null, $mediaId=null){
		$this->autoRender = false;
		$result = "{meta:{code:404}, data:null}";
		if (is_null($this->data['mediaId'])){
			$slug = null;
		}
		if ($this->Session->check($this->InstagramApi->sessionBaseName.'.accessToken')){
			switch ($slug){
				case "post":
					$result = $this->InstagramApi->postLike($this->data['mediaId']);
					break;
				case "remove":
					$result = $this->InstagramApi->removeLike($this->data['mediaId']);
					break;
			}
		}
		echo $result;
	}
	
	
	//Ver el perfil de un usuario al que sigues
	public function profile($slug){
		if (!is_null($slug) && $this->Session->check($this->InstagramApi->sessionBaseName.'.accessToken')){
			//Si es un usuario en Instafolio, vamos a su perfil interno a ver sus fotos
			//si no lanzamos instagram
			$exists = $this->User->find('first', array('conditions'=>array('User.instagram_user'=>$slug)));
			if ($exists){
				$personal = $this->InstagramApi->getUser($exists['User']['instagram_id']);
				$result = json_decode($personal);
				$this->set('user', $result->data);
				$recent = $this->InstagramApi->getUserRecent($exists['User']['instagram_id']);
				$resultRecent = json_decode($recent);
				$this->set('media', $resultRecent);
			}
			$this->render('profile');
		} else {
			$this->redirect("/users");
		}
	}
	
	
	/* AJAX functions for likes and comments */
	public function likes($id=null){
		$this->autoRender = false;
		if (!is_null($id)){
			$media = $this->InstagramApi->getLikes($id);
			$result = json_decode($media);
			$htmlDoc = '';
			$contador = 0;
			foreach ($result->data as $value2){
				if ($contador>50){
					break;
				}
				$htmlDoc .= '<div class="info-content-likes">';
				//$htmlDoc .= '<img src="'.Router::url("/image/thumb/").$value2->profile_picture.'/50/50/1/1" alt="'.$value2->username.'" title="'.$value2->username.'" />';
				$htmlDoc .= '<img src="'.$value2->profile_picture.'" width="50" height="50" alt="'.$value2->username.'" title="'.$value2->username.'" />';
				$htmlDoc .= '<div class="info-inside">';
				$htmlDoc .= '<strong>'.$value2->username.'</strong>';
				$htmlDoc .= '</div>';
				$htmlDoc .= '</div>';
				$contador++;
			}
			//$htmlDoc .= '<div class="actions"><a href="#" id="'.$id.'" class="likes"><img src="'.Router::url("/image/thumb/").'img/heartShape.png/12/12/1/0" alt="I like this one" title="I like this one" /></a></div>';
		} else {
			$htmlDoc = 'ERROR';
		}
		echo $htmlDoc;
	}
	
	public function comments($id=null){
		$this->autoRender = false;
		if (!is_null($id)){
			$media = $this->InstagramApi->getMediaComments($id);
			$result = json_decode($media);
			$htmlDoc = '';
			$contador = 0;
			foreach ($result->data as $value2){
				if ($contador>50){
					break;
				}
				$htmlDoc .= '<div class="info-content">';
				//$htmlDoc .= '<img src="'.Router::url("/image/thumb/").$value2->from->profile_picture.'/50/50/1/1" alt="'.$value2->from->username.'" title="'.$value2->from->username.'" />';
				$htmlDoc .= '<img src="'.$value2->from->profile_picture.'" width="50" height="50" alt="'.$value2->from->username.'" title="'.$value2->from->username.'" />';
				$htmlDoc .= '<div class="info-inside">';
				$htmlDoc .= '<strong>'.$value2->from->username.' said:</strong><br />'.$value2->text;
				$htmlDoc .= '</div>';
				$htmlDoc .= '</div>';
				$contador++;
			}
			//$htmlDoc .= '<div class="actions"><a href="#" id="'.$id.'" class="comments"><img src="'.Router::url("/image/thumb/").'img/speechBubble.png/12/12/1/0" alt="Comment this one" title="Comment this one" /></a></div>';
		} else {
			$htmlDoc = 'ERROR';
		}
		echo $htmlDoc;
	}
	/******************************************/
	
	
	/* View media exists in database and insert if not*/
	public function ddbbExists(){
		$this->autoRender = false;
		$instagramUserId = $this->InstagramApi->readUserId();
		//Adding media to database if not exists
		$userId = $this->User->find('first', array('conditions'=>array('User.instagram_id'=>$instagramUserId)));
		$userId = $userId['User']['id'];
		$query = "DELETE * FROM photos WHERE user_id = ".$userId;
		$resultErase = $this->Photo->query($query);
		$media = $this->InstagramApi->getUserRecent($instagramUserId);
		$result = json_decode($media);
		foreach ($result->data as $value){
			$idParts = explode("_", $value->id);
			$exists = $this->Photo->find('first', array('conditions'=>array('Photo.photo_id'=>$idParts[0])));
			if (!$exists){
				$this->Photo->create();
				$media_ddbb['Photo']['photo_id'] = $idParts[0];
				$media_ddbb['Photo']['picture'] = $value->images->standard_resolution->url;
				$media_ddbb['Photo']['thumb'] = $value->images->thumbnail->url;
				$media_ddbb['Photo']['low'] = $value->images->low_resolution->url;
				$media_ddbb['Photo']['caption'] = utf8_decode($value->caption->text);
				$media_ddbb['Photo']['user_id'] = $userId;
				$media_ddbb['Photo']['filter'] = $value->filter;
				$media_ddbb['Photo']['link'] = $value->link;
				$media_ddbb['Photo']['latitude'] = $value->location->latitude;
				$media_ddbb['Photo']['longitude'] = $value->location->longitude;
				$media_ddbb['Photo']['date'] = date("Y-m-d H:i:s", $value->created_time);
				$this->Photo->save($media_ddbb);
			}
		}
		//Recursive method to get all media
		foreach ($this->checkNextMedia($result->pagination->next_url) as $value){
			foreach ($value as $value2){
				$idParts = explode("_", $value2->id);
				$exists = $this->Photo->find('count', array('conditions'=>array('Photo.photo_id'=>$idParts[0])));
				if (!$exists){
					$this->Photo->create();
					$media_ddbb['Photo']['photo_id'] = $idParts[0];
					$media_ddbb['Photo']['picture'] = $value2->images->standard_resolution->url;
					$media_ddbb['Photo']['thumb'] = $value2->images->thumbnail->url;
					$media_ddbb['Photo']['low'] = $value2->images->low_resolution->url;
					$media_ddbb['Photo']['caption'] = utf8_decode($value2->caption->text);
					$media_ddbb['Photo']['user_id'] = $userId;
					$media_ddbb['Photo']['filter'] = $value2->filter;
					$media_ddbb['Photo']['link'] = $value2->link;
					$media_ddbb['Photo']['latitude'] = $value2->location->latitude;
					$media_ddbb['Photo']['longitude'] = $value2->location->longitude;
					$media_ddbb['Photo']['date'] = date("Y-m-d H:i:s", $value2->created_time);
					$this->Photo->save($media_ddbb);
				}
			}
		}
	}
	

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_view method
 *
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
?>