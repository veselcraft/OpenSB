<?php
include('lib/common.php');

$error = '';

if (isset($_POST['action'])) {
	$username = (isset($_POST['username']) ? $_POST['username'] : null);
	$email = (isset($_POST['email']) ? $_POST['email'] : null);
	$pass = (isset($_POST['pass1']) ? $_POST['pass1'] : null);
	$pass2 = (isset($_POST['pass2']) ? $_POST['pass2'] : null);
	if ($testNewLayout) {
	$displayName = (isset($_POST['displayName']) ? $_POST['displayName'] : null);
	}

	if (!isset($username)) $error .= __("Blank username.");
	if (!isset($email)) $error .= __("Blank email.");
	if (!isset($pass) || strlen($pass) < 6) $error .= __("Password is too short.");
	if (!isset($pass2) || $pass != $pass2) $error .= __("The passwords don't match.");
	if (!isset($displayName)) $error .= __("Blank display name.");
	if (result("SELECT COUNT(*) FROM users WHERE username = ?", [$username])) $error .= __("Username has already been taken. "); //ashley2012 bypassed this -gr 7/26/2021
	if (!preg_match('/[a-zA-Z0-9_]+$/', $username)) $error .= __("Username contains invalid characters (Only alphanumeric and underscore allowed)."); //ashley2012 bypassed this with the long-ass arabic character. -gr 7/26/2021
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error .= __("Email isn't valid."); //60% of squarebracket accounts (excluding poktube accs) have invalid emails. this isn't even fucking working. -gr 7/26/2021

	// hCaptcha verification
	if ($hCaptchaSiteKey && $hCaptchaSecret) {
		if (!hCaptcha($_POST['h-captcha-response'])) $error .= __("Incorrect CAPTCHA. Please try again.");
	}

	if ($error == '') {
		$token = bin2hex(random_bytes(32));
		if ($testNewLayout) { 
		//reminder: incomplete shit, do not implement on production until later in beta 1 or if "back to school" fucks it 
		// up (it's late-july, my august exisential crisis is soon), then beta 2. -gr 7/26/2021
			query("INSERT INTO users (username, password, email, token, joined, display_name) VALUES (?,?,?,?,?,?)",
			[$username,password_hash($pass, PASSWORD_DEFAULT), $email, $token, time(), $displayName]);
		} else {
			query("INSERT INTO users (username, password, email, token, joined) VALUES (?,?,?,?,?)",
			[$username,password_hash($pass, PASSWORD_DEFAULT), $email, $token, time()]);
		}

		setcookie('SBTOKEN', $token, 2147483647);

		redirect('./');
	}
}

$twig = twigloader();
if ($testNewLayout) {
	// DEV ONLY - display names aren't currently fully implemented!!!!!!!!!!
	// ok yes i know this would break sbnext (though i deleted gordon.php since
	// it could have been used to bypass hcaptcha registeration and was a placeholder) 
	// but the only person who remotely even gives a shit about sbnext is icanttellyou 
	// but he's only using it as an excuse of not having finalium become the default theme 
	// for squarebracket. this excuse is invalid as he makes excuses for not working on sbnext.
	//
	// sbnext is NOT the future of squarebracket, the only thing implemented is a main page and 
	// (SUPER IMCOMPLETE) watch page with super garbage css. that's somehow worse than the
	// fucking css used on subrocks (bhief why are you using a homestuck stan to fix your
	// shitty css INSTEAD OF STEALING GOOGLE'S OLD-ASS CSS FOR YOUR REVIVAL). if there's
	// no progress on sbnext by beta 2 (or beta 1 refresh if we're doing the milestone 2/alpha 3 
	// cycle bullshit) then i will scrub sbnext off the squarebracket codebase. i am not sorry.
	// 
	// -Gamerappa, july 26th, 2021, 11:11PM EST.
	echo $twig->render('register_displayname.twig', ['error' => $error]);
} else {
	echo $twig->render('register.twig', ['error' => $error]);
}
