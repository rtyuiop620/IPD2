document.addEventListener('DOMContentLoaded', loadTasks);
document.querySelector('.add-task-btn').addEventListener('click', addTask);

function loadTasks() {
    fetch('task-handler.php')
        .then(response => response.json())
        .then(tasks => {
            const taskList = document.querySelector('.task-list');
            taskList.innerHTML = ''; // Clear the current list
            tasks.forEach(task => {
                const taskItem = document.createElement('li');
                taskItem.className = 'task';
                taskItem.innerHTML = `
                    <span>${task.task_text}</span>
                    <button onclick="deleteTask(this)">🗑️</button>
                    <button onclick="toggleComplete(this)">✔️</button>
                `;
                taskList.appendChild(taskItem);
            });
            updateProgress();
        });
}

function addTask() {
    const taskInput = document.querySelector('.task-input');
    const taskText = taskInput.value.trim();

    if (taskText === '') return;

    // Save task to database
    fetch('task-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'task=' + encodeURIComponent(taskText)
    }).then(() => {
        const taskList = document.querySelector('.task-list');
        const taskItem = document.createElement('li');
        taskItem.className = 'task';
        taskItem.innerHTML = `
            <span>${taskText}</span>
            <button onclick="deleteTask(this)">🗑️</button>
            <button onclick="toggleComplete(this)">✔️</button>
        `;
        taskList.appendChild(taskItem);
        taskInput.value = '';

        updateProgress();
    });
}

function deleteTask(button) {
    button.parentElement.remove();
    updateProgress();
    // Optionally add a fetch request to delete from the database
}

function toggleComplete(button) {
    const taskItem = button.parentElement;
    taskItem.classList.toggle('complete');
    updateProgress();
}

function updateProgress() {
    const tasks = document.querySelectorAll('.task');
    const completedTasks = document.querySelectorAll('.task.complete');
    const progressBar = document.querySelector('.progress-bar');
    const taskCounter = document.querySelector('.task-counter');

    const totalTasks = tasks.length;
    const completedCount = completedTasks.length;
    const progressPercentage = totalTasks === 0 ? 0 : (completedCount / totalTasks) * 100;

    progressBar.style.width = progressPercentage + '%';
    taskCounter.textContent = `${completedCount}/${totalTasks}`;
}
