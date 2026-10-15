<?php
require_once __DIR__ . "/auth/auth.php";
require_once __DIR__ . "/operations.php";

try {
    $filters = operationFilters($_GET);
    $conditions = [];
    $parameters = [];

    if ($filters["date_start"] !== "") {
        $conditions[] = "date_operation >= :date_start";
        $parameters["date_start"] = $filters["date_start"];
    }
    if ($filters["date_end"] !== "") {
        $conditions[] = "date_operation <= :date_end";
        $parameters["date_end"] = $filters["date_end"];
    }

    $sql = "SELECT date_operation, " . implode(", ", OPERATION_FIELDS) . ", total_operations
            FROM operations";
    if ($conditions !== []) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $sql .= " ORDER BY date_operation DESC";

    $statement = $pdo->prepare($sql);
    $statement->execute($parameters);

    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=operations.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, array_merge(["date_operation"], OPERATION_FIELDS, ["total_operations"]));
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
} catch (InvalidArgumentException $exception) {
    http_response_code(400);
    require __DIR__ . "/../errors/400.html";
    exit();
}
