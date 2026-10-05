let todos = [];
let selectedIndex = null;
let db = null;

const TodoList = document.getElementById("TodoList");
const newTodo = document.getElementById("newtodo");
const todoInput = document.getElementById("todoinput");
const desc = document.getElementById("description");
const date = document.getElementById("date");
const todoImageInput = document.getElementById("todoImage");
const notifTimeInput = document.getElementById("notifTime");

const detailTitle = document.getElementById("detailTitle");
const detailDesc = document.getElementById("detailDesc");
const detailDue = document.getElementById("detailDue");
const detailStatus = document.getElementById("detailStatus");
const detailImageContainer = document.getElementById("detailImageContainer");
const complete = document.getElementById("complete");
const detailNotifTime = document.getElementById("detailNotifTime");
const darkModeButton = document.getElementById("darkModeButton");

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js')
        .then(() => console.log('Service Worker Registered'))
        .catch(err => console.error('SW Registration Failed', err));
}

function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

function ScheduleNotification(todoTitle, notifTimeStr) {
    if ('Notification' in window && Notification.permission === 'granted' && notifTimeStr) {
        const [hours, minutes] = notifTimeStr.split(':');
        const now = new Date();
        const target = new Date();
        target.setHours(parseInt(hours), parseInt(minutes), 0, 0);

        let delay = target.getTime() - now.getTime();
        if (delay < 0) {
            delay += 24 * 60 * 60 * 1000;
        }

        navigator.serviceWorker.ready.then((registration) => {
            if (registration.active) {
                registration.active.postMessage({
                    type: 'SCHEDULE_NOTIFICATION',
                    title: 'Reminder Todo List',
                    body: `Waktunya kerjakan: ${todoTitle}`,
                    delay: delay
                });
            }
        });
    }
}

function setupTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }
}

darkModeButton.addEventListener("click", function() {
    document.body.classList.toggle("dark-mode");
    const isDark = document.body.classList.contains("dark-mode");
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
});

function IndexedDB() {
    const request = indexedDB.open("todoAppDB", 1);

    request.onupgradeneeded = function(event) {
        db = event.target.result;
        if (!db.objectStoreNames.contains("todos")) {
            db.createObjectStore("todos", { keyPath: "id", autoIncrement: true });
        }
    };
    request.onsuccess = function(event) {
        db = event.target.result;
        loadTodosFromDB();
    };
    request.onerror = function(event) {
        console.error("IndexedDB Error:", event.target.errorCode);
    };
}

function loadTodosFromDB() {
    const transaction = db.transaction(["todos"], "readonly");
    const store = transaction.objectStore("todos");
    const request = store.getAll();

    request.onsuccess = function() {
        todos = request.result || [];
        showTodos();
        if (todos.length > 0) {
            updateDetail(0);
        } else {
            updateDetail(null);
        }
    };
}

function saveTodoToDB(todoItem, callback) {
    const transaction = db.transaction(["todos"], "readwrite");
    const store = transaction.objectStore("todos");
    const request = store.add(todoItem);
    request.onsuccess = function(e) {
        todoItem.id = e.target.result;
        if (callback) callback();
    };
}

function updateTodoInDB(todoItem) {
    const transaction = db.transaction(["todos"], "readwrite");
    const store = transaction.objectStore("todos");
    store.put(todoItem);
}

function deleteTodoFromDB(id, callback) {
    const transaction = db.transaction(["todos"], "readwrite");
    const store = transaction.objectStore("todos");
    const request = store.delete(id);

    request.onsuccess = function() {
        if (callback) callback();
    };
}

function updateDetail(index) {
    if (todos.length === 0 || index === null || index >= todos.length) {
        detailTitle.textContent = "Tidak ada kegiatan";
        detailDesc.textContent = "Tambahkan TODO baru atau pilih dari list";
        detailDue.textContent = "-";
        detailNotifTime.textContent = "-";
        detailStatus.textContent = "-";
        detailImageContainer.innerHTML = "";
        complete.style.display = "none";
        return;
    }

    selectedIndex = index;
    let todo = todos[index];

    detailTitle.textContent = todo.title;
    detailDesc.textContent = todo.description || "Tidak ada deskripsi.";
    detailDue.textContent = todo.due || "-";
    detailNotifTime.textContent = todo.notifTime || "-";
    detailStatus.textContent = todo.completed ? "Completed" : "Not completed";

    detailImageContainer.innerHTML = "";
    if (todo.image) {
        let img = document.createElement("img");
        img.src = todo.image;
        img.alt = `Gambar lampiran untuk ${todo.title}`;
        img.style.maxWidth = "100%";
        img.style.maxHeight = "150px";
        img.style.borderRadius = "10px";
        detailImageContainer.appendChild(img);
    }

    complete.style.display = "inline-block";
    complete.textContent = todo.completed ? "Mark as Incomplete" : "Complete";
}

function showTodos() {
    TodoList.innerHTML = "";

    todos.forEach((todo, i) => {
        let item = document.createElement("div");
        item.className = "item";
        item.setAttribute("tabindex", "0");
        item.setAttribute("role", "button");
        item.setAttribute("aria-label", `Todo item: ${todo.title}`);
        
        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = todo.completed;
        checkbox.setAttribute("aria-label", `Selesaikan ${todo.title}`);
        
        let todoinfo = document.createElement("div");
        todoinfo.style.flex = "1";
        
        let title = document.createElement("h3");
        title.textContent = todo.title;
        if (todo.completed) title.style.textDecoration = "line-through";
        
        let due = document.createElement("p");
        due.textContent = `Due: ${todo.due || '-'}`;

        todoinfo.appendChild(title);
        todoinfo.appendChild(due);

        if (todo.image) {
            let thumb = document.createElement("img");
            thumb.src = todo.image;
            thumb.alt = todo.title;
            thumb.style.width = "40px";
            thumb.style.height = "40px";
            thumb.style.borderRadius = "8px";
            thumb.style.objectFit = "cover";
            item.appendChild(thumb);
        }

        let editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.type = "button";

        let deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.type = "button";

        checkbox.addEventListener("change", function(e) {
            e.stopPropagation();
            todo.completed = checkbox.checked;
            title.style.textDecoration = todo.completed ? "line-through" : "none";
            updateTodoInDB(todo);
            if (selectedIndex === i) updateDetail(i);
        });

        editBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            let newTitle = prompt("Edit Judul Todo:", todo.title);
            if (newTitle !== null && newTitle.trim() !== "") {
                let newDesc = prompt("Edit Deskripsi Todo:", todo.description);
                todo.title = newTitle.trim();
                if (newDesc !== null) todo.description = newDesc.trim();
                updateTodoInDB(todo);
                showTodos();
                if (selectedIndex === i) updateDetail(i);
            }
        });

        deleteBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            deleteTodoFromDB(todo.id, () => {
                todos.splice(i, 1);
                if (selectedIndex >= todos.length) {
                    selectedIndex = todos.length - 1;
                }
                showTodos();
                updateDetail(selectedIndex);
            });
        });

        item.addEventListener("click", function() {
            updateDetail(i);
        });

        item.addEventListener("keydown", function(e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                updateDetail(i);
            }
        });

        item.appendChild(checkbox);
        item.appendChild(todoinfo);
        item.appendChild(editBtn);
        item.appendChild(deleteBtn);

        TodoList.appendChild(item);
    });
}

complete.addEventListener("click", function() {
    if (selectedIndex !== null && todos[selectedIndex]) {
        todos[selectedIndex].completed = !todos[selectedIndex].completed;
        updateTodoInDB(todos[selectedIndex]);
        showTodos();
        updateDetail(selectedIndex);
    }
});

newTodo.addEventListener("submit", function(event) {
    event.preventDefault();

    if (todoInput.value.trim() === "") return;

    requestNotificationPermission();

    const file = todoImageInput.files[0];

    const SaveTodo = (base64Img) => {
        let baru = {
            title: todoInput.value.trim(),
            description: desc.value.trim(),
            due: date.value,
            notifTime: notifTimeInput.value,
            completed: false,
            image: base64Img || null
        };

        saveTodoToDB(baru, () => {
            todos.push(baru);
            showTodos();
            updateDetail(todos.length - 1);

            if (baru.notifTime) {
                ScheduleNotification(baru.title, baru.notifTime);
            }

            todoInput.value = "";
            desc.value = "";
            date.value = "";
            todoImageInput.value = "";
            notifTimeInput.value = "";
        });
    };

    if (file) {
        const reader = new FileReader();
        reader.onloadend = function() {
            SaveTodo(reader.result);
        };
        reader.readAsDataURL(file);
    } else {
        SaveTodo(null);
    }
});

setupTheme();
IndexedDB();