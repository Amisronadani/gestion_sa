<?php

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'assistant') {
    header("Location: idex_assistant.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Document</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <style>
          body{
            background-image:url('images/bgf_siege.jpg');
            padding:0px;
            font-family: Arial, sans-serif;
            margin-left: 0px;
            margin-right: 0px;
          }
          .header{
            padding:20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            border-radius:0px;
            margin:0;
            background-color:darkorange;
            display:flex;
            justify-content:space-around;
            margin-top:-8px;

          }
          #a{
            text-decoration:none;
            color:white;
            margin-left:5px;
          }
          .nav{
          background-color: #007bff;
          width:100%;
          position:absolute;
          display:flex;
          }
          img{
            width:120px;
            height:120px;
          }
          marquee{
            color:white;
            font-size:20px;
          }
          .nav a{
            color:white;
          }
         .menu{
          display:flex;
          justify-content:space-around;
         }


        #bout:hover {
                    color: white;
                }
                #bout {
                    background-color: #FFC0AB;
                    border-radius: 10px;
                    padding: 15px;
                }
            #bou:hover {
                    color: white;
                }
                
            p{
            color:orange;	
              
            }
            .logout {
            background-color: darkorange;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-left:370px;
            position:absolute;
        }
        .logout a{
           text-decoration:none;
        }
        .logout:hover {
            background-color: #c82333;
        }
		    .bien{
         padding:10%;
        }

        .user-circle {
            background-color: darkorange;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;            
            margin-left:100%;
            margin-top:50px;
            position:absolute;
          }
          .username-display p {
            color: white;
            margin-bottom:80px;
            font-size: 18px;
          }
        #cont {
              position: absolute;
              right: 20px; /* Position it 20px from the right side */
              bottom: 20px; /* Position it 20px from the bottom */
              width: 120px; /* Set a width for the circle */
              height: 120px; /* Set a height for the circle */
              background-color: darkorange; /* Pink background */
              border-radius: 50%; /* Make it circular */
              display: flex; /* Use flexbox to center content */
              align-items: center; /* Center items vertically */
              justify-content: center; /* Center items horizontally */
             }

        #cont a {
                color: white; /* White text */
                text-decoration: none; /* Remove underline from the link */
                font-weight: bold; /* Bold text */
               }
		</style>
          
      
</head>
<body>
    <div class="header">
      <div>
          <i class="fas fa-mail-bulk" style="color:white"></i><a href="info@bgf.com" id="a">info@bgf.com</a>
      </div>
      <div>
        <i class="fas fa-clock"style="color:white"></i> Heure du travail: 8.00am - 5.00pm
      </div>
      <div>
          <i class="fas fa-map-marker-alt" style="color:white" ></i> Boulevard de la Liberté, Bujumbura
      </div>
    </div>

      <div class="nav">
        <div id="font"><img src="images/logow_bgf.ico" alt="">
         <marquee behavior="" direction="left">Bienvenu dans le service d'assistance</marquee>
         </div> 

         <div class="menu">
            <div class="user-circle">
                <i class="fas fa-user" style="font-size: 30px; color: white;"></i>
            </div>
            <div class="username-display">
                <p>Bienvenue, <?= $_SESSION['username'] ?></p>
            </div>
            <div>
                <button class="logout"><a href="Logout.php">Deconnexion</a></button>
            </div>
        </div>
     </div>

     <div class="fas fa-arrow-right-arrow-left" id="cont">
      <a href="index_assistant.php"> Continue </a>
    </div>

    <div>
      <marquee direction="right"><p>Vous avez accès à l'espace d'assistant.</p></marquee>
    </div>

</body>
</html>