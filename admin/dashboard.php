<?php
require_once 'config.php';
checkLogin();

// Get projects count
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM projects");
    $projectCount = $stmt->fetch()['count'];
} catch(PDOException $e) {
    $projectCount = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>لوحة التحكم</h1>
                <p>مرحباً <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <div class="card-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>المشاريع</h3>
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="card-body">
                        <h2><?php echo $projectCount; ?></h2>
                        <p>إجمالي المشاريع</p>
                        <a href="projects.php" class="btn">إدارة المشاريع</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>مشروع جديد</h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="card-body">
                        <p>إضافة مشروع جديد إلى معرض الأعمال</p>
                        <a href="projects.php?action=add" class="btn">إضافة مشروع</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>الملف الشخصي</h3>
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="card-body">
                        <p>تحديث معلومات الملف الشخصي</p>
                        <a href="profile.php" class="btn">تعديل الملف</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>آخر المشاريع</h3>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>العنوان</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
                                while ($project = $stmt->fetch()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($project['title']) . "</td>";
                                    echo "<td>" . date('Y-m-d', strtotime($project['created_at'])) . "</td>";
                                    echo "<td>" . ($project['active'] ? 'نشط' : 'غير نشط') . "</td>";
                                    echo "<td>";
                                    echo "<a href='projects.php?action=edit&id=" . $project['id'] . "' class='btn btn-sm'>تعديل</a> ";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='4'>لا توجد مشاريع</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
