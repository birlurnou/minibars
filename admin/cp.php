<?php
$users_file = '../config/users.json';

function loadUsers($file) {
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    return json_decode($content, true);
}

$users = loadUsers($users_file);
$all_rights = ["excise", "depatchers", "deadlines", "history", "gih", "empty", "calculator", "education", "settings", "excise_input"];
$rights_labels = [
    "excise" => "Акцизы",
    "depatchers" => "Депатчеры",
    "deadlines" => "Сроки",
    "history" => "История",
    "gih" => "GIH",
    "empty" => "Пустые",
    "calculator" => "Калькулятор",
    "education" => "Обучение",
    "settings" => "Настройки",
    "excise_input" => "Ввод акцизов"
];
$total_rights = count($all_rights);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #0f0f12;
            padding: 24px;
            color: #e8e8ec;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            background: #1a1a1f;
            border-radius: 16px;
            border: 1px solid #2c2c32;
            overflow: hidden;
        }

        .header {
            background: #131316;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #2c2c32;
        }

        .header h1 {
            font-size: 25px;
            font-weight: 500;
            letter-spacing: 0.3px;
            color: #e8e8ec;
        }

        .save-btn {
            background: #2c6e2c;
            color: #e8e8ec;
            border: 1px solid #3a8a3a;
            padding: 8px 30px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .save-btn:hover:not(:disabled) {
            background: #357a35;
            border-color: #4a9a4a;
        }

        .save-btn:disabled {
            background: #2a2a30;
            border-color: #3a3a42;
            color: #6a6a72;
            cursor: default;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #2c2c32;
        }

        th {
            background: #131316;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9a9aa2;
        }

        tr:hover td {
            background: #1e1e24;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 5px 20px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.15s;
            font-family: inherit;
        }

        .btn-edit {
            background: #2a4a6e;
            color: #d4eaff;
            border: 1px solid #3a5a7e;
        }

        .btn-edit:hover {
            background: #2f557d;
            border-color: #4a6a8e;
        }

        .btn-copy {
            background: #3a4a6e;
            color: #d4eaff;
            border: 1px solid #4a5a7e;
        }

        .btn-copy:hover {
            background: #3f557d;
            border-color: #4e6a8e;
        }

        .btn-delete {
            background: #5a2a2a;
            color: #ffd4d4;
            border: 1px solid #7a3a3a;
        }

        .btn-delete:hover {
            background: #6a3030;
            border-color: #8a4444;
        }

        .add-btn {
            background: #1e3a4a;
            color: #c4e4f4;
            border: 1px solid #2a5a6a;
            padding: 8px 20px;
            border-radius: 7px;
            margin: 20px 24px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            font-family: inherit;
            transition: all 0.2s;
        }

        .add-btn:hover {
            background: #234a5a;
            border-color: #2e6a7e;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(8, 8, 12, 0.85);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #1c1c22;
            border: 1px solid #3a3a42;
            border-radius: 6px;
            width: 600px;
            max-width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #2c2c34;
            background: #16161c;
        }

        .modal-header h2 {
            font-size: 16px;
            font-weight: 500;
            color: #e0e0e8;
        }

        .close {
            font-size: 24px;
            cursor: pointer;
            color: #7a7a82;
            line-height: 1;
        }

        .close:hover {
            color: #c0c0c8;
        }

        .form-group {
            padding: 12px 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
            font-weight: 500;
            color: #aaaab2;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .form-group input {
            width: 100%;
            padding: 9px 12px;
            background: #111116;
            border: 1px solid #33333b;
            border-radius: 4px;
            font-size: 15px;
            font-family: inherit;
            color: #e0e0e8;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4a5a7e;
        }

        /* Два столбца для прав */
        .rights-two-columns {
            display: flex;
            gap: 24px;
            border: 1px solid #2c2c34;
            border-radius: 4px;
            padding: 12px;
            background: #121216;
        }

        .rights-column {
            flex: 1;
        }

        .rights-column label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            cursor: pointer;
            text-transform: none;
            font-size: 15px;
            color: #c0c0ca;
            font-weight: normal;
            letter-spacing: 0;
        }

        .rights-column input {
            width: auto;
            margin-right: 10px;
            margin-left: 4px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            padding: 16px 20px 20px;
            border-top: 1px solid #2c2c34;
            background: #16161c;
        }

        .modal-buttons button {
            flex: 1;
            padding: 9px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
        }

        .btn-save-modal {
            background: #2c6e2c;
            color: #e0e0e8;
            border: 1px solid #3a8a3a;
        }

        .btn-save-modal:hover {
            background: #357a35;
        }

        .btn-cancel-modal {
            background: #2a2a30;
            color: #c0c0c8;
            border: 1px solid #3a3a42;
        }

        .btn-cancel-modal:hover {
            background: #35353d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ</h1>
            <button class="save-btn" id="saveBtn" disabled>СОХРАНИТЬ</button>
        </div>

        <table id="userTable">
            <thead>
                <tr>
                    <th>Логин</th>
                    <th>Права</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="userTableBody"></tbody>
        </table>

        <button class="add-btn" id="addUserBtn">Добавить пользователя</button>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">РЕДАКТИРОВАНИЕ</h2>
                <span class="close">&times;</span>
            </div>
            <form id="userForm">
                <div class="form-group">
                    <label>ЛОГИН</label>
                    <input type="text" id="modalLogin" required>
                </div>
                <div class="form-group">
                    <label>ПАРОЛЬ</label>
                    <input type="text" id="modalPassword" required>
                </div>
                <div class="form-group">
                    <label>ДОСТУП</label>
                    <div class="rights-two-columns" id="modalRights">
                        <div class="rights-column" id="rightsColumn1"></div>
                        <div class="rights-column" id="rightsColumn2"></div>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" id="modalSaveBtn" class="btn-save-modal">СОХРАНИТЬ</button>
                    <button type="button" id="modalCancelBtn" class="btn-cancel-modal">ОТМЕНА</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let allUsers = <?php echo json_encode($users); ?>;
        let allRights = <?php echo json_encode($all_rights); ?>;
        let hasChanges = false;
        let currentAction = null;
        let currentUsername = null;
        let originalUsers = JSON.parse(JSON.stringify(allUsers));

        const userTableBody = document.getElementById('userTableBody');
        const saveBtn = document.getElementById('saveBtn');
        const modal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalLogin = document.getElementById('modalLogin');
        const modalPassword = document.getElementById('modalPassword');
        const modalRights = document.getElementById('modalRights');
        const modalSaveBtn = document.getElementById('modalSaveBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const closeBtn = document.querySelector('.close');
        const addUserBtn = document.getElementById('addUserBtn');

        // Разделение прав на два столбца (первые 5 и последние 5)
        const firstFive = allRights.slice(0, 5);
        const secondFive = allRights.slice(5, 10);

        function renderRightsCheckboxes(selectedRights = []) {
            const col1 = document.getElementById('rightsColumn1');
            const col2 = document.getElementById('rightsColumn2');
            
            // Маппинг прав на русские названия (передаём из PHP)
            const rightsLabels = <?php echo json_encode($rights_labels); ?>;
            const firstFive = allRights.slice(0, 5);
            const secondFive = allRights.slice(5, 10);
            
            col1.innerHTML = '';
            col2.innerHTML = '';
            
            firstFive.forEach(right => {
                const label = document.createElement('label');
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.value = right;
                cb.checked = selectedRights.includes(right);
                label.appendChild(cb);
                label.appendChild(document.createTextNode(' ' + (rightsLabels[right] || right)));
                col1.appendChild(label);
            });
            
            secondFive.forEach(right => {
                const label = document.createElement('label');
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.value = right;
                cb.checked = selectedRights.includes(right);
                label.appendChild(cb);
                label.appendChild(document.createTextNode(' ' + (rightsLabels[right] || right)));
                col2.appendChild(label);
            });
        }

        function getSelectedRights() {
            const selected = [];
            const allCheckboxes = modalRights.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(cb => {
                if (cb.checked) selected.push(cb.value);
            });
            return selected;
        }

        function getRightsCount(rights) {
            return rights && rights.length ? rights.length : 0;
        }

        function renderTable() {
            userTableBody.innerHTML = '';
            for (let login in allUsers) {
                const user = allUsers[login];
                const rightsCount = getRightsCount(user.access);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="font-weight: 500;">${escapeHtml(login)}</td>
                    <td>${rightsCount}/${allRights.length}</td>
                    <td class="actions">
                        <button class="btn btn-edit" data-login="${escapeHtml(login)}">Редактировать</button>
                        <button class="btn btn-copy" data-login="${escapeHtml(login)}">Скопировать</button>
                        <button class="btn btn-delete" data-login="${escapeHtml(login)}">Удалить</button>
                    </td>
                `;
                userTableBody.appendChild(row);
            }

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', () => openEditModal(btn.getAttribute('data-login')));
            });
            document.querySelectorAll('.btn-copy').forEach(btn => {
                btn.addEventListener('click', () => openCopyModal(btn.getAttribute('data-login')));
            });
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', () => deleteUser(btn.getAttribute('data-login')));
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function openEditModal(login) {
            currentAction = 'edit';
            currentUsername = login;
            modalTitle.textContent = 'РЕДАКТИРОВАНИЕ';
            modalLogin.value = login;
            modalLogin.disabled = true;
            modalPassword.value = allUsers[login].password;
            renderRightsCheckboxes(allUsers[login].access);
            modal.style.display = 'flex';
        }

        function openCopyModal(login) {
            currentAction = 'copy';
            currentUsername = login;
            modalTitle.textContent = 'КОПИРОВАНИЕ';
            modalLogin.value = '';
            modalLogin.disabled = false;
            modalPassword.value = '';
            renderRightsCheckboxes(allUsers[login].access);
            modal.style.display = 'flex';
        }

        function openAddModal() {
            currentAction = 'add';
            currentUsername = null;
            modalTitle.textContent = 'ДОБАВЛЕНИЕ';
            modalLogin.value = '';
            modalLogin.disabled = false;
            modalPassword.value = '';
            renderRightsCheckboxes([]);
            modal.style.display = 'flex';
        }

        function deleteUser(login) {
            if (confirm(`Удалить пользователя "${login}"?`)) {
                delete allUsers[login];
                checkForChanges();
                renderTable();
            }
        }

        modalSaveBtn.addEventListener('click', () => {
            const login = modalLogin.value.trim();
            const password = modalPassword.value.trim();
            
            if (!login || !password) {
                alert('Заполните логин и пароль');
                return;
            }
            
            const rights = getSelectedRights();
            
            if (currentAction === 'edit') {
                if (login !== currentUsername && allUsers[login]) {
                    alert('Пользователь уже существует');
                    return;
                }
                if (login !== currentUsername) {
                    allUsers[login] = { password: password, access: rights };
                    delete allUsers[currentUsername];
                } else {
                    allUsers[login] = { password: password, access: rights };
                }
            } else if (currentAction === 'copy') {
                if (allUsers[login]) {
                    alert('Пользователь уже существует');
                    return;
                }
                allUsers[login] = { password: password, access: rights };
            } else if (currentAction === 'add') {
                if (allUsers[login]) {
                    alert('Пользователь уже существует');
                    return;
                }
                allUsers[login] = { password: password, access: rights };
            }
            
            modal.style.display = 'none';
            checkForChanges();
            renderTable();
        });

        function closeModal() {
            modal.style.display = 'none';
        }
        
        closeBtn.addEventListener('click', closeModal);
        modalCancelBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        addUserBtn.addEventListener('click', openAddModal);
        
        function checkForChanges() {
            const currentJson = JSON.stringify(allUsers);
            const originalJson = JSON.stringify(originalUsers);
            hasChanges = currentJson !== originalJson;
            saveBtn.disabled = !hasChanges;
        }
        
        saveBtn.addEventListener('click', async () => {
            if (!hasChanges) return;
            
            const response = await fetch('save_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(allUsers)
            });
            
            const result = await response.json();
            if (result.success) {
                // alert('Файл users.json обновлён');
                originalUsers = JSON.parse(JSON.stringify(allUsers));
                hasChanges = false;
                saveBtn.disabled = true;
            } else {
                alert('Ошибка: ' + result.error);
            }
        });
        
        // Инициализация
        renderRightsCheckboxes([]);
        renderTable();
    </script>
</body>
</html>