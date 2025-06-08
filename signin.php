<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="./css/style.css">
		<title>Document</title>
	</head>
	<body> 
        
        <div class="maincontent">
			<h5>You have Successfully signed up now login using your credentials!</h5>
		</div>
        
        <main>

            <form action="includes/loginhandler.php" method="post">
                <!-- USER Name -->			
                <label for="username">UserName:</label>
                <input type="text" id="username" name="username" placeholder="Joe34l" required>
                
                <label for="email">Email:</label>
                <input id="email" name="email" type="email" placeholder="johndoe@email.com" required>		
                
                <label for="password">Password:</label>
                <input id="password" name="password" type="password" placeholder="*******" required>		
                
                <button type="submit">Login</button>	
            </form>
            
        </main>
	</body>
</html>

