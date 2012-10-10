<?php
/**
 * InstagramAPI component
 * @author polidog <polidogs@gmail.com>
 * @version 0.0.1
 */
class InstagramApiComponent extends Component
{
	const REQUEST_METHOD_GET = 'get';
	const REQUEST_METHOD_POST = 'post';
	
	/**
	 * 使用するコンポーネント
	 * @var array
	 * @access private
	 */
	public $helpers = array('Session');
	public $components = array('Session');
	
	/**
	 * コントローラ
	 * @var AppController 
	 * @access private
	 */
	var $_controller;
	
	/**
	 * OAuth認証時のURL
	 * @var string
	 * @access public
	 */
	var $authorizeUrl = "http://api.instagram.com/oauth/authorize";
	
	/**
	 * AccessTokenを取得するためのURL
	 * @var string
	 */
	var $accessTokenUrl = "https://api.instagram.com/oauth/access_token";
	
	/**
	 * 認証時のコールバックURL
	 * @var string
	 * @access public
	 */
	var $oauthCallbackUrl;
	
	/**
	 * CLIENT ID
	 * @var string
	 * @access public
	 */
	var $clientId;
	
	/**
	 * CLIENT SECRET
	 * @var string
	 * @access public
	 */
	var $clientSecret;

	/**
	 * アプリケーションに対するAPIの許容範囲の設定
	 * @var array
	 */
	var $scope = array(
		'basic','relationships','comments','likes'
	);
	
	
	/**
	 * OAuth用のコールバックの指定
	 * @var array
	 * @access public
	 */
	var $autoStartAction = array(
		'oauthStart' =>'/instagram/index' ,
		'oauthCallback' => '/instagram/callback',
	);
	
	
	
	/**
	 * API通信時に使用するURL
	 * @var string
	 * @access public
	 */
	var $apiBaseUrl	= "https://api.instagram.com/v1";

	/**
	 * access tokenを保存するためn
	 * @var object 
	 */
	var $sessionBaseName = "instagram";
	
	/**
	 * response typeを保存するためn
	 * @var object 
	 */
	var $responseType = "code";
	
	/**
	 * HttpSocket instance
	 * @var HttpSocket
	 */
	var $HttpSocket;

	/**
	 * リダイレクトを許可する、しないを選択する
	 * @var boolean
	 * @access public
	 */
	var $redirect	= true;		

	/**
	 * リダイレクト時のURL
	 * @var array
	 * @access public
	 */
	var $redirectUrl = array(
		'oauth_denied'		=> '/',
		'oauth_noauthorize' => '/',
		'oauth_authorize'	=> '/'
	);
	
	
	/* Calling array URLS */
	protected $_endpointUrls = array(
		'authorize' => 'https://api.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=%s',
		'access_token' => 'https://api.instagram.com/oauth/access_token',
		'user' => 'https://api.instagram.com/v1/users/%d/?access_token=%s',
		//'user_feed' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s&max_id=%d&min_id=%d',
		'user_feed' => 'https://api.instagram.com/v1/users/self/feed?access_token=%s',
		//'user_recent' => 'https://api.instagram.com/v1/users/%d/media/recent/?access_token=%s&max_id=%d&min_id=%d&max_timestamp=%d&min_timestamp=%d',
		'user_recent' => 'https://api.instagram.com/v1/users/%d/media/recent/?access_token=%s',
		'user_search' => 'https://api.instagram.com/v1/users/search?q=%s&access_token=%s',
		'user_follows' => 'https://api.instagram.com/v1/users/%d/follows?access_token=%s',
		'user_followed_by' => 'https://api.instagram.com/v1/users/%d/followed-by?access_token=%s',
		'user_requested_by' => 'https://api.instagram.com/v1/users/self/requested-by?access_token=%s',
		'user_relationship' => 'https://api.instagram.com/v1/users/%d/relationship?access_token=%s',
		'modify_user_relationship' => 'https://api.instagram.com/v1/users/%d/relationship?action=%s&access_token=%s',
		'media' => 'https://api.instagram.com/v1/media/%d?access_token=%s',
		'media_search' => 'https://api.instagram.com/v1/media/search?lat=%s&lng=%s&max_timestamp=%d&min_timestamp=%d&distance=%d&access_token=%s',
		'media_popular' => 'https://api.instagram.com/v1/media/popular?access_token=%s',
		'media_comments' => 'https://api.instagram.com/v1/media/%d/comments?access_token=%s',
		'post_media_comment' => 'https://api.instagram.com/v1/media/%d/comments?access_token=%s',
		'delete_media_comment' => 'https://api.instagram.com/v1/media/%d/comments?comment_id=%d&access_token=%s',
		'likes' => 'https://api.instagram.com/v1/media/%d/likes?access_token=%s',
		'post_like' => 'https://api.instagram.com/v1/media/%d/likes?access_token=%s',
		'remove_like' => 'https://api.instagram.com/v1/media/%d/likes?access_token=%s',
		'tags' => 'https://api.instagram.com/v1/tags/%s?access_token=%s',
		'tags_recent' => 'https://api.instagram.com/v1/tags/%s/media/recent?max_id=%d&min_id=%d&access_token=%s',
		'tags_search' => 'https://api.instagram.com/v1/tags/search?q=%s&access_token=%s',
		'locations' => 'https://api.instagram.com/v1/locations/%d?access_token=%s',
		'locations_recent' => 'https://api.instagram.com/v1/locations/%d/media/recent/?max_id=%d&min_id=%d&max_timestamp=%d&min_timestamp=%d&access_token=%s',
		'locations_search' => 'https://api.instagram.com/v1/locations/search?lat=%s&lng=%s&foursquare_id=%d&distance=%d&access_token=%s',
	);
	
	/**
	 * オブジェクト起動時
	 * @param AppController $controller
	 * @param array $settings
	 */
	function initialize(&$controller,$settings=null ) {
		$this->_controller = &$controller;
		
		// パラメータをセットする
		if ( isset($controller->instagramApi) && is_array($controller->instagramApi)) {
			foreach( $controller->instagramApi as $key => $value ) {
				$this->$key = $value;
			}
		}
		//App::import('Core', 'HttpSocket');
		App::uses('HttpSocket', 'Network/Http');
		$this->HttpSocket = new HttpSocket();
		
	}
	
	/**
	 * beforeFilter後の動作
	 * @param AppController $controller
	 */
	function startup(&$controller) {
		if ( !empty($this->oauthCallbackUrl) ) {
			$this->oauthCallbackUrl = Router::url($this->oauthCallbackUrl,true);
		}
	}
	
	/**
	 * OAuth認証を開始する
	 */
	function oauthStart() {
		if ( is_null($this->oauthCallbackUrl) ) {
			$this->oauthCallbackUrl = "http://".env('SERVER_NAME').$this->autoStartAction['oauthCallback'];
		}
		
		$scope = null;
		if ( is_array($this->scope) ) {
			$scope = implode(' ', $this->scope);
		}
		$url = $this->authorizeUrl.'/'."?".http_build_query(array(
			'client_id' => $this->clientId,
			'redirect_uri' => $this->oauthCallbackUrl,
			'response_type' => $this->responseType,
			'scope' => $scope,			
		));
		$this->_redirect($url,null,true);
		
	}
	
	/**
	 * OAuth認証のコールバック
	 */
	function oauthCallback() {
		$code = null;
		if ( isset($this->_controller->params['url']['code']) ) { 
			$code = $this->_controller->params['url']['code'];
		}
		$accessToken = $this->getAccessToken($code);
		$this->saveUserId($accessToken['user']['id']);
		$this->saveAccessToken($accessToken['access_token']);
		return $accessToken;
	}
	
	/**
	 * AccessTokenを取得する
	 * @param string $code
	 * @return array
	 */
	function getAccessToken($code = null) {
		if ( is_null($code) ) return false;
		$uri = $this->accessTokenUrl."?".http_build_query(array(
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
			'grant_type'	=> 'authorization_code',
			'redirect_uri' => $this->oauthCallbackUrl,
			'code' => $code,			
		));
		$accessToken = $this->HttpSocket->post($this->accessTokenUrl,array(
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret,
			'grant_type'	=> 'authorization_code',
			'redirect_uri' => $this->oauthCallbackUrl,
			'code' => $code,			
		));
		if ( $accessToken ) {
			$accessToken = json_decode($accessToken,true);
		}
		return $accessToken;
	}
	
	/**
	 * @param string $accessToken
	 */
	function saveUserId($userId) {
		$this->Session->write($this->sessionBaseName.'.userId',$userId);
	}
	
	/**
	 * return userID
	 * @return string
	 */
	function readUserId() {
		return $this->Session->read($this->sessionBaseName.'.userId');		
	}

	/**
	 * @param string $accessToken
	 */
	function saveAccessToken($accessToken) {
		$this->Session->write($this->sessionBaseName.'.accessToken',$accessToken);
	}
	
	/**
	 * 保存しているアクセストークンを取得する
	 * @return string
	 */
	function readAccessToken() {
		return $this->Session->read($this->sessionBaseName.'.accessToken');		
	}
	
	/**
	 * APIをコールする
	 * @param string $path
	 * @param array $data
	 * @param string $method
	 * @param boolean $assoc
	 */
	function callApi($path,$data = array(),$method=self::REQUEST_METHOD_GET,$decode = true,$assoc = true) {
		$accessToken = $this->readAccessToken();
		if ( !$accessToken ) {
			return false;
		}
		$urlVars = array();
		$urlVars = http_build_query($data);
		$url = $this->apiBaseUrl.$path.'?'.$urlVars.'&access_token='.$accessToken;
		//$result = $this->HttpSocket->$method($url,$data);
		$result = $this->HttpSocket->$method($url);
		if ( $decode ) {
			$result = json_decode($result,$assoc);
		}
		return $result;
	}
	
	
	function getUsers() {
		
	}
	
	/**
	 * __callメソッドの実装
	 * @param string $method
	 * @param array $args
	 */
	function __call( $method, $args ) {
		$pattern = "/^(api)([a-zA-Z1-9_]*)/i";
		if ( preg_match($pattern, $method, $matches) ) {
			if ( isset($matches[2]) ) {
				
				$apiPath = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '/\\1', $matches[2]));
				$apiArgs = array($apiPath);
				
				if ( is_array($args) ) {
					foreach($args as $arg ) {
						$apiArgs[] = $arg;
					}
				}
				return call_user_func_array( array( $this, 'callApi'), $apiArgs);
			}
		}		
	}
	
	
	/**
	 * リダイレクト処理を行う
	 * @param string $type	$this->redirectUrlのキーまたはURLを指定する
	 * @param string $flashMessage　リダイレクト先で表示したいメッセージ
	 * @param boolean $forceRedirect 強制リダイレクトフラグ
	 * @access private
	 */
	function _redirect($type,$flashMessage=null,$forceRedirect = false) {
		
		$redirectFlag = $this->redirect;
		if ( $redirectFlag === false && $forceRedirect === true ) {
			$redirectFlag = true;
		}
		
		if ( $redirectFlag ) {
			$url = $type;
			if ( isset($this->redirectUrl[$type]) ) {
				$url = $this->redirectUrl[$type];
				if ( is_null($url) ) {
					return null;
				}
			}
			
			if ( !is_null($flashMessage) ) {
				$this->Session->setFlash($flashMessage);
			}
			
			$this->_controller->redirect($url);
			
		}
		
		if ( $forceRedirect ) {
			
			if ( !is_null($flashMessage) ) {
				$this->Session->setFlash($flashMessage);
			}
			
			$this->_controller->redirect($type);
		}
	}
	
	
	/**
      * Get basic information about a user.
      * @param $id
      */
	public function getUser($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user'], $id, $this->readAccessToken());
		$result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * See the authenticated user's feed.
	 * @param integer $maxId. Return media after this maxId.
	 * @param integer $minId. Return media before this minId.
	 */
	public function getUserFeed($maxId = null, $minId = null,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_feed'], $this->readAccessToken(), $maxId, $minId);
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get the most recent media published by a user.
	 * @param $id. User id
	 * @param $maxId. Return media after this maxId
	 * @param $minId. Return media before this minId
	 * @param $maxTimestamp. Return media before this UNIX timestamp
	 * @param $minTimestamp. Return media after this UNIX timestamp
	 */
	public function getUserRecent($id, $maxId = '', $minId = '', $maxTimestamp = '', $minTimestamp = '', $method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_recent'], $id, $this->readAccessToken(), $maxId, $minId, $maxTimestamp, $minTimestamp);
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Search for a user by name.
	 * @param string $name. A query string
	 */
	public function searchUser($name,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_search'], $name, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get the list of users this user follows.
	 * @param integer $id. The user id
	 */
	public function getUserFollows($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_follows'], $id, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get the list of users this user is followed by.
	 * @param integer $id
	 */
	public function getUserFollowedBy($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_followed_by'], $id, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * List the users who have requested this user's permission to follow
	 */
	public function getUserRequestedBy($method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_requested_by'], $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get information about the current user's relationship (follow/following/etc) to another user.
	 * @param integer $id
	 */
	public function getUserRelationship($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['user_relationship'], $id, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Modify the relationship between the current user and the target user
	 * In order to perform this action the scope must be set to 'relationships'
	 * @param integer $id
	 * @param string $action. One of follow/unfollow/block/unblock/approve/deny
	 */
	public function modifyUserRelationship($id, $action,$method=self::REQUEST_METHOD_POST) {
	    $endpointUrl = sprintf($this->_endpointUrls['modify_user_relationship'], $id, $action, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get information about a media object.
	 * @param integer $mediaId
	 */
	public function getMedia($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['media'], $id, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Search for media in a given area.
	 * @param float $lat
	 * @param float $lng
	 * @param integer $maxTimestamp
	 * @param integer $minTimestamp
	 * @param integer $distance
	 */
	public function mediaSearch($lat, $lng, $maxTimestamp = '', $minTimestamp = '', $distance = '',$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['media_search'], $lat, $lng, $maxTimestamp, $minTimestamp, $distance, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get a list of what media is most popular at the moment.
	 */
	public function getPopularMedia($method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['media_popular'], $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get a full list of comments on a media.
	 * @param integer $id
	 */
	public function getMediaComments($id,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['media_comments'], $id, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Create a comment on a media.
	 * @param integer $id
	 * @param string $text
	 */
	public function postMediaComment($id, $text,$method=self::REQUEST_METHOD_POST) {
	    $this->_init();
	    $endpointUrl = sprintf($this->_endpointUrls['post_media_comment'], $id, $text, $this->readAccessToken());
		$result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Remove a comment either on the authenticated user's media or authored by the authenticated user.
	 * @param integer $mediaId
	 * @param integer $commentId
	 */
	public function deleteComment($mediaId, $commentId,$method=self::REQUEST_METHOD_DELETE) {
	    $endpointUrl = sprintf($this->_endpointUrls['delete_media_comment'], $mediaId, $commentId, $this->readAccessToken());
		$result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get a list of users who have liked this media.
	 * @param integer $mediaId
	 */
	public function getLikes($mediaId,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['likes'], $mediaId, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Set a like on this media by the currently authenticated user.
	 * @param integer $mediaId
	 */
	public function postLike($mediaId,$method=self::REQUEST_METHOD_POST) {
	    //$endpointUrl = sprintf($this->_endpointUrls['post_like'], $mediaId);
	    //$this->_initHttpClient($endpointUrl, CurlHttpClient::POST);
	    //$this->_httpClient->setPostParam('access_token', $this->readAccessToken());
	    //return $this->_getHttpClientResponse();
		$endpointUrl = sprintf($this->_endpointUrls['post_like'], $mediaId, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Remove a like on this media by the currently authenticated user.
	 * @param integer $mediaId
	 */
	public function removeLike($mediaId,$method=self::REQUEST_METHOD_DELETE) {
		$endpointUrl = sprintf($this->_endpointUrls['remove_like'], $mediaId, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	    //$endpointUrl = sprintf($this->_endpointUrls['remove_like'], $mediaId, $this->readAccessToken());
	    //$this->_initHttpClient($endpointUrl, CurlHttpClient::DELETE);
	    //return $this->_getHttpClientResponse();
	}
    
	/**
	 * Get information about a tag object.
	 * @param string $tagName
	 */
	public function getTags($tagName,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['tags'], $tagName, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get a list of recently tagged media.
	 * @param string $tagName
	 * @param integer $maxId
	 * @param integer $minId
	 */
	public function getRecentTags($tagName, $maxId = '', $minId = '',$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['tags_recent'], $tagName, $maxId, $minId, $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Search for tags by name - results are ordered first as an exact match, then by popularity.
	 * @param string $tagName
	 */
	public function searchTags($tagName,$method=self::REQUEST_METHOD_GET) {
	    $endpointUrl = sprintf($this->_endpointUrls['tags_search'], urlencode($tagName), $this->readAccessToken());
	    $result = $this->HttpSocket->$method($endpointUrl);
	    return $result;
	}
    
	/**
	 * Get information about a location.
	 * @param integer $id
	 */
	public function getLocation($id) {
	    $endpointUrl = sprintf($this->_endpointUrls['locations'], $id, $this->readAccessToken());
	    $this->_initHttpClient($endpointUrl);
	    return $this->_getHttpClientResponse();
	}
    
	/**
	 * Get a list of recent media objects from a given location.
	 * @param integer $locationId
	 */
	public function getLocationRecentMedia($id, $maxId = '', $minId = '', $maxTimestamp = '', $minTimestamp = '') {
	    $endpointUrl = sprintf($this->_endpointUrls['locations_recent'], $id, $maxId, $minId, $maxTimestamp, $minTimestamp, $this->readAccessToken());
	    $this->_initHttpClient($endpointUrl);
	    return $this->_getHttpClientResponse();
	}
    
	/**
	 * Search for a location by name and geographic coordinate.
	 * @see http://instagr.am/developer/endpoints/locations/#get_locations_search
	 * @param float $lat
	 * @param float $lng
	 * @param integer $foursquareId
	 * @param integer $distance
	 */
	public function searchLocation($lat, $lng, $foursquareId = '', $distance = '') {
	    $endpointUrl = sprintf($this->_endpointUrls['locations_search'], $lat, $lng, $foursquareId, $distance, $this->readAccessToken());
	    $this->_initHttpClient($endpointUrl);
	    return $this->_getHttpClientResponse();
	}
	
}
?>