    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "12345";
    $database = "coursewares";

    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Verify username and password
        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {
            // Fetch the user's role
            $row = mysqli_fetch_assoc($result);
            $role = $row['role'];

            // Set session variables
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on the user's role
            if ($role === 'admin') {
                header("Location: admin.php"); // Redirect to admin.php
                exit();
            } elseif ($role === 'student') {
                header("Location: newstudent.php"); // Redirect to student.php
                exit();
            } elseif ($role === 'teacher') {
                header("Location: tt.php"); // Redirect to teacher.php
                exit();
            }
        } else {
            // Authentication failed
            $errorMessage = "Invalid username or password.";
        }

        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($conn);
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Design</title>
    <link rel="stylesheet" href="logi.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Proxima+Nova:wght@400;700&display=swap');

body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(45deg, #8a2be2, #4169e1);
    font-family: "Proxima Nova", sans-serif;
}


.mcnt {
    display: flex;
    width: 1000px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.xd {
    flex: 1;

}

.container {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    /* padding: 20px; */
}

.form-container {
    width: 100%;
}

.form-group {
    margin-bottom: 15px;
   
}

.button-container {
    text-align: center;
}

.im {
    max-width: 100%;
    height: auto;
    width: 100%;
    object-fit: cover;
}

.logo-container {
    display: flex;
    flex-direction: column;
    /* text-align: center; */
    margin-bottom: 20px;
    margin-top: -10px;
}
.asd{
    width: 500px;
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    box-shadow: 10px 0 10px rgba(63, 62, 62, 0.1);

}
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity:;
    
  }
  
  .logo-container {
    display: flex;
    text-align: center;
    margin-bottom: 20px;
    align-items: center;
    margin-top: 70px;
  }
  
  .logo {
    width: 70px; /* Adjust the width as needed */
    margin-right: 10px;
  }
  
  h4{
    
  font-weight: bold;
  color: #333;
  margin-bottom: 25px;  
  text-transform: uppercase;
  border-bottom: 2px solid black;
  
  
  }
 
  
  .form-container {
    /* background-color: #f2f2f2; */
    /* padding: 20px; */
    border-radius: 5px;
    width: 300px; /* Adjust the width as needed */
  }
  
  .error {
    color: red;
    margin-bottom: 10px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
  }
  
  input[type="text"],
  input[type="password"] {
    width: 100%;
    /* padding: 10px; */
    border-radius: 3px;
    border: 1px solid #ccc;
  }
  
  .button-container {
    text-align: center;
    
  }
  
  
  .as{
    display: flex;
    align-items: center;
  }
  .form-group {
    margin-bottom: 20px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
  }
  
  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 10px;
    border: none;
    border-bottom: 1px solid #ccc;
    background-color: transparent;
  }
  
  input[type="text"]::placeholder,
  input[type="password"]::placeholder {
    color: #999;
  }
  
 
  
  button:hover {
    background-color: #45a049;
  }
  button {
    padding: 10px 35px;
    background-color: rgb(125, 83, 217);
    color: #fff;
    border: none;
    border-radius: 9999px !important;
    cursor: pointer;
    font-weight: bold;
  }
  .wb{
    padding: 10px 17px;
    background-color: rgb(125, 83, 217);
    color: #fff;
    border: none;
    border-radius: 20px;
    margin-left:-333px;
    border-top-left-radius: 0;
    font-size: 20px;
  }
      .hh{
          font-size:50px;
          color:  #00008B  ;

        }
        .hhS{
          margin-right:-70px;
          margin-top: -20px;
          font-size:15px;
          color:  #00008B ;
          font-weight:normal;
        }
        .wb{
          background-color: #00008B ;
        }
        .xds{
          background-color: #00008B ;

        }


        .input-wrapper {
  position: relative;
}

input[type="text"] {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  padding-left:45px; /* Adjust the value to make space for the image */
}
input[type="password"] {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  padding-left:45px;
}
.input-icon1 {
  position: absolute;
  top: 50%;
  left: 10px;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  background-image: url(images/usernamee.png); /* Adjust the image path */
  background-repeat: no-repeat;
  background-size: cover;
}
.input-icon2{
  position: absolute;
  top: 50%;
  left:10px;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  background-image: url(images/password.png); /* Adjust the image path */
  background-repeat: no-repeat;
  background-size: cover;
}
  </style>
</head>

<body>
<div class="mcnt">
   
  <div class="xd">
    <img  src="images/32914.jpg" class="asd" alt="" class="im">
  </div>
  <div class="container">
  <h3 class="wb">Welcome back!</h3>
    <div class="logo-container">
    <div class ="as">
    <img  src="images/pangasinan-state-university-logo@1x.png" alt="" class="logo">
    <h4 class="hh">COURSEWARE</h4>
    </div>  
      <h4 class="hhS">HUMAN COMPUTER INTERACTION</h4>
    </div>
    <div class="form-container">
      <?php
      if (isset($errorMessage)) {
          echo '<p class="error">' . $errorMessage . '</p>';
      }
      ?>
      <form method="POST" action="login.php">
      
        <div class="form-group">
        
          <label for="username"></label>
          
          <div class="input-wrapper">
  <input type="text" name="username" placeholder="Enter your username" required>
  <span class="input-icon1"></span>
</div>
        </div>
        <div class="form-group">
          
          <label for="password"></label>
          
          <div class="input-wrapper">
  <input type="password" name="password" placeholder="Enter your password" required>
  <span class="input-icon2"></span>
</div>
        </div>
        <div class="button-container">
          <button class="xds" type="submit">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>

