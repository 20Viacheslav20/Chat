<?php
require 'vendor/autoload.php';
//header('Content-type: text/plain; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

/*
$client = new \MongoDB\Client("mongodb+srv://admin:admin@cluster0.rj9z889.mongodb.net/");
$DB = $client->library_db;
$collection = $DB->appUser;

$filter = ['username' => 'Admin'];
$options = ['limit' => 2];

$cursor = $collection->find($filter, $options);

foreach ($cursor as $document) {
    $username = $document['username'];
    echo "Username: $username\n";
}
*/

session_start();


// Rest of your code handling the GET request
// Access the username sent in the GET request body
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['username'])) {
    $key = 'bibaDavida'; 
    $encryptedUsername = $_GET['username'];

    $decodedUsername = '';
    for ($i = 0; $i < strlen($encryptedUsername); $i++) {
        $decodedUsername .= chr(ord($encryptedUsername[$i]) ^ ord($key[$i % strlen($key)]));
    }

    $_SESSION['name'] = urldecode($decodedUsername);
}

if (isset($_GET['logout']))
{    
	
	//Simple exit message 
    $logout_message = "<div class='msgln'>
                            <span class='left-info'>User <b class='user-name-left'>". $_SESSION['name'] ."</b> 
                                has left the chat session.
                            </span>
                            <br>
                        </div>";

    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
	
	session_destroy();
	header("Location: index.php"); //Redirect the user 
}



?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Chat</title>
        <meta name="description" content="Chat" />
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
    <?php
    ?>
        <div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
                <p class="logout"><a id="exit" href="#" onclick="exitChat()">Exit Chat</a></p>
            </div>
            <div id="chatbox">
            <?php
            if(file_exists("log.html") && filesize("log.html") > 0){
                $contents = file_get_contents("log.html");          
                echo $contents;
            }
            ?>
            </div>
            <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" />
                <input name="submitmsg" type="submit" id="submitmsg" value="Send" onclick="sendMessage()"/>
            </form>
        </div>
        <script type="text/javascript">
            function sendMessage() {
                var usermsgInput = document.getElementById("usermsg");
                var clientmsg = usermsgInput.value;

                fetch("post.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "usermsg=" + encodeURIComponent(clientmsg)
                })
                .then(response => {

                    return response.text();
                })
                .then(data => {

                })
                .catch(error => {
                    console.error("Error:", error);
                });

       
                usermsgInput.value = "";
            }

            function exitChat() {
                var exit = confirm("Are you sure you want to end the session?");
                if (exit) {
                    fetch("index.php?logout=1")
                        .then(response => response.text())
                        .then(data => {

                            window.location = "http://localhost:3000/";
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                    
                }
            }

            setInterval(function() {
                var chatbox = document.getElementById("chatbox");
                fetch("log.html")
                    .then(response => response.text())
                    .then(html => {
                        chatbox.innerHTML = html;
                        //Auto-scroll 
                        chatbox.scrollTop = chatbox.scrollHeight;
                    })
                    .catch(error => {
                        console.error("Error fetching chat log:", error);
                    });
            }, 2500);
</script>
    </body>
</html>
<?php
?>