<h1>Access to Google Sheets from Yii2</h1>

Example are using Google REST API. OAuth2 authorization and getting spreadsheet.

<h2>Register Google-project first</h2>

Follow the instructions - 
<a href='https://support.google.com/cloud/answer/6158849?hl=en&ref_topic=6262490'>
    Setting up OAuth 2.0
</a>.

<h2>Installation</h2>

Copy to corresponding directories of your app.

Change

CLIENT_ID,<br> 
CLIENT_SECRET,<br> 
REDIRECT_URI

in <code>frontend/controllers/GoogleController.php</code> on your own values.

Now you can view spreadsheets, if you have them, by http://yoursite.ru
