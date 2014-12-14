<?php
class CreateAccountPage extends Page {
	private $result;
	private $error;

	/**
	 * this method can write additional http headers, for example for cache control.
	 *
	 * @return true, to continue the rendering, false to not render the normal content
	 */
	public function writeHttpHeader() {
		if (strpos(STENDHAL_LOGIN_TARGET, 'https://') !== false) {
			if (!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS'] != "on")) {
				header('Location: '.STENDHAL_LOGIN_TARGET.rewriteURL('/account/create-account.html'));
				return false;
			}
		}
		if (isset($_SESSION['account'])) {
			header('Location: '.STENDHAL_LOGIN_TARGET.rewriteURL('/account/mycharacters.html'));
			return false;
		}

		return $this->process();
	}

	function process() {
		global $protocol;
		if (!$_POST) {
			return true;
		}
		if (!$_POST['name'] || !$_POST['pw'] || !$_POST['pr']) {
			$this->error = 'One of the mandatory fields was empty.';
			return true;
		}

		if ($_POST['csrf'] != $_SESSION['csrf']) {
			$this->error = 'Session information was lost.';
			return true;
		}

		if ($_POST['pw'] != $_POST['pr']) {
			$this->error = 'Your password and repetition do not match.';
			return true;
		}

		$user = strtolower($_POST['name']);
		require_once('scripts/pharauroa/pharauroa.php');
		$clientFramework = new PharauroaClientFramework(STENDHAL_MARAUROA_SERVER, STENDHAL_MARAUROA_PORT, STENDHAL_MARAUROA_CREDENTIALS);
		$template = new PharauroaRPObject();
		$template->put('outfit', $_REQUEST['outfitcode']);

		$this->result = $clientFramework->createAccount($user, $_POST['pw'], $_POST['email']);

		if ($this->result->wasSuccessful()) {
			// on success: login and redirect to character creation
			header('HTTP/1.0 301 Moved permanently.');
			header("Location: ".$protocol."://".$_SERVER['HTTP_HOST'].preg_replace("/&amp;/", "&", rewriteURL('/account/create-character.html')));
			$_SESSION['account'] = Account::readAccountByName($user);
			PlayerLoginEntry::logUserLogin($_POST['name'], $_SERVER['REMOTE_ADDR'], null, true);
			$_SESSION['marauroa_authenticated_username'] = $_SESSION['account']->username;
			$_SESSION['csrf'] = createRandomString();
			return false;
		} else {
			return true;
		}
	}

	public function writeHtmlHeader() {
		echo '<title>Create Account'.STENDHAL_TITLE.'</title>';
		echo '<meta name="robots" content="noindex">'."\n";
	}

	function writeContent() {
		$this->show();
	}

	function show() {
		if ($this->error || (isset($this->result) && !$this->result->wasSuccessful())) {
			startBox("<h1>Error</h1>");
			if ($this->error) {
				echo '<span class="error">'.htmlspecialchars($this->error).'</span>';
			} else {
				echo '<span class="error">'.htmlspecialchars($this->result->getMessage()).'</span>';
			}
			endBox();
		}

		$_SESSION['csrf'] = createRandomString();
		startBox("<h1>Create Account</h1>");
?>

<form id="createAccountForm" name="createAccountForm" action="" method="post"> <!-- onsubmit="return createAccountCheckForm()" -->
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf'])?>">

<table>
<tr>
<td><label for="name">Name:<sup>*</sup> </label></td>
<td><input id="name" name="name" value="<?php if (isset($_REQUEST['name'])) {echo htmlspecialchars($_REQUEST['name']);}?>" type="text" maxlength="20" ></td>
<td><div id="namewarn" class="warn"></div></td>
</tr>

<tr>
<td><label for="pw">Password:<sup>*</sup> </label></td>
<td><input id="pw" name="pw" type="password"></td>
<td><div id="pwwarn" class="warn"></div></td>
</tr>

<tr>
<td><label for="pr">Password Repeat:<sup>*</sup> </label></td>
<td><input id="pr" name="pr" type="password"></td>
<td><div id="prwarn" class="warn"></div></td>
</tr>

<tr>
<td><label for="email">E-Mail: </label></td>
<td><input id="email" name="email" value="<?php if (isset($_REQUEST['email'])) {echo htmlspecialchars($_REQUEST['email']);}?>" type="email" maxlength="50"></td>
<td><div id="emailwarn" class="warn"></div></td>
</tr>
</table>

<input id="serverpath" name="serverpath" type="hidden" value="<?php echo STENDHAL_FOLDER;?>">
<input name="submit" style="margin-top: 2em" type="submit" value="Create Account">
</form>
<?php 

endBox();

echo '<br>';

startBox("<h2>External Account</h2>");
?>
	<form id="openid_form" action="<?php echo STENDHAL_FOLDER;?>/?id=content/account/login" method="post">
		<input id="oauth_version" name="oauth_version" type="hidden">
		<input id="oauth_server" name="oauth_server" type="hidden">

		<div id="openid_choice">
			<p>Do you already have an account on one of these sites?</p>
			<div id="openid_btns"></div>
		</div>

		<div>
			<noscript>
				<p>OpenID is a service that allows you to log on to many different websites using a single identity.</p>
			</noscript>
		</div>
		<table id="openid-url-input">
		<tbody><tr>
			<td class="vt large">
				<input id="openid_identifier" name="openid_identifier" class="openid-identifier" style="height: 28px" tabindex="100" type="text">
			</td>

			<td class="vt large">
				<input id="submit-button" style="margin-left: 5px; height: 36px;" value="Log in" tabindex="101" type="submit">
			</td>
		</tr></tbody>
		</table>
		<?php 
		if (isset($_REQUEST['url'])) {
			echo '<input type="hidden" name="url" value="'.htmlspecialchars($_REQUEST['url']).'">';
		}
	
		if (isset($this->openid->error)) {
			echo '<div class="error">'.htmlspecialchars($this->openid->error).'</div>';
		}

?>
	</form>
<?php

		endBox();
?>

<br>
<?php startBox("<h2>Logging and privacy</h2>");?>
<p>
<font size="-1">On login information which identifies your computer on the internet will be 
logged to prevent abuse (like many attempts to guess a password in order to
hack an account or creation of many accounts to cause trouble).</font></p>

<p><font size="-1">
Furthermore all events and actions that happen within the game-world 
(like solving quests, attacking monsters) are logged. This information is 
used to analyse bugs and in rare cases for abuse handling.</font></p>
<?php 
		endBox();
	}
	
}

$page = new CreateAccountPage();
