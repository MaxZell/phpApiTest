<?php
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit();
    }

    if (!isset($_GET['empid']) || empty($_GET['empid'])) {
        http_response_code(400);
        exit();
    }

    $host = 'localhost';
    $db   = 'planning';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    $stmt = $pdo->prepare("select WorkWeek, trim(PlannedPercent*100)+0 as PlannedPercent from v_transfersoxestime where EmployeeId=:employeeId and StartDate >=curdate() group by WorkWeek order by StartDate DESC");

    $stmt->execute(['employeeId' => $_GET['empid']]);
    $rows = $stmt->fetchAll();

    header("Content-type: application/json");
    echo json_encode($rows);
?>