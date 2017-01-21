<?php
try {
	require_once('Stripe/lib/Stripe.php');

	// Set your own stripe test or live secret key
	Stripe::setApiKey("sk_test_N277J5UCZr0y4Omz1j7ATuEY");

	//Create the customer . It contains Stripe token , Plan name that you created first time ,and payer email
   if(isset($_GET['Weekly'])){
	   $plan="Weekly";
	   $amount=15;
   }else if(isset($_GET['Bi-Weekly'])){
		$plan="Bi-Weekly";
		$amount=10;
   }else{
	   $plan="Monthly";
	   $amount=5;
   }

	$customer = Stripe_Customer::create(array(
	  "source" => $_POST['stripeToken'],
	  "plan" => $plan,
	  "email" => $_POST["stripeEmail"]

	  )
	);
	Stripe_charge::create(array(
		'customer' => $customer->id,
		'amount' => '1999',
		'currency' => 'usd',
		'description' => 'One-time setup fee'
	));


	echo '<h1> Thank you for your'.$plan.' Subscription ('.$amount.' '.$plan.') and one time setup fee of $19.99<br><br></h1>';

	if(isset($_POST['stripeEmail'])){
		$email=$_POST['stripeEmail'];
		$today=date("Y-m-d H:i:s");
		$message = '<html><body>';
		$message .= "<br>"." Hi there"."<br>";
		$message .= 'Thank you for your'.$plan.' Subscription ('.$amount.' '.$plan.') and one time setup fee of $19.99 <br><br>';
		$message .= 'houstonglasspickup.com'.'<br><br>';
		$message .= "</body></html>";
		$subject="Payment Invoice- 9stickers.com";
		$receiver_email=$_POST["stripeEmail"];
		$headers = "From: " .$receiver_email. "\r\n";
		$headers .= "Reply-To: ". $receiver_email . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$adminemail="glasspickupservice@gmail.com";
		mail($adminemail, $subject, $message, $headers);


		mail($email, $subject, $message, $headers);
		$servername = "localhost";
		$username = "test124";
		$password = "qurban124";
		$dbname = "mysubscriptions";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		// Check connection
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}

		$sql = "INSERT INTO subscriptions (plan, amount, email,date)
		VALUES ('$plan', '$amount', '$email','$today')";

		if (mysqli_query($conn, $sql)) {
			//echo "New record created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		}

		mysqli_close($conn);

	}
}
catch(Stripe_CardError $e) {
	$body = $e->getJsonBody();
}

?>
