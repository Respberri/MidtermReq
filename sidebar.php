<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$_debug = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="sidebar">
        <img class="bmsi-logo" src="/images/bmsi-logo.png" alt="logo of bmsi">
        <h2>Welcome, <?=ucfirst($_SESSION['role']) . " - " . $_SESSION['username']?>!</h2>
        <div class="menu">

            <!-- admin sidebar -->
			<?php if ($_SESSION['role'] == 'admin' || $_debug): ?>
            <div class="item"><a href="dashboard.php"><i class="fa-solid fa-house"></i>Home</a></div>

            <div class="item"><a href="create_user.php"><i class="fa-solid fa-user-plus"></i>Create User</a></div>

            <div class="item"><a class="sub-btn"><i class="fa-solid fa-graduation-cap"></i>Students
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
            <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <div class="item"><a class="sub-btn"><i class="fa-solid fa-user-edit"></i>Manage Student
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
                    <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <a href="project.php" class="sub-item"><i class="fa-solid fa-address-book"></i>Student List</a>
                <a href="enroll_student.php" class="sub-item"><i class="fa-solid fa-user-check"></i>Enroll Students</a>
                    </div>
                </div>
                <a href="analytics.php" class="sub-item"><i class="fa-solid fa-trophy"></i>Academic Records</a>   

                </div>
            </div>

            <div class="item"><a class="sub-btn"><i class="fa fa-sitemap"></i>Academic Management
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
                <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <div class="item"><a class="sub-btn"><i class="fa-solid fa-list"></i></i>Manage Subjects
                    <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
                    <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <a href="create_subject.php" class="sub-item"><i class="fa-solid fa-circle-plus"></i>Create Subject</a>
                <a href="assign_student_subject.php" class="sub-item"><i class="fa-solid fa-list-check"></i>Assign Student Subject</a>
                <a href="assign_faculty_subject.php" class="sub-item"><i class="fa fa-user-tie"></i>Assign Faculty Subject</a>
                <a href="view_subject_list.php" class="sub-item"><i class="fa-solid fa-book-open-reader"></i>View Subjects</a>
                    </div>
                </div>
                <div class="item"><a class="sub-btn"><i class="fa fa-calendar-alt"></i></i>Curriculum Planning
            <i class="fa-solid fa-chevron-down drpdown"></i>
            </a>
                        <div class="sub-menu">
                <a href="" class="sub-item"></a>
                <a href="create_section.php" class="sub-item"><i class="fa-solid fa-table-cells-large"></i>Create Section</a>
                <a href="assign_section_subject.php" class="sub-item"><i class="fa-solid fa-align-left"></i>Assign Section Subject</a>
                        </div>
                    </div> 
                </div>
            </div>

            <div class="item"><a class="sub-btn"><i class="fa fa-chalkboard-teacher"></i>Faculty
				<i class="fa-solid fa-chevron-down drpdown"></i>
				    </a>
				        <div class="sub-menu">
					        <a href="" class="sub-item"></a>
					        <a href="manage_faculty.php" class="sub-item"><i class="fa-solid fa-id-badge"></i>Faculty List</a>
				        </div>
			</div>
			<?php endif; ?>

            <!-- faculty sidebar -->
			<?php if ($_SESSION['role'] == 'faculty' || $_debug): ?>
            <div class="item"><a class="sub-btn"><i class="fa fa-chalkboard"></i>Class
                <i class="fa-solid fa-chevron-down drpdown"></i>
                    </a>
                        <div class="sub-menu">
                            <a href="" class="sub-item"></a>
                            <a href="faculty_student_list.php" class="sub-item"><i class="fa-solid fa-address-book"></i>Student List</a>
                            <a href="view_faculty_section_subjects.php" class="sub-item"><i class="fa-solid fa-user-group"></i>Faculty Section Students</a>
                            <a href="#" class="sub-item"><i class="fa-solid fa-trophy"></i>Academic Records</a> 
                        </div>
            </div>

            <div class="item"><a class="sub-btn"><i class="fa-solid fa-comment-dots"></i>Student Interaction
                <i class="fa-solid fa-chevron-down drpdown"></i>
                    </a>
                        <div class="sub-menu">
                            <a href="" class="sub-item"></a>
                            <a href="#" class="sub-item"><i class="fa-solid fa-upload"></i>Assignments</a>
                            <a href="faculty_announcements.php" class="sub-item"><i class="fa fa-bullhorn"></i>Announcement</a>
                        </div>
            </div>
            
            
		

			<?php endif; ?>

            <!-- student sidebar -->
			<?php if ($_SESSION['role'] == 'student' || $_debug): ?>
            <div class="item"><a class="sub-btn"><i class="fa-solid fa-circle-user"></i>My Account
                <i class="fa-solid fa-chevron-down drpdown"></i>
                    </a>
                        <div class="sub-menu">
                            <a href="" class="sub-item"></a>
                            <a href="student_personal.php" class="sub-item"><i class="fa-solid fa-id-card"></i>Student Profile</a>
                            <a href="change_password.php" class="sub-item"><i class="fa-solid fa-lock"></i>Change Password</a>
                        </div>
            </div>

            <div class="item"><a class="sub-btn"><i class="fa fa-chalkboard"></i></>Class
                <i class="fa-solid fa-chevron-down drpdown"></i>
                    </a>
                    <div class="sub-menu">
                        <a href="" class="sub-item"></a>
                        <a href="student_announcement_view.php" class="sub-item"><i class="fa fa-bullhorn"></i>Class Announcement</a>
                        <a href="#" class="sub-item"><i class="fa-solid fa-upload"></i>Class Assignment</a>
                    </div>
            </div>

			<?php endif; ?>

            <div class="item"><a class="sub-btn"><i class="fa-solid fa-circle-info"></i>More
                <i class="fa-solid fa-chevron-down drpdown"></i>
                    </a>
                        <div class="sub-menu">
                            <a href="" class="sub-item"></a>
                            <a href="mission_vision.php" class="sub-item"><i class="fa-solid fa-lightbulb"></i>Mission & Vision</a>
                            <a href="members.php" class="sub-item"><i class="fa-solid fa-users-gear"></i>Developers</a>
                        </div>
            </div>

            <div class="item"><a href="logout.php"><i class="fa-solid fa-circle-left"></i>Logout</a></div>
        </div>
    </div>

    <!-- jquery for sub-menu toggle -->
    <script src="/js/jquery.js"></script>
    <script>
        $(document).ready(function(){
            $('.sub-btn').click(function(){
                $(this).next('.sub-menu').slideToggle();
                $(this).find('.dropdown').toggleClass('rotate');
            })
        })
    </script>
</body>
</html>