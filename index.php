<?php
require_once 'config.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'personal';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $notif_time = $_POST['notif_time'];
    $todo_type = $_POST['type'];

    $image_path = null;
    if (isset($_FILES['todoImage']) && $_FILES['todoImage']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['todoImage']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $target_file_path = $upload_dir . $fileName;
        $relative_path = 'uploads/' . $fileName;

        if (move_uploaded_file($_FILES['todoImage']['tmp_name'], $target_file_path)) {
            $image_path = $relative_path;
        }
    }

    $stmt = $conn->prepare("INSERT INTO todos (type, title, description, due_date, notif_time, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $todo_type, $title, $description, $due_date, $notif_time, $image_path);
    $stmt->execute();
    header("Location: index.php?type=" . $todo_type);
    exit();
}

// EDIT TODO 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $notif_time = $_POST['notif_time'];
    $todo_type = isset($_POST['type']) ? $_POST['type'] : $type;

    if (isset($_FILES['todoImage']) && $_FILES['todoImage']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['todoImage']['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $target_file_path = $upload_dir . $fileName;
        $relative_path = 'uploads/' . $fileName;

        if (move_uploaded_file($_FILES['todoImage']['tmp_name'], $target_file_path)) {
            $stmt_old = $conn->prepare("SELECT image_path FROM todos WHERE id = ?");
            $stmt_old->bind_param("i", $id);
            $stmt_old->execute();
            $res_old = $stmt_old->get_result()->fetch_assoc();
            if ($res_old && !empty($res_old['image_path']) && file_exists(__DIR__ . '/' . $res_old['image_path'])) {
                unlink(__DIR__ . '/' . $res_old['image_path']);
            }

            $stmt = $conn->prepare("UPDATE todos SET title = ?, description = ?, due_date = ?, notif_time = ?, image_path = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $title, $description, $due_date, $notif_time, $relative_path, $id);
            $stmt->execute();
        }
    } else {
        $stmt = $conn->prepare("UPDATE todos SET title = ?, description = ?, due_date = ?, notif_time = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $description, $due_date, $notif_time, $id);
        $stmt->execute();
    }
    
    header("Location: index.php?type=" . $todo_type . "&detail=" . $id);
    exit();
}

//DELETE TODO
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt_img = $conn->prepare("SELECT image_path FROM todos WHERE id = ?");
    $stmt_img->bind_param("i", $id);
    $stmt_img->execute();
    $res = $stmt_img->get_result()->fetch_assoc();
    if ($res && !empty($res['image_path']) && file_exists(__DIR__ . '/' . $res['image_path'])) {
        unlink(__DIR__ . '/' . $res['image_path']);
    }

    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?type=" . $type);
    exit();
}

//COMPLETE
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("UPDATE todos SET is_completed = NOT is_completed WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?type=" . $type . "&detail=" . $id);
    exit();
}

$stmt = $conn->prepare("SELECT * FROM todos WHERE type = ? ORDER BY id DESC");
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();
$todos = $result->fetch_all(MYSQLI_ASSOC);

$selected_id = isset($_GET['detail']) ? $_GET['detail'] : (count($todos) > 0 ? $todos[0]['id'] : null);
$selected_todo = null;

if ($selected_id) {
    foreach ($todos as $t) {
        if ($t['id'] == $selected_id) {
            $selected_todo = $t;
            break;
        }
    }
}
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : null;
$edit_todo = null;

if ($edit_id) {
    foreach ($todos as $t) {
        if ($t['id'] == $edit_id) {
            $edit_todo = $t;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List - <?= ucfirst($type) ?></title>
    <link rel="stylesheet" href="cantik.css">
    <style>
        .modal-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000;
        }
        .modal-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: var(--radius-lg);
            width: 90%; max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>Welcome back 👋</h1>
            <p>TODO LIST KEZIA</p>
        </div>
        <button id="darkModeButton" type="button">Toggle Theme</button>
    </header>

    <div class="app-container">
        <?php include 'menu-bar.php'; ?>
        
        <main role="main">
            <section class="todo" aria-labelledby="todo-heading">
                <h2 id="todo-heading"><?= ucfirst($type) ?> Activities</h2>
                <div id="TodoList">
                    <?php if (empty($todos)): ?>
                        <p style="color: var(--text-secondary);">Belum ada tugas pada kategori ini.</p>
                    <?php else: ?>
                        <?php foreach ($todos as $item): ?>
                            <div class="item <?= ($selected_todo && $selected_todo['id'] == $item['id']) ? 'active-item' : '' ?>" 
                                 onclick="window.location.href='index.php?type=<?= $type ?>&detail=<?= $item['id'] ?>'">
                                
                                <a href="index.php?type=<?= $type ?>&toggle=<?= $item['id'] ?>" onclick="event.stopPropagation();">
                                    <input type="checkbox" <?= $item['is_completed'] ? 'checked' : '' ?>>
                                </a>

                                <div style="flex: 1;">
                                    <h3 style="<?= $item['is_completed'] ? 'text-decoration: line-through;' : '' ?>">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </h3>
                                    <p>Due: <?= $item['due_date'] ?: '-' ?></p>
                                </div>

                                <div class="actions">
                                    <a href="index.php?type=<?= $type ?>&edit=<?= $item['id'] ?>" onclick="event.stopPropagation();">
                                        <button type="button">Edit</button>
                                    </a>
                                    <a href="index.php?type=<?= $type ?>&delete=<?= $item['id'] ?>" 
                                       onclick="event.stopPropagation(); return confirm('Hapus todo ini?')">
                                        <button type="button">Delete</button>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        
            <!-- Todo Detail Panel -->
            <section class="detail" aria-labelledby="detail-heading">
                <h2 id="detail-heading">Todo Detail</h2>

                <?php if ($selected_todo): ?>
                    <h3><?= htmlspecialchars($selected_todo['title']) ?></h3>
                    <p><?= htmlspecialchars($selected_todo['description'] ?: 'Tidak ada deskripsi.') ?></p>

                    <p><b>Due:</b> <span><?= $selected_todo['due_date'] ?: '-' ?></span></p>
                    <p><b>Notification Time:</b> <span><?= $selected_todo['notif_time'] ?: '-' ?></span></p>
                    <p><b>Status:</b> <span><?= $selected_todo['is_completed'] ? 'Completed' : 'Not completed' ?></span></p>

                    <div style="margin-top: 15px;">
                        <p style="margin-bottom: 5px; font-weight: 600; font-size: 13px;">Attachment Image:</p>
                        <?php if (!empty($selected_todo['image_path']) && file_exists(__DIR__ . '/' . $selected_todo['image_path'])): ?>
                            <img src="<?= htmlspecialchars($selected_todo['image_path']) ?>" 
                                 alt="Attachment" 
                                 style="max-width: 100%; max-height: 200px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border-color);">
                        <?php else: ?>
                            <div style="width: 100%; height: 110px; background: rgba(0,0,0,0.04); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); font-size: 13px; border: 1px dashed var(--border-color);">
                                No image attached
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px; margin-top: 15px;">
                        <a href="index.php?type=<?= $type ?>&toggle=<?= $selected_todo['id'] ?>">
                            <button type="button">
                                <?= $selected_todo['is_completed'] ? 'Mark as Incomplete' : 'Complete' ?>
                            </button>
                        </a>
                        <a href="index.php?type=<?= $type ?>&edit=<?= $selected_todo['id'] ?>">
                            <button type="button" style="background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary);">
                                Edit
                            </button>
                        </a>
                    </div>
                <?php else: ?>
                    <h3>Select Todo</h3>
                    <p>Pilih TODO di sebelah kiri untuk melihat detailnya</p>
                    <p><b>Due:</b> -</p>
                    <p><b>Notification Time:</b> -</p>
                    <p><b>Status:</b> -</p>
                <?php endif; ?>
            </section>
        </main>

        <!-- Form Sidebar Panel (Add New) -->
        <aside role="complementary" aria-labelledby="add-heading">
            <h2 id="add-heading">Add New Todo</h2>

            <form action="index.php?type=<?= $type ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="type" value="<?= $type ?>">

                <label for="todoinput">Todo Title</label>
                <input type="text" id="todoinput" name="title" placeholder="Insert todo title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Description..."></textarea>

                <label for="date">Due Date</label>
                <input type="date" id="date" name="due_date">

                <label for="todoImage">Capture / Upload Image</label>
                <input type="file" id="todoImage" name="todoImage" accept="image/*">

                <label for="notifTime">Notification Time</label>
                <input type="time" id="notifTime" name="notif_time">

                <button type="submit">Add Todo</button>
            </form>
        </aside>
    </div>

    <?php if ($edit_todo): ?>
        <div class="modal-bg">
            <div class="modal-card">
                <h2>Edit Todo</h2>
                <form action="index.php?type=<?= $type ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $edit_todo['id'] ?>">
                    <input type="hidden" name="type" value="<?= $edit_todo['type'] ?>">

                    <label>Todo Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($edit_todo['title']) ?>" required>

                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($edit_todo['description']) ?></textarea>

                    <label>Due Date</label>
                    <input type="date" name="due_date" value="<?= $edit_todo['due_date'] ?>">

                    <label>Notification Time</label>
                    <input type="time" name="notif_time" value="<?= $edit_todo['notif_time'] ?>">

                    <label>Change Image (Optional)</label>
                    <input type="file" name="todoImage" accept="image/*">

                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                        <button type="submit" style="flex: 1;">Save Changes</button>
                        <a href="index.php?type=<?= $type ?>" style="flex: 1; text-decoration: none;">
                            <button type="button" style="width: 100%; background: #ccc; color: #333;">Cancel</button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script src="interaction.js"></script>
</body>
</html>