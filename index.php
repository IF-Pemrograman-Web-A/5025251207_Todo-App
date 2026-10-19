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
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $fileName = time() . '_' . basename($_FILES['todoImage']['name']);
        $target_file = $upload_dir . $fileName;
        if (move_uploaded_file($_FILES['todoImage']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO todos (type, title, description, due_date, notif_time, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $todo_type, $title, $description, $due_date, $notif_time, $image_path);
    $stmt->execute();
    header("Location: index.php?type=" . $todo_type);
    exit();
}

// --- DELETE TODO ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?type=" . $type);
    exit();
}

// --- TOGGLE COMPLETE ---
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("UPDATE todos SET is_completed = NOT is_completed WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?type=" . $type);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List - <?= ucfirst($type) ?></title>
    <link rel="stylesheet" href="cantik.css">
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
            <!-- Left Panel: Todo List -->
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
        
            <!-- Right Panel: Todo Detail -->
            <section class="detail" aria-labelledby="detail-heading">
                <h2 id="detail-heading">Todo Detail</h2>

                <?php if ($selected_todo): ?>
                    <h3><?= htmlspecialchars($selected_todo['title']) ?></h3>
                    <p><?= htmlspecialchars($selected_todo['description'] ?: 'Tidak ada deskripsi.') ?></p>

                    <p><b>Due:</b> <span><?= $selected_todo['due_date'] ?: '-' ?></span></p>
                    <p><b>Notification Time:</b> <span><?= $selected_todo['notif_time'] ?: '-' ?></span></p>
                    <p><b>Status:</b> <span><?= $selected_todo['is_completed'] ? 'Completed' : 'Not completed' ?></span></p>

                    <?php if ($selected_todo['image_path']): ?>
                        <div style="margin-top: 10px;">
                            <img src="<?= $selected_todo['image_path'] ?>" style="max-width: 100%; max-height: 150px; border-radius: 10px;">
                        </div>
                    <?php endif; ?>

                    <a href="index.php?type=<?= $type ?>&toggle=<?= $selected_todo['id'] ?>">
                        <button type="button" style="margin-top: 15px;">
                            <?= $selected_todo['is_completed'] ? 'Mark as Incomplete' : 'Complete' ?>
                        </button>
                    </a>
                <?php else: ?>
                    <h3>Select Todo</h3>
                    <p>Pilih TODO di sebelah kiri untuk melihat detailnya</p>
                    <p><b>Due:</b> -</p>
                    <p><b>Notification Time:</b> -</p>
                    <p><b>Status:</b> -</p>
                <?php endif; ?>
            </section>
        </main>

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

    <script src="interaction.js"></script>
</body>
</html>