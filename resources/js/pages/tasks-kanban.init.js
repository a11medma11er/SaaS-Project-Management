/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: tasks-kanaban  init js
*/

var tasks_list = [
    document.getElementById("kanbanboard"),
    document.getElementById("unassigned-task"),
    document.getElementById("todo-task"),
    document.getElementById("inprogress-task"),
    document.getElementById("reviews-task"),
    document.getElementById("completed-task"),
    document.getElementById("new-task")
];
if (tasks_list) {
    var myModalEl = document.getElementById('deleteRecordModal');
    if (myModalEl) {
        myModalEl.addEventListener('show.bs.modal', function (event) {
            document.getElementById('delete-record').addEventListener('click', function () {
                event.relatedTarget.closest(".tasks-box").remove();
                document.getElementById('delete-btn-close').click();
                taskCounter();
            });
        });
    }

    function noTaskImage() {
        Array.from(document.querySelectorAll("#kanbanboard .tasks-list")).forEach(function (item) {
            var taskBox = item.querySelectorAll(".tasks-box")
            if (taskBox.length > 0) {
                item.querySelector('.tasks').classList.remove("noTask");
            } else {
                item.querySelector('.tasks').classList.add("noTask");
            }
        });
    }

    function taskCounter() {
        task_lists = document.querySelectorAll("#kanbanboard .tasks-list");
        if (task_lists) {
            Array.from(task_lists).forEach(function (element) {
                tasks = element.getElementsByClassName("tasks");
                Array.from(tasks).forEach(function (ele) {
                    task_box = ele.getElementsByClassName("tasks-box");
                    task_counted = task_box.length;
                });
                badge = element.querySelector(".totaltask-badge").innerText = "";
                badge = element.querySelector(".totaltask-badge").innerText = task_counted;
            });
        }
    }

    drake = dragula(tasks_list).on('drag', function (el) {
        el.className = el.className.replace('ex-moved', '');
    }).on('drop', function (el) {
        el.className += ' ex-moved';
    }).on('over', function (el, container) {
        container.className += ' ex-over';
    }).on('out', function (el, container) {
        container.className = container.className.replace('ex-over', '');

        noTaskImage();
        taskCounter();
    });

    var kanbanboard = document.querySelectorAll('#kanbanboard');
    if (kanbanboard) {
        var scroll = autoScroll([
            document.querySelector("#kanbanboard"),
        ], {
            margin: 20,
            maxSpeed: 100,
            scrollWhenOutside: true,
            autoScroll: function () {
                return this.down && drake.dragging;
            }
        });
    }

    // Create Board and Add Member features removed for better UX
}