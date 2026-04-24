<?php
require_once 'build/inc/db.php';

// --- Configuration ---
$password = password_hash('password123', PASSWORD_DEFAULT);

echo "Seeding Users...\n";
$teacher_ids = [];
$student_ids = [];

// Seed Teachers
$teachers = ['safyan1', 'safyan2', 'safyan3'];
foreach ($teachers as $name) {
    $email = $name . "@teachmate.com";
    $conn->query("INSERT IGNORE INTO users (name, email, password, role, verify_status) VALUES ('$name', '$email', '$password', 'teacher', 'approved')");
    $res = $conn->query("SELECT id FROM users WHERE email='$email'");
    $teacher_ids[] = $res->fetch_assoc()['id'];
}

// Seed Students
for ($i = 1; $i <= 10; $i++) {
    $name = "muthair" . $i;
    $email = $name . "@teachmate.com";
    $conn->query("INSERT IGNORE INTO users (name, email, password, role, verify_status) VALUES ('$name', '$email', '$password', 'student', 'approved')");
    $res = $conn->query("SELECT id FROM users WHERE email='$email'");
    $student_ids[] = $res->fetch_assoc()['id'];
}

// --- Clear existing dummy data ---
echo "Cleaning up existing courses...\n";
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DELETE FROM courses");
$conn->query("DELETE FROM feedback");
$conn->query("DELETE FROM assignments");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");


echo "Seeding Categories...\n";
$categories = ['Web Development', 'Graphic Design', 'Digital Marketing', 'Data Science', 'Business', 'Cyber Security', 'UI/UX Design'];
$cat_map = [];
foreach ($categories as $cat) {
    $conn->query("INSERT IGNORE INTO categories (name) VALUES ('$cat')");
    $res = $conn->query("SELECT id FROM categories WHERE name='$cat' LIMIT 1");
    $cat_map[$cat] = $res->fetch_assoc()['id'];
}

echo "Seeding Courses...\n";
$course_ids = [];
$course_data = [
    [
        'title' => 'Complete Web Development Bootcamp',
        'desc' => 'Master HTML, CSS, JavaScript, PHP, and MySQL from scratch.',
        'hours' => '45',
        'img' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Web Development'
    ],
    [
        'title' => 'Graphic Design Masterclass',
        'desc' => 'Learn Photoshop, Illustrator, and Figma to create stunning designs.',
        'hours' => '30',
        'img' => 'https://images.unsplash.com/photo-1558655146-d09347e92766?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Graphic Design'
    ],
    [
        'title' => 'Digital Marketing Strategy 2026',
        'desc' => 'Master SEO, SEM, Social Media, and Email Marketing.',
        'hours' => '25',
        'img' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Digital Marketing'
    ],
    [
        'title' => 'Python for Data Science',
        'desc' => 'Learn Python, Pandas, NumPy, and Matplotlib for data analysis.',
        'hours' => '40',
        'img' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Data Science'
    ],
    [
        'title' => 'UI/UX Design Advanced Bundle',
        'desc' => 'Master user research, wireframing, prototyping and high-fidelity design.',
        'hours' => '35',
        'img' => 'https://images.unsplash.com/photo-1541462608141-c758715b3a58?auto=format&fit=crop&q=80&w=800',
        'cat' => 'UI/UX Design'
    ],
    [
        'title' => 'Mobile App Development (Flutter)',
        'desc' => 'Build beautiful native apps for iOS and Android with a single codebase.',
        'hours' => '50',
        'img' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Web Development'
    ],
    [
        'title' => 'AI & Machine Learning Foundations',
        'desc' => 'Understand the basics of AI, neural networks, and deep learning.',
        'hours' => '60',
        'img' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Data Science'
    ],
    [
        'title' => 'Business Leadership & Mgmt',
        'desc' => 'Develop critical skills for managing teams and leading organizations.',
        'hours' => '20',
        'img' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Business'
    ],
    [
        'title' => 'Cyber Security Essentials',
        'desc' => 'Learn to protect networks and systems from digital attacks.',
        'hours' => '45',
        'img' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Cyber Security'
    ],
    [
        'title' => 'Social Media Management Pro',
        'desc' => 'Learn content creation, scheduling, and community management.',
        'hours' => '15',
        'img' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=800',
        'cat' => 'Digital Marketing'
    ]

];


foreach ($course_data as $c) {
    $title = $conn->real_escape_string($c['title']);
    $desc = $conn->real_escape_string($c['desc']);
    $img = $conn->real_escape_string($c['img']);
    $hours = $c['hours'];
    $cat_name = $c['cat'];
    $cat_id = $cat_map[$cat_name];
    
    $conn->query("INSERT INTO courses (title, short_description, video_hours, articles, resources, assignments, certificate, full_description, instructor_name, instructor_designation, overview, what_you_will_learn, thumbnail, category_id) 
                  VALUES ('$title', '$desc', '$hours', 10, 5, 2, 'Yes', '$desc', 'Safyan Expert', 'Senior Instructor', 'Comprehensive course overview...', 'Skills you will gain...', '$img', $cat_id)");
    $course_ids[] = $conn->insert_id;
}


echo "Seeding Assignments...\n";
if (!empty($course_ids) && !empty($teacher_ids)) {
    $now = date('Y-m-d H:i:s');
    $conn->query("INSERT INTO assignments (title, filename, filesize, uploaded_by, course_id, uploaded_at) VALUES ('Project Proposal Guide', 'proposal.pdf', 102400, " . $teacher_ids[0] . ", " . $course_ids[0] . ", '$now')");
}


echo "Seeding Feedback...\n";
if (count($course_ids) >= 2 && count($student_ids) >= 3) {
    $feedbacks = [
        ['user_id' => $student_ids[0], 'course_id' => $course_ids[0], 'category' => 'Teacher', 'rating' => 5, 'comments' => 'Safyan is an amazing teacher! The way he explains complex topics is very easy to understand.'],
        ['user_id' => $student_ids[1], 'course_id' => $course_ids[0], 'category' => 'Content', 'rating' => 4, 'comments' => 'The course content is very detailed and practical.'],
        ['user_id' => $student_ids[2], 'course_id' => $course_ids[1], 'category' => 'Project', 'rating' => 5, 'comments' => 'The graphic design projects helped me build a great portfolio.']
    ];

    foreach ($feedbacks as $f) {
        $uid = $f['user_id'];
        $cid = $f['course_id'];
        $cat = $f['category'];
        $rat = $f['rating'];
        $com = $conn->real_escape_string($f['comments']);
        $conn->query("INSERT INTO feedback (user_id, course_id, category, rating, comments) VALUES ($uid, $cid, '$cat', $rat, '$com')");
    }
}

echo "Database Seeded Successfully!\n";
?>
