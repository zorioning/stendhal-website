<?php

require_once('lib/openid/lightopenid.php');


class OpenidPage extends Page {
	private $error;

	public function writeHttpHeader() {
		if (!isset($_GET['openid_mode'])) {
			if (isset($_POST['openid_identifier'])) {
				$openid = new LightOpenID;
				$openid->identity = $_POST['openid_identifier'];
				$openid->required = array('contact/email', 'namePerson/friendly');
				$openid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
				$openid->returnUrl = $openid->realm . $_SERVER['REQUEST_URI'];
				try {
					header('Location: ' . $openid->authUrl());
					return false;
				} catch (ErrorException $e) {
					$this->error = $e->getMessage();
				}
			}
		}
		return true;
	}

	public function writeHtmlHeader() {
		echo '<meta name="robots" content="noindex">'."\n";
		echo '<title>Openid'.STENDHAL_TITLE.'</title>';
		?><style type="text/css">
#openid_choice{
	margin-top:10px; display:none;
}
#openid_input_area{
	padding-top:10px; margin-bottom:30px; margin-left:35px; clear:both;
}
#openid_username{margin-right:5px;}
#openid_btns, #openid_btns br{
	margin-left:30px; clear:both;
}
#openid_highlight{
	padding:3px; background-color:#FFFCC9; float:left;
}
.openid_large_btn{
	width:100px; height:60px; border:2px solid #DDD; border-right:2px solid #ccc; border-bottom:2px solid #ccc; margin:3px; float:left;-moz-border-radius:5px;-webkit-border-radius:5px;box-shadow:2px 2px 4px #ddd;-moz-box-shadow:2px 2px 4px #ddd;-webkit-box-shadow:2px 2px 4px #ddd;
}
.openid_large_btn:hover{
	margin:4px 0 0 6px;border:2px solid #999;box-shadow:none;-moz-box-shadow:none;-webkit-box-shadow:none;
}
.openid_small_btn{
	width:24px;height:24px;border:2px solid #DDD;border-right:2px solid #ccc;border-bottom:2px solid #ccc;margin:3px;float:left;-moz-border-radius:5px;-webkit-border-radius:5px;box-shadow:2px 2px 4px #ddd;-moz-box-shadow:2px 2px 4px #ddd;-webkit-box-shadow:2px 2px 4px #ddd;
}
.openid_small_btn:hover{margin:4px 0 0 6px;border:2px solid #999;box-shadow:none;-moz-box-shadow:none;-webkit-box-shadow:none;
}
a.openid_large_btn:focus{
	outline:none;
}
a.openid_large_btn:focus{
	-moz-outline-style:none;
}
.openid_selected{
	border:4px solid #DDD;
}
	</style>
	<script src="/css/jquery-00000001.js" type="text/javascript"></script>
	<script src="/css/openid-00000001.js" type="text/javascript"></script>
		<?php 
	}

	function writeContent() {



		try {
			if (!isset($_GET['openid_mode'])) {
				startBox("Open ID");
?>

	<form id="openid_form" action="" method="post">
		<input id="oauth_version" name="oauth_version" type="hidden">
		<input id="oauth_server" name="oauth_server" type="hidden">

		<div id="openid_choice">
			<p>Do you already have an account on one of these sites?:</p>
			<div id="openid_btns"></div>
		</div>

		<div id="openid_input_area"></div>
		<div>
			<noscript>
				<p>OpenID is a service that allows you to log on to many different websites using a single identity.</p>
			</noscript>
		</div>

		<p>Or, you can manually enter your OpenID</p>
		<table id="openid-url-input">
		<tbody><tr>
			<td class="vt large">
				<input id="openid_identifier" name="openid_identifier" class="openid-identifier" style="height: 28px; width: 450px;" tabindex="100" type="text">
			</td>

			<td class="vt large">
				<input id="submit-button" style="margin-left: 5px; height: 36px;" value="Log in" tabindex="101" type="submit">
			</td>
		</tr></tbody>
		</table>
	</form>

	<script type="text/javascript">
		$().ready(function() {
			openid.init('openid_identifier');
		});
	</script>

<?php

	if (isset($this->error)) {
		echo '<div class="error">'.htmlspecialchars($this->error).'</div>';
	}

		endBox();
			} elseif($_GET['openid_mode'] == 'cancel') {
				echo 'User has canceled authentication!';
			} else {
				startBox("Result");
				$openid = new LightOpenID;
				echo 'Validate: ' . $openid->validate() . '<br>';
				echo 'Identity: ' . htmlspecialchars($openid->identity) . '<br>';
				$attributes = $openid->getAttributes();
				echo 'E-Mail: ' . htmlspecialchars($attributes['contact/email']);
				echo 'Nickname: ' . htmlspecialchars($attributes['namePerson/friendly']);
				endBox();
			}
		} catch(ErrorException $e) {
			echo $e->getMessage();
		}

		endBox();
	}
}
$page = new OpenidPage();