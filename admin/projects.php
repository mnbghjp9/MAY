<?php
require_once 'config.php';
checkLogin();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $link = sanitize($_POST['link']);
        $active = isset($_POST['active']) ? 1 : 0;
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $fileName;
            }
        }
        
        try {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO projects (title, description, link, image, active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $description, $link, $image, $active]);
            } else {
                if ($image) {
                    $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, link = ?, image = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $link, $image, $active, $id]);
                } else {
                    $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, link = ?, active = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $link, $active, $id]);
                }
            }
            header("Location: projects.php?success=" . ($action === 'add' ? 'added' : 'updated'));
            exit;
        } catch(PDOException $e) {
            $error = "حدث خطأ: " . $e->getMessage();
        }
    }
}

// Get project data for editing
$project = null;
if ($action === 'edit' && $id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        
        if (!$project) {
            header("Location: projects.php");
            exit;
        }
    } catch(PDOException $e) {
        $error = "حدث خطأ: " . $e->getMessage();
    }
}

// Delete project
if ($action === 'delete' && $id) {
    try {
        // Get image name first
        $stmt = $conn->prepare("SELECT image FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        
        if ($project && $project['image']) {
            $imagePath = 'uploads/' . $project['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: projects.php?success=deleted");
        exit;
    } catch(PDOException $e) {
        $error = "حدث خطأ: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشاريع | لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1><?php echo $action === 'add' ? 'إضافة مشروع جديد' : ($action === 'edit' ? 'تعديل المشروع' : 'إدارة المشاريع'); ?></h1>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    switch ($_GET['success']) {
                        case 'added':
                            echo 'تم إضافة المشروع بنجاح';
                            break;
                        case 'updated':
                            echo 'تم تحديث المشروع بنجاح';
                            break;
                        case 'deleted':
                            echo 'تم حذف المشروع بنجاح';
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="card">
                    <form action="projects.php?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="title">عنوان المشروع</label>
                            <input type="text" id="title" name="title" value="<?php echo $project['title'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف المشروع</label>
                            <textarea id="description" name="description" required><?php echo $project['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="link">رابط المشروع</label>
                            <input type="url" id="link" name="link" value="<?php echo $project['link'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">صورة المشروع</label>
                            <input type="file" id="image" name="image" accept="image/*" <?php echo $action === 'add' ? 'required' : ''; ?>>
                            <?php if ($action === 'edit' && $project['image']): ?>
                                <p>الصورة الحالية: <?php echo $project['image']; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="active" <?php echo (isset($project['active']) && $project['active']) ? 'checked' : ''; ?>>
                                نشط
                            </label>
                        </div>
                        
                        <button type="submit" class="btn"><?php echo $action === 'add' ? 'إضافة المشروع' : 'تحديث المشروع'; ?></button>
                        <a href="projects.php" class="btn btn-secondary">إلغاء</a>
                    </form>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h3>قائمة المشاريع</h3>
                        <a href="projects.php?action=add" class="btn">إضافة مشروع جديد</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>العنوان</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
                                    while ($project = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>";
                                        if ($project['image']) {
                                            echo "<img src='uploads/" . htmlspecialchars($project['image']) . "' alt='" . htmlspecialchars($project['title']) . "' width='50'>";
                                        }
                                        echo "</td>";
                                        echo "<td>" . htmlspecialchars($project['title']) . "</td>";
                                        echo "<td>" . date('Y-m-d', strtotime($project['created_at'])) . "</td>";
                                        echo "<td>" . ($project['active'] ? 'نشط' : 'غير نشط') . "</td>";
                                        echo "<td>";
                                        echo "<a href='projects.php?action=edit&id=" . $project['id'] . "' class='btn btn-sm'>تعديل</a> ";
                                        echo "<a href='projects.php?action=delete&id=" . $project['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"هل أنت متأكد من حذف هذا المشروع؟\")'>حذف</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } catch(PDOException $e) {
                                    echo "<tr><td colspan='5'>حدث خطأ أثناء جلب المشاريع</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
