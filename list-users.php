<?php
session_start();

// Проверка на авторизацию
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: /');
    exit;
}

$host = 'localhost';
$dbname = 'dance-studio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Выборка записей, дата создания которых сегодня или позже
    $stmt = $pdo->prepare("SELECT r.*, s.namestyles FROM registrations r
                           JOIN styles s ON r.idstyles = s.idstyles
                           WHERE r.created_at >= CURDATE()  ORDER BY idstyles DESC");
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Выборка хореографов
    $choreographersStmt = $pdo->prepare("SELECT idchoreographer, namechoreographer AS name FROM choreographers");
    $choreographersStmt->execute();
    $choreographers = $choreographersStmt->fetchAll(PDO::FETCH_ASSOC);

    // Выборка расписаний
    $schedulesStmt = $pdo->prepare("SELECT s.*, c.namechoreographer AS instructor_name, st.namestyles 
        FROM schedule s
        JOIN choreographers c ON s.idchoreographer = c.idchoreographer
        JOIN styles st ON s.idstyles = st.idstyles");
    $schedulesStmt->execute();
    $schedules = $schedulesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Выборка групп (по расписанию)
    $groupsStmt = $pdo->prepare("SELECT sch.idschedule, sch.dayschedule, sch.timeschedule, 
                                 c.namechoreographer AS instructor_name, st.namestyles 
                                 FROM schedule sch
                                 JOIN choreographers c ON sch.idchoreographer = c.idchoreographer
                                 JOIN styles st ON sch.idstyles = st.idstyles
                                 GROUP BY sch.idschedule, sch.idchoreographer, sch.idstyles, sch.dayschedule, sch.timeschedule");
    $groupsStmt->execute();
    $groups = $groupsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

function translateForWhom($value)
{
    $parts = explode(', ', $value);
    $translations = ['self' => 'себя', 'child' => 'ребенка'];
    $translatedParts = array_map(function ($part) use ($translations) {
        return $translations[$part] ?? $part;
    }, $parts);
    return implode(', ', $translatedParts);
}

function translateDancedBefore($value)
{
    $translations = ['yes' => 'да', 'no' => 'нет'];
    return $translations[$value] ?? $value;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && isset($_POST['idregistrations']) && isset($_POST['newPhoneCallStatus'])) {
    $id = $_POST['idregistrations'];
    $newStatus = $_POST['newPhoneCallStatus'];
    // Проверка на корректность значения статуса
    $validStatuses = ['Бронь подтверждена', 'Перенос', 'Не ответил'];
    if (in_array($newStatus, $validStatuses)) {
        $updateStmt = $pdo->prepare("UPDATE registrations SET phonecalls = :newStatus WHERE idregistrations = :id");
        $updateStmt->execute(['newStatus' => $newStatus, 'id' => $id]);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Недопустимый статус.";
    }
}

// Обработка формы добавления студента
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $fullname = $_POST['fullname'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $idschedule = $_POST['idschedule'];

    // Добавление студента
    $addStudentStmt = $pdo->prepare("INSERT INTO students (fullname, birthdate, address, phone) VALUES (:fullname, :birthdate, :address, :phone)");
    $addStudentStmt->execute(['fullname' => $fullname, 'birthdate' => $birthdate, 'address' => $address, 'phone' => $phone]);

    $idstudent = $pdo->lastInsertId();

    // Добавление студента в расписание
    $addScheduleMemberStmt = $pdo->prepare("INSERT INTO group_members (idschedule, idstudent) VALUES (:idschedule, :idstudent)");
    $addScheduleMemberStmt->execute(['idschedule' => $idschedule, 'idstudent' => $idstudent]);

    // Перенаправление на ту же страницу для предотвращения повторной отправки формы
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$stylesStmt = $pdo->prepare("SELECT idstyles, namestyles FROM styles");
$stylesStmt->execute();
$styles = $stylesStmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка формы для добавления записи в choreographers_style
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addChoreographerStyle']) && isset($_POST['idchoreographer']) && isset($_POST['idstyles'])) {
    $idchoreographer = $_POST['idchoreographer'];
    $idstyles = $_POST['idstyles'];

    $stmt = $pdo->prepare("INSERT INTO choreographers_style (idchoreographer, idstyles) VALUES (:idchoreographer, :idstyles)");
    $stmt->bindParam(':idchoreographer', $idchoreographer);
    $stmt->bindParam(':idstyles', $idstyles);

    if ($stmt->execute()) {
        echo "Стиль успешно добавлен к мастеру";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}

// Получение данных для выпадающих списков
$choreographers = $pdo->query("SELECT idchoreographer, namechoreographer FROM choreographers")->fetchAll(PDO::FETCH_ASSOC);
$styles = $pdo->query("SELECT idstyles, namestyles FROM styles")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница для авторизованных пользователей</title>
    <link rel="stylesheet" href="css/user-list.css">
    <script>
        /*
        function showGroupInfo(groupId) {
            var groups = <?= json_encode($groups) ?>;
            var groupInfo = groups.find(group => group.idgroup == groupId);

            if (groupInfo) {
                document.getElementById('group-info').innerHTML = `
                    <p><strong>Время начала:</strong> ${groupInfo.start}</p>
                    <p><strong>Конец занятия:</strong> ${groupInfo.end}</p>
                    <p><strong>День события:</strong> ${groupInfo['the day of the event']}</p>
                    <p><strong>Инструктор:</strong> ${groupInfo.instructor_name}</p>
                    <p><strong>Стиль:</strong> ${groupInfo.namestyles}</p>
                `;
                
                // Выборка студентов для данной группы
                fetch('functions/get_group_students.php?group_id=' + groupId)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            var studentsList = data.map((student, index) => `<li>${index + 1}. ${student.fullname} (${student.phone})</li>`).join('');
                            document.getElementById('group-students').innerHTML = '<ul>' + studentsList + '</ul>';
                        } else {
                            document.getElementById('group-students').innerHTML = 'Нет данных о студентах.';
                        }
                    });
            }
        }*/
        function showGroupInfo(scheduleId) {
            var groups = <?= json_encode($groups) ?>;
            var groupInfo = groups.find(group => group.idschedule == scheduleId);

            if (groupInfo) {
                document.getElementById('group-info').innerHTML = `
                    <p><strong>День занятия:</strong> ${groupInfo.dayschedule}</p>
                    <p><strong>Время занятия:</strong> ${groupInfo.timeschedule}</p>
                    <p><strong>Инструктор:</strong> ${groupInfo.instructor_name}</p>
                    <p><strong>Стиль:</strong> ${groupInfo.namestyles}</p>
                `;

                // Выборка студентов для данной группы
                fetch('functions/get_schedule_students.php?schedule_id=' + scheduleId)
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            var studentsList = data.map((student, index) => `<li>${index + 1}. ${student.fullname} (${student.phone})</li>`).join('');
                            document.getElementById('group-students').innerHTML = '<ul>' + studentsList + '</ul>';
                        } else {
                            document.getElementById('group-students').innerHTML = 'Нет данных о студентах.';
                        }
                    });
            }
        }

        function addChoreographer(event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById('add-choreographer-form'));
            fetch('functions/add_choreographer.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Мастер добавлен успешно');
                        // Добавить мастера в список
                        var select = document.getElementById('idchoreographer');
                        var option = document.createElement('option');
                        option.value = data.idchoreographer;
                        option.text = formData.get('name');
                        select.add(option);
                        document.getElementById('add-choreographer-form').reset();
                    } else {
                        alert('Ошибка при добавлении мастера');
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        }

        function addGroup(event) {
            event.preventDefault();
            var formData = new FormData(document.getElementById('add-group-form'));
            fetch('functions/add_group.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Группа добавлена успешно');
                        // Добавить группу в список
                        var select = document.getElementById('idgroup');
                        var option = document.createElement('option');
                        option.value = data.idgroup;
                        option.text = formData.get('namestyles') + ' - ' + formData.get('instructor_name');
                        select.add(option);
                        document.getElementById('add-group-form').reset();
                    } else {
                        alert('Ошибка при добавлении группы');
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        }
    </script>
</head>

<body>
    <div id="logout-button" style="padding: 20px;">
        <form action="functions/logout.php" method="POST">
            <button type="submit" name="logout">Выйти</button>
        </form>
    </div>
    <div id="authorized-section">
        <h2>Записи студии танцев</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Полное имя</th>
                <th>Дата рождения</th>
                <th>Телефон</th>
                <th>Для кого</th>
                <th>Танцевал ранее</th>
                <th>Стиль</th>
                <th>Дополнительно</th>
                <th>Дата создания</th>
                <th>Телефонные звонки</th>
                <th>Изменить статус</th>
            </tr>
            <?php foreach ($registrations as $row) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['idregistrations']) ?></td>
                    <td><?= htmlspecialchars($row['fullName']) ?></td>
                    <td><?= htmlspecialchars($row['birthDate']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars(translateForWhom($row['forWhom'])) ?></td>
                    <td><?= htmlspecialchars(translateDancedBefore($row['dancedBefore'])) ?></td>
                    <td><?= htmlspecialchars($row['namestyles']) ?></td>
                    <td><?= htmlspecialchars($row['additional']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['phonecalls']) ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="idregistrations" value="<?= $row['idregistrations'] ?>">
                            <select name="newPhoneCallStatus">
                                <option value="Бронь подтверждена">Бронь подтверждена</option>
                                <option value="Перенос">Перенос</option>
                                <option value="Не ответил">Не ответил</option>
                            </select>
                            <button type="submit" name="update">Обновить</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <h2>Добавить студента</h2>
        <form action="" method="POST">
            <label for="fullname">Полное имя:</label>
            <input type="text" id="fullname" name="fullname" required><br>

            <label for="birthdate">Дата рождения:</label>
            <input type="date" id="birthdate" name="birthdate" required><br>

            <label for="address">Адрес:</label>
            <textarea id="address" name="address" required></textarea><br>

            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="idschedule">Группа:</label>
            <select id="idschedule" name="idschedule" required>
                <?php foreach ($schedules as $schedule) { ?>
                    <option value="<?= $schedule['idschedule'] ?>"><?= htmlspecialchars($schedule['namestyles']) ?> - <?= htmlspecialchars($schedule['instructor_name']) ?> - <?= htmlspecialchars($schedule['dayschedule']) ?> - <?= htmlspecialchars($schedule['timeschedule']) ?></option>
                <?php } ?>
            </select><br>

            <button type="submit" name="add_student">Добавить студента</button>
        </form>

        <h2>Выбрать группу</h2>
        <select onchange="showGroupInfo(this.value)">
            <option value="">Выбор группы</option>
            <?php foreach ($schedules as $schedule) { ?>
                <option value="<?= $schedule['idschedule'] ?>"><?= htmlspecialchars($schedule['namestyles']) ?> - <?= htmlspecialchars($schedule['instructor_name']) ?> - <?= htmlspecialchars($schedule['dayschedule']) ?> - <?= htmlspecialchars($schedule['timeschedule']) ?></option>
            <?php } ?>
        </select>


        <div id="group-info">
            <!-- Информация о выбранной группе будет отображаться здесь -->
        </div>

        <div id="group-students">
            <!-- Список студентов в выбранной группе будет отображаться здесь -->
        </div>

        <h2>Добавить мастера</h2>
        <form id="add-choreographer-form" onsubmit="addChoreographer(event)">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="experience">Опыт:</label>
            <textarea id="experience" name="experience" required></textarea><br>

            <button type="submit">Добавить мастера</button>
        </form>
        <h1>Add Choreographer Style</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="addChoreographerStyle" value="1">

            <label for="choreographer">Select Choreographer:</label>
            <select id="choreographer" name="idchoreographer" required>
                <?php
                if (count($choreographers) > 0) {
                    foreach ($choreographers as $choreographer) {
                        echo "<option value='" . $choreographer['idchoreographer'] . "'>" . $choreographer['namechoreographer'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Нету доступных мастеров</option>";
                }
                ?>
            </select><br><br>

            <label for="style">Select Style:</label>
            <select id="style" name="idstyles" required>
                <?php
                if (count($styles) > 0) {
                    foreach ($styles as $style) {
                        echo "<option value='" . $style['idstyles'] . "'>" . $style['namestyles'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Нету доступных стилей</option>";
                }
                ?>
            </select><br><br>

            <input type="submit" value="Отправить">
        </form>
    </div>
</body>

</html>