<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "12345";
$database = "coursewares";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $firstName = $row['firstName'];
    $lastName = $row['lastName'];
    $role = $row['role'];
    $sectionId = $row['teacherCode'];
    $sectionName = fetchSectionName($conn, $sectionId);

    // Fetch students in the same section as the teacher
    $studentsInSameSection = fetchStudentsInSameSection($conn, $sectionId);
} else {
    $firstName = "Unknown";
    $lastName = "Unknown";
    $sectionName = "Unknown Section";
    $studentsInSameSection = array();
}
$studentCountQuery = "SELECT COUNT(*) AS student_count FROM users WHERE role = 'student'";
$studentCountResult = mysqli_query($conn, $studentCountQuery);

if ($studentCountResult) {
    $studentCountRow = mysqli_fetch_assoc($studentCountResult);
    $studentCount = $studentCountRow['student_count'];
} else {
    $studentCount = 0; // Default value if the query fails
}
$studentsInSameSection = fetchStudentsInSameSection($conn, $sectionId);
$totalStudentsCount = count($studentsInSameSection);

function fetchTotalSectionsCount($conn) {
    $totalSectionsCount = 0;
    $totalSectionsQuery = "SELECT COUNT(*) AS totalSectionsCount FROM section";
    $result = mysqli_query($conn, $totalSectionsQuery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalSectionsCount = $row['totalSectionsCount'];
    } else {
        // Handle the error or log it
        echo "Error fetching total sections count: " . mysqli_error($conn);
    }

    return $totalSectionsCount;
}
$totalSectionsCount = fetchTotalSectionsCount($conn);

function fetchSectionName($conn, $sectionId) {
    $sectionName = "Unknown Section";
    $sectionNameQuery = "SELECT section_name FROM section WHERE section_id = ?";
    $statement = mysqli_prepare($conn, $sectionNameQuery);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $sectionId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $sectionName);

        if (mysqli_stmt_fetch($statement)) {
        }

        mysqli_stmt_close($statement);
    }

    return $sectionName;
}
function fetchTotalModulesCount($conn) {
    $totalModulesCount = 0;
    $totalModulesQuery = "SELECT COUNT(*) AS totalModulesCount FROM topics"; // Replace 'your_modules_table' with your actual table name
    $result = mysqli_query($conn, $totalModulesQuery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalModulesCount = $row['totalModulesCount'];
    } else {
        
        // Handle the error or log it
        echo "Error fetching total modules count: " . mysqli_error($conn);
    }

    return $totalModulesCount;
}
$totalModulesCount = fetchTotalModulesCount($conn);

function fetchStudentsInSameSection($conn, $sectionId) {
    $students = array();
    $studentsQuery = "SELECT * FROM users WHERE role = 'student' AND section_id = ?";
    $statement = mysqli_prepare($conn, $studentsQuery);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $sectionId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }

        mysqli_stmt_close($statement);
    }

    return $students;
}

function countQuizzes($conn) {
    $countSql = "SELECT COUNT(*) as quiz_count FROM quizzes";
    $countResult = $conn->query($countSql);

    if ($countResult->num_rows > 0) {
        $count = $countResult->fetch_assoc()['quiz_count'];
        return $count;
    } else {
        return 0;
    }
}


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST["role"] == "student") {
                $username = $_POST["username"];
                $password = $_POST["password"];
                $studentFirstName = $_POST["studentFirstName"];
                $studentLastName = $_POST["studentLastName"];
                $sectionId = $_POST["section_id"];
                $role = $_POST["role"];
        
                // Perform the necessary database operations for adding a student user
                $sql = "INSERT INTO users (username, password, firstName, lastName, section_id, role) 
                VALUES ('$username', '$password', '$studentFirstName', '$studentLastName', '$sectionId', '$role')";
        
                if ($conn->query($sql) === TRUE) {

                    echo '<script>window.location = "tt.php#managestudent-container";</script>';
                    
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                // If the selected role is not "student," you can display an error message or take appropriate action.
                echo "Error: Only student users can be added.";
            }
        }?>




    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <style>
            body {
                margin: 0;   
                font-family:'Roboto', Arial, sans-serif;
                background-color: rgb(230,230,250); /* Set a background color for better visualization */
            }

            #header {
            background-color: #0D28D0;
            padding: 10px;
            color: #fff;
            display: flex;
            align-items: center; /* Align items vertically */
            justify-content: space-between; /* Add space between logo and clickable divs */
            gap: 5px;
            border-bottom: 1px solid white; /* Add a white bottom border */
            z-index: 2;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow to the header */
    }

            #header img {
                margin-right: -90px; /* Add some space to the right of the image */
            }

            #header div {
                margin-left:40px; /* Remove default margin */
            }

            #header h3, #header p {
                margin: 0; /* Remove default margin */
            }

            #header .hci {
                font-size: 10px;
            }

            #header div {
                cursor: pointer;
                margin-left: 40px; /* Add some space between the header elements */
            }

            #header .text {
                position:absolute;
                margin-left:43px;
                display: block;
            }
            

            .container {
            display: none;
            padding: 20px;
            border: 1px solid #ddd;
            margin: 10px 10px 10px 15px;
            background-color: rgb(255, 255, 255);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 3;
            position: absolute;
            max-width: 98%; /* Set the width to 100% */
            min-width: 300px; /* Set a minimum width to ensure usability */
        }


            #dashboard-container {
                display: block; /* Make the dashboard container visible by default */
            }

            #student-list-container {
            }

            #example-container {
            }

            #managequiz-container{
                
            }

            .psulogo {
                width: 40px;
                margin-right:10px;

            }

            .btmhead {
                background-color: #0D28D0;
                color: white;
                padding: 1px 15px 1px 15px;
                font-weight: bold;
                z-index: 2;
                position: relative; /* Add relative positioning */
            }
            .nas:hover{
                border-bottom: 2px solid white; /* Add a white bottom border */
                
            }
            .nas:active {
    border-bottom: 2px solid white !important;
}
            .nas.selected{
                border-bottom: 2px solid white; /* Add a white bottom border */
            }
            .nav{
                display: flex;
                justify-content: space-around;
    

                    }
                    @media screen and (max-width: 800px) {
            .nav {
                flex-direction: column;
                align-items: center;
            }

            .nav {
                width: 100%;
                
            }  
        }

        .button-container {
    display: flex;
    flex-direction: row;
}

.btn-edit,
.btn-delete {
    
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 3px;
    background-color: #fff;
    margin-right: 5px; /* Optional: Add margin between the buttons */
}
input,
select {
    width: 20%; /* This makes the input elements take up 100% of the container width */
    padding: 8px;
    margin-bottom: 16px;
    box-sizing: border-box;}

    .med-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .smol-container {
            width: 200px;
            height:200px;
            border: 2px solid black;
            border-radius: 8px;
            padding: 10px;
            margin: 10px;
            text-align: center;
        }

        .smol-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .smol-main {
            display: flex;
            align-items: center;
           
            margin-bottom: 10px;
        }
        .smol-main p{
            font-size:25px;
        }
        .smol-container button {
    background-color: #0E9F6E;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 5px 20px;
    margin: 5px 0;
    cursor: pointer;
}
.dashed {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 90px;
    background-image: url('images/computers.png');
    background-size: cover;
    margin-bottom: 50px;
    margin-right: 50px;
    background-position: right;
    background-size: 150px;
    background-repeat: no-repeat; 
    padding: 20px;
    background-color:#FFD966; 
    color:#00796B; 
    border: 2px solid transparent; 
    
    border-image-slice: 1; 
    border-radius: 15px ; 
}


.dashed h1, .dashed p {
    margin: 0; }
.bgcom{
    width:100px;
    
}
.nam{
    font-weight:bold;
    text-transform: uppercase;
}
.imgcount{  
    margin-right:40px;
}
.out{
    text-decoration:none;
    color:white;
    margin-left:-25px;
    margin-right:-20px;
}
.out:hover{
    color:white;
}
{
    display:flex;
    align-items:center; 
}
.form-container {
    

        
    }

    .styled-form {
        max-width: 400px;
        margin: 0 auto;
    }

    .styled-input,
    .styled-select,
    .styled-button {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s ease-in-out;
    }

    .styled-input:focus,
    .styled-select:focus {
        border-color: #4CAF50;
    }

    .styled-button {
        background-color: #4CAF50;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
    }

    .styled-button:hover {
        background-color: #45a049;
    }

    .styled-link {
            display: inline-block;
            margin-right:20px;
            padding: 10px 15px;
            text-decoration: none;
            color: #3498db; /* Blue color */
            background-color: #fff; /* White background */
            border: 2px solid #3498db; /* Blue border */
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s; /* Smooth transition on hover */
        }

        /* Hover effect */
        .styled-link:hover {
            background-color: #3498db; /* Blue color on hover */
            color: #fff; /* White text on hover */
        }
        </style>
    </head>
    <body>

        <div id="header">
            <img src="images\pangasinan-state-university-logo@1x.png" alt="" class="psulogo">
            <div class="text">
                <h3>COURSEWARE</h3>
                <p class="hci">HUMAN COMPUTER INTERACTION</p>
            </div>
           <!--  <a href="logout.php" class="mm"  style="font-size:15px; margin-left:-10px;">Logout</a> -->
            <div class="nav">
                <div onclick="showContainer('dashboard-container')"class="nas">DASHBOARD</div>
                <div onclick="showContainer('managestudent-container')"class="nas">ADD STUDENTS</div>
                <div onclick="showContainer('studentlist-container')"class="nas">STUDENTS LIST</div>
                <div onclick="showContainer('managesection-container')"class="nas">CLASS</div>  
                <div onclick="showContainer('managemodule-container')"class="nas">MODULES</div>
                <div onclick="showContainer('managequiz-container')"class="nas">ASSIGNMENTS</div>
                <div onclick="showContainer('managebank-container')"class="nas">TESTBANK</div>
                <div>|</div>
               
                <div class="nas"  style="margin-right:20px;"><a class="out" href="logout.php">Logout</a></div>
            </div>
           
        </div> 
        <div class="btmhead"><p>INSTRUCTOR'S DASHBOARD</p></div>
        
        <div id="dashboard-container" class="container">
       
        
        <div class="dashed">
        <p>WELCOME ,</p>
    <h1 class="nam"><?php echo $firstName . ' ' . $lastName; ?></h1>
  
</div>         
            <div class="med-container"> 
            <div class="smol-container" style="border-color:#0E9F6E;">
                    <div class="smol-head" >
                        <p>Manage Students</p>  <button onclick="toggleManageStudents()">Add</button>

                    </div>
                    <hr>
                    <div class="smol-main">
                        <img class="imgcount" src="images\icons8-auditorium.gif" alt="small-icon">  <p><?php echo $studentCount; ?></p>
                        
                    </div>
                    <p>No. of active users</p>

                </div>

            <div class="smol-container" style="border-color:#3F83F8;">
                    <div class="smol-head">
                        <p>View Student List</p> <button onclick="showContainer('studentlist-container')" style="background-color:#3F83F8">View</button>
                        
                    </div>
                    <hr>
                    <div class="smol-main">
                        <img class="imgcount" src="images\icons8-people-50.png" alt="small-icon"> <p><?php echo $totalStudentsCount; ?></p>
                        
                    </div>
                    <p>No. of your students</p>

                </div>
            <div class="smol-container" style="border-color:#FF5A1F;">
                    <div class="smol-head">
                        <p>Manage Section</p>  <button onclick="showContainer('managesection-container')" style="background-color:#FF5A1F" >Add</button>
                    </div>
                    <hr>
                    <div class="smol-main">
                        <img class="imgcount" src="images\icons8-system-information.gif" style="width:50px;" alt="small-icon"> <p><?php echo $totalSectionsCount; ?></p>
                        
                    </div>
                    <p>No. of sections.</p>

                </div>
            <div class="smol-container" style="border-color:#BA3D5D;">
                    <div class="smol-head">
                        <p>Manage Module</p> <button onclick="showContainer('managemodule-container')" style="background-color:#BA3D5D">Add</button>
                    </div>
                    <hr>
                    <div class="smol-main">
                        <img class="imgcount" src="images\icons8-book (1).gif" alt="small-icon"> <p><?php echo $totalModulesCount; ?></p>
                        
                    </div>
                    <p>No. of modules.</p>

                </div>
                <div class="smol-container" style="border-color:#6B275A;"  >
                    <div class="smol-head">
                        <p > Manage / View Quizzes</p>
                        <button onclick="showContainer('managequiz-container')" style="background-color:#6B275A">Add</button>
                    </div>
                    <hr>
                    <div class="smol-main">
                        <img class="imgcount" src="images\icons8-quiz.gif" alt="small-icon">
                        <p><?php echo countQuizzes($conn); ?></p>
                    </div>
                    <p>No. of quizzes.</p>
                    
                </div>

            </div>
        </div>
        <div id="managestudent-container" class="container" >
        <div class="form-container" >
        <form method="POST" action="tt.php" class="styled-form" >
        <div class="">
            <div class="" >
            <label for="username">Username</label>
            <input type="text"  class="form-control"name="username"  required>
           
            </div>
           <div class="" >
            <label for="password">Password</label>
            <input type="password" class="form-control" ame="password" placeholder="password" required>
           
            </div>
            <label for="role">Role:</label>
            <select  class="form-control" name="role" required onchange="showAdditionalFields(this.value)">
                <option value="student">Student</option>
            </select>
            </div>

            <!-- Additional fields for Student role -->
            <div id="studentFields" class="additional-fields">
                <div class="" >
                
                <input class="form-control" type="text" name="studentFirstName" placeholder="Student First Name" id="studentFirstName">
                
                </div>
                <div class="" >
                
                <input class="form-control" type="text" name="studentLastName"  placeholder="Student Last Name"id="studentLastName">
                </div>
                <label for="sectionSelect">Section:</label>
                <select class="form-control"  name="section_id" >
                    <?php
                    $sql = "SELECT * FROM section";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['section_id'] . '">' . $row['section_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

        <button type="submit" class="btn btn-success styled-button">+ Add User</button>
    </form>

        
    </div>
        </div>

        <div id="studentlist-container" class="container">
    <h2>Your Students</h2>

    <button onclick="openAddStudentModal()" class="styled-link" id="addstudentcon"> + Add Student</button>
    <table  class="table table-hover">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Section Name</th>
                <th>Scores</th>
                
            </tr>
        </thead>
        <tbody>
                <?php foreach ($studentsInSameSection as $student): ?>
            <tr>
                <td><?php echo $student['id']; ?></td>
                <td><?php echo $student['firstName']; ?></td>
                <td><?php echo $student['lastName']; ?></td>
                <td><?php echo fetchSectionName($conn, $student['section_id']); ?></td>
                <td><a href="scores.php?user_id=<?php echo $student['id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>

<div id="addStudentModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                <button type="button" class="btn-close"  data-dismiss="modal" aria-label="Close" onclick="closeAddStudentModal()">
                </button>
            </div>
            <div class="modal-body" >
                <!-- Your form for adding a student goes here -->
                <div class="form-container">
        <form method="POST" action="tt.php" >
            <div class="">
            <div class="" >
            <label for="username">Username</label>
            <input type="text"  class="form-control"name="username"  required>
           
            </div>
           <div class="" >
            <label for="password">Password</label>
            <input type="password" class="form-control" ame="password" placeholder="password" required>
           
            </div>
            <label for="role">Role:</label>
            <select  class="form-control" name="role" required onchange="showAdditionalFields(this.value)">
                <option value="student">Student</option>
            </select>
            </div>

            <!-- Additional fields for Student role -->
            <div id="studentFields" class="additional-fields">
                <div class="" >
                
                <input class="form-control" type="text" name="studentFirstName" placeholder="Student First Name" id="studentFirstName">
                
                </div>
                <div class="" >
                
                <input class="form-control" type="text" name="studentLastName"  placeholder="Student Last Name"id="studentLastName">
                </div>
                <label for="sectionSelect">Section:</label>
                <select class="form-control"  name="section_id" >
                    <?php
                    $sql = "SELECT * FROM section";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['section_id'] . '">' . $row['section_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success styled-button">Add User</button>
        </form>

        
    </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAddStudentModal() {
        document.getElementById('addStudentModal').style.display = 'block';
    }

    function closeAddStudentModal() {
        document.getElementById('addStudentModal').style.display = 'none';
    }
</script>

        
        <div id="managesection-container" class="container">
        
        <div class="form-container">
    <h2>Add Section</h2>
    <form method="POST" action="addsection.php">
        <label for="section_number">Section Number:</label>
        <input type="text" class="form-control"  style ="width:30%" name="section_number" id="section_number" required>
        <button type="submit" class="styled-link"> + Add section</button>
    </form>
    <h4>Section List</h4>
    <?php
   
    $servername = "localhost";
    $username = "root";
    $password = "12345";
    $dbname = "coursewares";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sectionQuery = "SELECT * FROM section";
    $sectionResult = $conn->query($sectionQuery);

    if ($sectionResult->num_rows > 0) {
         echo "<table class='table table-hover'>";
        echo '<tr><th>Section ID</th><th>Section Name</th></tr>';
        while ($section = $sectionResult->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $section["section_id"] . '</td>';
            echo '<td>' . $section["section_name"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo 'No sections found.';
    }
    $conn->close();
    ?>
</div>
    </div>
            
        </div>
        
        <div id="managemodule-container" class="container">
        <div class="addbutton"> 
                    <form action="newaddcontent.php" method="POST">
                        <button type="submit" name="hci1" class="styled-link"><i class="fas fa-plus" style="margin-right:15px;"></i>Add Content</button>
                    </form>    
                </div>
                    <h4>Content List</h4>      
            <?php
            $servername = "localhost"; 
            $username = "root"; 
            $password = "12345"; 
            $dbname = "coursewares"; 
            $conn = new mysqli($servername, $username, $password, $dbname);
      
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
      
            if (isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
                $topic_id = $_GET["delete"];
                $sql = "DELETE FROM topics WHERE topic_id = $topic_id";
                if ($conn->query($sql) === TRUE) {
                    echo "Topic deleted successfully.";
                } else {
                    echo "Error deleting topic: " . $conn->error;
                }
            }           
            $sql = "SELECT topic_id, topic_title, course_code FROM topics";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo "<table class='table table-hover'>";
                echo "<tr><th>Topic Title</th><th>Course Code</th><th>Edit</th><th>Delete</th><th>View</th><th>Add Subtopic</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["topic_title"] . "</td>";
                    echo "<td>" . $row["course_code"] . "</td>";
                
                    echo "<td><a href='edittopic.php?id=" . $row["topic_id"] . "' class=''><i class='fas fa-edit'></i> </a></td>";
                    echo "<td><a href='managetopic.php?delete=" . $row["topic_id"] . "' class=''><i class='fas fa-trash-alt'></i> </a></td>";
                    echo "<td><a href='viewsubtopics.php?topic_id=" . $row["topic_id"] . "' class='btn-view'><i class='fas fa-eye'></i> </a></td>";
                    echo "<td><a href='addtopic.php?topic_id=" . $row["topic_id"] . "' class='btn-add-subtopic'><i class='fas fa-plus'></i> </a></td>";
                    echo "</tr>";
                }
                echo "</table>";   
            } else {
                echo "No topics found.";
            }

            $conn->close();
            ?>
           
            
        </div>
         <div id="managequiz-container" class="container">
    <button id="toggleButton" class="styled-link"><i class="fas fa-plus" style="margin-right:15px;"></i>Create Quiz</button>
    <div id="quizContainer" style="display: none;" role="document">
        <form action="createquiz.php" method="POST" class="p-4 rounded shadow" id="createquiz">
            <button type="button" class="btn-close" onclick="closeQuizContainer()"></button>
            <h1 class="text-center mb-4">Create a New Quiz</h1>
            <div class="form-group">
                <label for="quiz_title">Quiz Title</label>
                <input type="text" class="form-control" id="quiz_title" name="quiz_title" required>
            </div>
            <div class="form-group">
                <label for="created_at">Created Date</label>
                <input type="date" class="form-control" id="created_at" name="created_at" required>
            </div>
            <div class="form-group">
                <label for="quiz_description">Quiz Description</label>
                <textarea class="form-control" id="quiz_description" name="quiz_description" required></textarea>
            </div>
            <div class="form-group">
                <label for="total_question">Total Questions</label>
                <input type="number" class="form-control" id="total_question" name="total_question" required>
            </div>
            <div class="form-group">
                <label for="section_id">Section</label>
                <select class="form-control" id="section_id" name="section_id">
                    <?php
                    $hostname = 'localhost';
                    $username = 'root';
                    $password = '12345';
                    $database = 'coursewares';
                    $connection = mysqli_connect($hostname, $username, $password, $database);
                    $sql = "SELECT * FROM section";
                    $result = mysqli_query($connection, $sql);
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['section_id']}'>{$row['section_name']}</option>";
                        }
                    } else {
                        echo "Error: " . mysqli_error($connection);
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="topic_id">Topic</label>
                <select class="form-control" id="topic_id" name="topic_id">
                    <?php
                    $sqlTopics = "SELECT * FROM topics";
                    $resultTopics = mysqli_query($connection, $sqlTopics);
                    if ($resultTopics) {
                        while ($rowTopic = mysqli_fetch_assoc($resultTopics)) {
                            echo "<option value='{$rowTopic['topic_id']}'>{$rowTopic['topic_title']}</option>";
                        }
                    } else {
                        echo "Error: " . mysqli_error($connection);
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Select Course</label>
                <select class="form-control" id="course_id" name="course_id" required>
                    <?php
                    $sqlCourses = "SELECT * FROM course_code";
                    $resultCourses = mysqli_query($connection, $sqlCourses);
                    if ($resultCourses) {
                        while ($rowCourse = mysqli_fetch_assoc($resultCourses)) {
                            echo "<option value='{$rowCourse['course_id']}'>{$rowCourse['course_name']}</option>";
                        }
                    } else {
                        echo "Error: " . mysqli_error($connection);
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mx-auto">Create Quiz</button>
        </form>
    </div>

    <script>
        function closeQuizContainer() {
            document.getElementById('quizContainer').style.display = 'none';
        }

        // Example function to show the container (this can be triggered by any event like a button click)
        function showQuizContainer() {
            document.getElementById('quizContainer').style.display = 'flex';
        }
    </script>
            <h4>Quiz List</h4>
    <table class="table table-hover">
        <tr>
            <th>Title</th>
            <th>Created At</th>
            <th>Description</th>
            <th>Total Questions</th>
            <th>Section</th>
            <th>Created By</th>
            <th>Scores</th>
            <th>Action</th>
        </tr>

    <style>

        
#quizContainer {
    width: 100%; 
    height: 100%; 
    margin: 0;
    padding: 20px; 
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    background-color: rgba(173, 216, 230, 0.7);
    position: fixed; 
    overflow-y: auto; 
    z-index: 9999; 
    backdrop-filter: blur(5px);
    top: 0; 
    left: 0; 
    right: 0; 
    bottom: 0; 
    
    
}
 backdrop-filter: blur(5px); 
#addStudentModal,#quizContaineqr::-webkit-scrollbar {
         width: 0; 
}

#addStudentModal{
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); 
    background-color: rgba(173, 216, 230, 0.7); 
    padding: 20px; 
    position: fixed; 
    overflow-y: scroll; 
    z-index: 9999; 
    backdrop-filter: blur(5px);
    height: 100%; 
}
#createquiz {
    background-color:white;
    height: 880px; 
    width: 50%;
    padding:10px; 
    border-radius: 10px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
    margin-left:300px ;
    
    
    
}
    </style>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "12345";
    $dbname = "coursewares";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['deleteQuiz']) && isset($_POST['quiz_id'])) {
        $quiz_id = $_POST['quiz_id'];

        $delete_quiz_sql = "DELETE FROM quizzes WHERE quiz_id = '$quiz_id'";
        if ($conn->query($delete_quiz_sql) === TRUE) {
            echo "Quiz deleted successfully.";
        } else {
            echo "Error deleting quiz: " . $conn->error;
        }
    }

    // Updated query to order by created_at in descending order
    $quizSql = "SELECT quizzes.*, users.firstName, users.lastName FROM quizzes
                JOIN users ON quizzes.created_by = users.username
                ORDER BY quizzes.created_at DESC";
    $quizResult = $conn->query($quizSql);

    if ($quizResult->num_rows > 0) {
        while ($quiz = $quizResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $quiz["quiz_title"] . "</td>";
            echo "<td>" . $quiz["created_at"] . "</td>";
            echo "<td>" . $quiz["quiz_description"] . "</td>";
            echo "<td>" . $quiz["total_question"] . "</td>";
            echo "<td>" . fetchSectionName($conn, $quiz['section_id']) . "</td>";

            // Combine firstName and lastName
            $createdBy = $quiz["firstName"] . ' ' . $quiz["lastName"];
        
            echo "<td>" . $createdBy . "</td>";
            echo "<td><a href='studentscores.php?quiz_id={$quiz["quiz_id"]}'>View</a></td>";

            echo "<td>
                <div class='button-container'>
                    <form method='POST' action='editQuiz.php'>
                        <input type='hidden' name='quiz_id' value='" . $quiz["quiz_id"] . "'>
                        <button type='submit' name='editQuiz' style='color: #0d6efd;' class='btn-edit'><i class='fas fa-edit'></i></button>
                    </form>
                    <form method='POST' action='deletequiz.php'>
                        <!-- Added onsubmit for confirmation -->    
                    <input type='hidden' name='quiz_id' value='" . $quiz["quiz_id"] . "'>
                    <button type='submit' name='deleteQuiz' style='color: #dc3545;' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this quiz?\");'><i class='fas fa-trash-alt'></i></button>
                </form>
            </div>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9'>No quizzes found.</td></tr>";
}

$conn->close();
?>
            <script>
                function confirmDelete() {
                    return confirm('Are you sure you want to delete this quiz?');
                }
</script>
    </table>
            
        </div>

  </div>
    </form>
    
       
    </div>

</div>
        

        </div>


        <div id="managebank-container" class="container">
    <a href='bank.php' class='styled-link'> + Multiple Question</a>
    <a href='true_or_false.php' class='styled-link'> + True or False</a>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>#managebank-container" method="GET" id="filterForm">
    <div >
        <label  for="select-course">Select Course:</label>
        <select   name="selected_course_id" id="select-course" onchange="document.getElementById('filterForm').submit()">
            <option value="">All</option>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "12345";
            $dbname = "coursewares";
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $course_id_sql = "SELECT DISTINCT course_id FROM questions";
            $course_id_result = $conn->query($course_id_sql);

            if ($course_id_result->num_rows > 0) {
                while ($row = $course_id_result->fetch_assoc()) {
                    $display_value = ($row['course_id'] == 1) ? 'HCI 1' : (($row['course_id'] == 2) ? 'HCI 2' : $row['course_id']);
                    $selected = isset($_GET['selected_course_id']) && $_GET['selected_course_id'] == $row['course_id'] ? 'selected' : '';
                    echo "<option value='" . $row['course_id'] . "' $selected>" . $display_value . "</option>";
                }
            }
            ?>
        </select>

        <label for="select-topic">Select Topic:</label>
        <select name="selected_topic" id="select-topic" onchange="document.getElementById('filterForm').submit()">
            <option value="">All</option>
            <?php
            $topic_sql = "SELECT DISTINCT title FROM subtopics";
            $topic_result = $conn->query($topic_sql);

            if ($topic_result->num_rows > 0) {
                while ($row = $topic_result->fetch_assoc()) {
                    $selected = isset($_GET['selected_topic']) && $_GET['selected_topic'] == $row['title'] ? 'selected' : '';
                    echo "<option value='" . $row['title'] . "' $selected>" . $row['title'] . "</option>";
                }
            }
            ?>
        </select>

    </div>
</form>
<style>

#filterForm {
    text-align: center; 
}

label {
    display: inline-block; 
    margin: 10px auto; 
    color: #333;
}

select {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
}
</style>

<script>
    document.getElementById('select-course').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('select-topic').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
</script>

    <?php
    $where_clause = "";
    $selected_course_id = isset($_GET["selected_course_id"]) ? $_GET["selected_course_id"] : "";
    if (!empty($selected_course_id)) {
        $where_clause = " WHERE q.course_id = '$selected_course_id'";
    }

    $selected_topic = isset($_GET["selected_topic"]) ? $_GET["selected_topic"] : "";
    if (!empty($selected_topic)) {
        if (!empty($where_clause)) {
            $where_clause .= " AND ";
        } else {
            $where_clause = " WHERE ";
        }
        $where_clause .= "s.title = '$selected_topic'";
    }

    $sql = "SELECT 
                row_number,
                question_id,
                topic_title,
                question_text,
                created_by,
                course_id
            FROM (
                SELECT 
                    ROW_NUMBER() OVER (ORDER BY q.question_id DESC) AS row_number,
                    q.question_id,
                    s.title AS topic_title, 
                    q.question_text, 
                    q.created_by,
                    q.course_id
                FROM questions q
                JOIN subtopics s ON q.topic_id = s.topic_id
                $where_clause
                GROUP BY q.question_id, q.question_text, q.created_by, q.course_id
            ) AS subquery
            ORDER BY question_id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='table table-hover'>";
        echo "<tr><th></th><th>Subtopic Title</th><th>Question Text</th><th>Created By</th><th>Action</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["row_number"] . "</td>";
            echo "<td>" . $row["topic_title"] . "</td>";
            echo "<td>" . $row["question_text"] . "</td>";
            echo "<td>" . $row["created_by"] . "</td>";
            /* echo "<td>" . $row["course_id"] . "</td>"; */
            echo "<td><a href='editquestion.php?question_id=" . urlencode($row["question_id"]) . "' class='btn-edit'><i class='fas fa-edit'></i></a> <a href='managetestbank.php?delete_question=" . urlencode($row["question_id"]) . "' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this question?\")'><i class='fas fa-trash'></i></a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No questions found.";
    }

    $conn->close();
    ?>
</div>             
<script>
            
            
            showContainer('dashboard-container');

            function showContainer(containerId) {
    // Hide all containers
    var containers = document.querySelectorAll('.container');
    containers.forEach(function (container) {
        container.style.display = 'none';
    });

    // Show the selected container
    var selectedContainer = document.getElementById(containerId);
    selectedContainer.style.display = 'block';
}
            function fetchSectionName($conn, $sectionId) {
    $sectionName = "Unknown Section";
    $sectionNameQuery = "SELECT section_name FROM section WHERE section_id = ?";
    $statement = mysqli_prepare($conn, $sectionNameQuery);

    if ($statement) {
        mysqli_stmt_bind_param($statement, 'i', $sectionId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_bind_result($statement, $sectionName);

        if (mysqli_stmt_fetch($statement)) {
        }

        mysqli_stmt_close($statement);
    }

    return $sectionName;
}

document.getElementById("toggleButton").addEventListener("click", function () {
        var quizContainer = document.getElementById("quizContainer");
        if (quizContainer.style.display === "none" || quizContainer.style.display === "") {
            quizContainer.style.display = "block";
            
        } else {
            quizContainer.style.display = "none";
        }
    });
    
    
    function toggleManageStudents() {
    var container = document.getElementById("managestudent-container");
    container.style.display = (container.style.display === 'none' || container.style.display === '') ? 'block' : 'none';
}



    // Check if the URL contains the anchor #managesection-container
    if (window.location.hash === '#managesection-container') {
        // Use JavaScript to show the managesection-container
        showContainer('managesection-container');
         if (history.replaceState) {
        history.replaceState(null, document.title, window.location.pathname + window.location.search);
    } else {
        
        window.location.hash = ''; 
    }   
    }


    if (window.location.hash === '#managestudent-container') {
    // Use JavaScript to show the managesection-container
    showContainer('managestudent-container');

    // Remove the hash from the URL
    if (history.replaceState) {
        history.replaceState(null, document.title, window.location.pathname + window.location.search);
    } else {
        
        window.location.hash = ''; 
    }
}

if (window.location.hash === '#managemodule-container') {
    // Use JavaScript to show the managesection-container
    showContainer('managemodule-container');

    // Remove the hash from the URL
    if (history.replaceState) {
        history.replaceState(null, document.title, window.location.pathname + window.location.search);
    } else {
        
        window.location.hash = ''; 
    }
}

if (window.location.hash === '#managebank-container') {
    // Use JavaScript to show the managesection-container
    showContainer('managebank-container');

    // Remove the hash from the URL
    if (history.replaceState) {
        history.replaceState(null, document.title, window.location.pathname + window.location.search);
    } else {
        
        window.location.hash = ''; 
    }
}

if (window.location.hash === '#studentlist-container') {
        // Use JavaScript to show the managesection-container
        showContainer('studentlist-container');
         if (history.replaceState) {
        history.replaceState(null, document.title, window.location.pathname + window.location.search);
    } else {
        
        window.location.hash = ''; 
    }   
    }
    
    

</script>


        </script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-Xg4pDAAiAELCtEkkY9LpZ60DzpBmce6hO9a7u09i6d4wCp5CV+BYLZt5PkrXs15n" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy5qGqr0Ld7uZIbd78J0iC4awW+EmaK6Ut" crossorigin="anonymous"></script>

    </body>
    </html>     
