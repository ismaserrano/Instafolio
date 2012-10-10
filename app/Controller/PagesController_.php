<?php
/**
 * Pages Controller
 *
 */
class PagesController extends AppController {

	public $helpers = array('Html','Form','Session','Js');//helpers empleados
	public $components = array('InstagramApi','RequestHandler','Session');//componentes empleados
	public $uses = array('User','Photo');
	
	public $logoutVar = false;
	
	var $instagramApi = array(
		'clientId' => 'c0eb2bfc20474995bfcb3efa9a40e263',
		'clientSecret' => '266cd61c8c8445ef886bcdaba2706824',
		'oauthCallbackUrl' => 'http://www.instafolio.net/users/oauthCallback',
	);
	
	
	public function beforeRender(){
		$this->set('logout', $this->logoutVar);
	}


/**
 * index method
 *
 * @return void
 */
	public function index($slug=null, $arg1=null, $arg2=null) {
		switch ($slug){
			case "about-us":
			case "contact":
				$this->render($slug);
				break;
			case "logout":
			case "search":
			case "getAccessToken":
			case "tinyVal":
				$this->$slug($arg1, $arg2);
				break;
			//case "popular":
			//	$popular = $instagram->getPopularMedia();
			//	$response = json_decode($popular, true);
			//	$this->render($slug);
			//	break;
			default:
				if (!empty($slug) && $slug!=''){
					$exists = $this->User->find('first', array(
										   'conditions'=>array(
												       'User.instagram_user'=>$slug,
												       'User.public'=>1
												       )
										   )
								    );
					if (!$exists){
						$this->autoRender = false;
						echo "This user not exists or his profile is not public.";
					} else {
						//Configure::write('debug', 1);
						//$this->User->recursive = 0;
						$user = $this->User->find('first', array(
										 'conditions'=>array(
												     'User.instagram_user'=>$slug,
												     'User.public'=>1
												     ),
										 'contain'=>array('Photo'=>array('conditions'=>array('Photo.active'=>1),
														'order'=>array('Photo.date DESC')))
										)
								);
						$this->set('user', $user);
						$testing = '';
						//if ($_SERVER['REMOTE_ADDR']=='85.53.50.139'){
						//	$testing = '_';
						//}
						$this->layout = 'templates'.$testing;
						$this->render('profile');
					}
				}
		}
	}
	
	
	public function logout(){
		$this->Session->write($this->InstagramApi->sessionBaseName.'.accessToken', null);
		$this->Session->write($this->InstagramApi->sessionBaseName.'.userId', null);
		$this->logoutVar = true;
		$this->render('index');
	}
	
	
	/* Search function */
	public function search(){
		$this->autoRender = false;
		$text = $this->data['Search']['text'];
		$searchOpt = substr($text, 0, 1);
		
		switch($searchOpt){
			case '#':
				$text = str_replace($searchOpt, '', $text);
				$result = $this->InstagramApi->getRecentTags($text);
				break;
			case '@':
				$text = str_replace($searchOpt, '', $text);
				$result = $this->InstagramApi->searchUser($text);
				break;
			default:
				$result = $this->InstagramApi->getRecentTags($text);
		}
		//$response = json_decode($result);
		echo $result;
	}
	
	
	public function tinyVal($opt=null){
		$this->autoRender = false;
		$html = '';
		switch($opt){
			case 'profile':
				$this->User->updateAll(array('User.'.$this->data['field'] => $this->data['val']), array('User.instagram_id' => $this->data['id']));
				break;
			default:
				if (is_null($opt)){
					if ($this->Photo->updateAll(array('Photo.'.$this->data['field'] => $this->data['val']),array('Photo.photo_id' => $this->data['id']))){
						if ($this->data['val']=='0'){
							$html = '<a href="#" id="active_'.$this->data['id'].'" title="Enabled/Disabled" class="active_button" val="1"><img src="'.Router::url("/image/thumb/").'img/okTip.png/12/12/1/0" alt="enabled/disabled" title="enabled/disabled" /></a> Disabled';
						} else {
							$html = '<a href="#" id="active_'.$this->data['id'].'" title="Enabled/Disabled" class="active_button" val="0"><img src="'.Router::url("/image/thumb/").'img/okTip.png/12/12/1/0" alt="enabled/disabled" title="enabled/disabled" /></a> Enabled';
						}
					}
				} else {
					$photo = $this->Photo->find('first', array('conditions'=>array('Photo.photo_id'=>$opt)));
					$photoStatus = $photo['Photo']['active']?'Enabled':'Disabled';
					$photoVal = $photo['Photo']['active']?'0':'1';
					$html = '<a href="#" id="active_'.$opt.'" title="Enabled/Disabled" class="active_button" val="'.$photoVal.'"><img src="'.Router::url("/image/thumb/").'img/okTip.png/12/12/1/0" alt="enabled/disabled" title="enabled/disabled" /></a> '.$photoStatus;
				}
		}
		echo $html;
	}

}
?>