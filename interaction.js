let todos = [
{
title: "Resume Jarkom",
description: "Kirim resume materi setiap minggu",
due: "2026-09-19",
completed: false
},
{
title: "Kelas PWeb",
description: "Jangan telat kelas PWeb",
due: "2026-09-22",
completed: false
},
{
title: "Quiz KPPL",
description: "Belajar quiz KPPL",
due: "2026-09-25",
completed: false
}
];

let selectedIndex = null; // todo yg lg dipake buat detail 

/* samain di html*/
let TodoList = document.getElementById("TodoList");
let newTodo = document.getElementById("newtodo");
let todoInput = document.getElementById("todoinput");
let desc = document.getElementById("description");
let date = document.getElementById("date");

// buat detail todo list

let detailTitle = document.getElementById("detailTitle");
let detailDesc = document.getElementById("detailDesc");
let detailDue = document.getElementById("detailDue");
let detailStatus = document.getElementById("detailStatus");
let complete= document.getElementById("complete");

function updateDetail(index) {
if (todos.length === 0 || index === null ||index >= todos.length) {
    detailTitle.textContent = "Tidak ada kegiatan";
    detailDesc.textContent = "Tambahkan TODO baru atau pilih dari list";
    detailDue.textContent = "-";
    detailStatus.textContent = "-";
    complete.style.display = "none";
    return;
}

    selectedIndex = index;
    let todo = todos[index];

detailTitle.textContent = todo.title;
detailDesc.textContent = todo.description || "Tidak ada deskripsi.";
detailDue.textContent = todo.due || "-";
detailStatus.textContent = todo.completed ? "Completed" : "Not completed";

    complete.style.display = "inline-block";
    complete.textContent = todo.completed ? "Mark as Incomplete" : "Complete";
}

function showTodos() {
    TodoList.innerHTML = "";

    for (let i=0; i< todos.length; i++) {
        let todo = todos[i];
        let item = document.createElement("div");
        /*buat div luaran*/
        item.className = "item";
        item.style.cursor = "pointer";

        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = todo.completed;

        let todoinfo = document.createElement("div");
        /*buat elemen h3, p*/
        todoinfo.style.flex = "1";
        let title = document.createElement("h3");
        title.textContent = todo.title;
        let due = document.createElement("p");
        due.textContent = todo.due;
        let description = document.createElement("p");
        description.textContent = todo.description;
        let edit = document.createElement("button");
        edit.textContent = "Edit";
        let hapus = document.createElement("button");
        hapus.textContent = "Delete";
    
    
    checkbox.addEventListener("change", function() {
        todo.completed = checkbox.checked;

        if (todo.completed) {
            title.style.textDecoration = "line-through";
        } else {
            title.style.textDecoration = "none";
        }
    });

/*Implement buttons for edit and delete*/

   edit.addEventListener("click", function(e) {
      e.stopPropagation();
      let newTitle = prompt("Edit Judul Todo:", todo.title);
      if (newTitle !== null && newTitle.trim() !== "") {
        let newDesc = prompt("Edit Deskripsi Todo:", todo.description);
        todo.title = newTitle;
        if (newDesc !== null) {
          todo.description = newDesc;
        }
        showTodos();
        if (selectedIndex === i) updateDetail(i);
      }
    });

    hapus.addEventListener("click", function(e) {
      e.stopPropagation();
      todos.splice(i, 1);

      if (selectedIndex >= todos.length) {
        selectedIndex = todos.length - 1;
      }
      showTodos();
      updateDetail(selectedIndex);
    });

    //buat todo detailny pas klik
    item.addEventListener("click", function() {
      updateDetail(i);
    });

    todoinfo.appendChild(title);
    todoinfo.appendChild(due);
    todoinfo.appendChild(description);

    item.appendChild(checkbox);
    item.appendChild(todoinfo);
    item.appendChild(edit);
    item.appendChild(hapus);
    TodoList.appendChild(item);

    if (todo.completed) {
        title.style.textDecoration = "line-through";
    }

}
}

complete.addEventListener("click", function() {
  if (selectedIndex !== null && todos[selectedIndex]) {
    todos[selectedIndex].completed = !todos[selectedIndex].completed;
    showTodos();
    updateDetail(selectedIndex);
  }
});

newTodo.addEventListener("submit", function(event) {
    event.preventDefault(); /*prevent page reload*/

    if (todoInput.value.trim() === "") return;

    
    let baru = {
    title: todoInput.value,
    description: desc.value,
    due: date.value,
    completed: false
};
todos.push(baru);
showTodos();

todoInput.value = "";
desc.value = "";
date.value = "";
});

let darkModeButton = document.getElementById("darkModeButton");
darkModeButton.addEventListener("click", function() {

document.body.classList.toggle("dark-mode");
});
showTodos();
