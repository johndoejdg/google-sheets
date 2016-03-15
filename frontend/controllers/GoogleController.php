<?php
/**
* @author sergmoro1@ya.ru
* @license MIT
* 
* Google Sheets autorization (OAuth2).
* Getting a list of user (registered in a Google) spreadsheets.
* 
*/

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;

class GoogleController extends Controller
{
	// create a project first - https://console.developers.google.com/project
    const CLIENT_ID = 'google cliendt id';
    const CLIENT_SECRET = 'google secret code';
    // allowed operations
	const SCOPE = 'https://spreadsheets.google.com/feeds/';
	// dev mode
	const REDIRECT_URI = 'http://localhost/yoursite/frontend/web/google/index';
	// production mode
	//const REDIRECT_URI = 'http://yoursite.ru/google/index';
    
	protected function curl($options)
	{
        $ch = curl_init();
		curl_setopt_array($ch, $options);
		$http_data = curl_exec($ch);
		curl_close($ch);
		return $http_data;
	}
	
	protected function getAuthorizationCode()
	{
		$url = 'https://accounts.google.com/o/oauth2/v2/auth';

		$params = [
			'response_type' => 'code',
			'access_type' => 'offline', // the app needs to use Google API in the background
			'approval_prompt' => 'force',
			'client_id' => self::CLIENT_ID,
			'redirect_uri' => self::REDIRECT_URI,
			'scope' => self::SCOPE,
		];

		$this->redirect($url . '?' . http_build_query($params));
	}

	protected function getAccessToken($code)
	{
		$url = "https://accounts.google.com/o/oauth2/token";
		
		$http_data = $this->curl([
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => [
				'code' => $code,
				'client_id' => self::CLIENT_ID,
				'client_secret' => self::CLIENT_SECRET,
				'redirect_uri' => self::REDIRECT_URI,
				'grant_type' => 'authorization_code'
			],
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true
		]);

		$response = json_decode($http_data);
		
		if(isset($response->refresh_token)) {
			// Refresh tokens are for long term user and should be stored
			// They are granted first authorization for offline access
			file_put_contents("../runtime/GmailToken.txt", $response->refresh_token);
		}
		
		// Kep access token
		$session = Yii::$app->session;
		$session->set('access_token', $response->access_token);
		$session->close();
		
		$this->redirect(['google/index']);
	}

    public function actionIndex($code = null)
    {
		if($code) {
			// Exchange Authorization Code for OAuth Token
			$this->getAccessToken($code);
		} else {
			$session = Yii::$app->session;
			if($access_token = $session->get('access_token')) {
				$url = "https://spreadsheets.google.com/feeds/spreadsheets/private/full";
			
				$http_data = $this->curl([
					CURLOPT_POST => false,
					CURLOPT_URL => $url . "?access_token=$access_token",
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_RETURNTRANSFER => true
				]);
				$xml = simplexml_load_string($http_data);
				$sheets = $this->getSheets($xml);
				
				$dataProvider = new ArrayDataProvider([
					'allModels' => $sheets,
					'pagination' => [
						'pageSize' => 5,
					],
					'sort' => [
						'attributes' => ['updated_at'],
					],
				]);
				list($title, $email) = explode('-', $xml->title);
				return $this->render('index', [
					'dataProvider' => $dataProvider,
					'title' => $title,
					'owner' => $email,
				]);
			} else {
				$this->getAuthorizationCode();
			}
		}
    }
    
    private function getSheets($xml)
    {
		$a = [];
		for($i=0; $i<count($xml->entry); $i++)
		{
			$b = [];
			$entry = $xml->entry[$i];
			$b['id'] = substr($entry->id, strrpos($entry->id, '/') + 1);
			$b['title'] = $entry->title;
			$b['updated_at'] = strtotime($entry->updated);
			$b['editable'] = substr($entry->link[0]['href'], -4) == 'full' ? 1 : 0;
			$b['author'] = $entry->author->name;
			$b['email'] = $entry->author->email;
			$a[$i] = $b;
		}
		return $a;
	}
	
    public function actionView($id)
    {
		return $this->render('view', ['id' => $id]);
	}
}
