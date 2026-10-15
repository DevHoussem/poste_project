<?php

const OPERATION_FIELDS = [
    "paiement_mandat",
    "emission_mandat",
    "retrait_ccp",
    "versement_ccp",
    "retrait_carnet",
    "versement_carnet",
    "western_union",
];

function operationInput(array $input): array
{
    $date = trim((string) ($input["date_operation"] ?? ""));
    $errors = [];

    $dateObject = DateTime::createFromFormat("!Y-m-d", $date);
    if (!$dateObject || $dateObject->format("Y-m-d") !== $date) {
        $errors[] = "Please provide a valid operation date.";
    }

    $values = ["date_operation" => $date];
    foreach (OPERATION_FIELDS as $field) {
        $rawValue = $input[$field] ?? null;
        if ($rawValue === null || filter_var($rawValue, FILTER_VALIDATE_INT) === false || (int) $rawValue < 0) {
            $errors[] = "All operation values must be non-negative whole numbers.";
            continue;
        }

        $values[$field] = (int) $rawValue;
    }

    if ($errors !== []) {
        throw new InvalidArgumentException(implode(" ", array_unique($errors)));
    }

    $values["total_operations"] = array_sum(
        array_map(static fn(string $field): int => $values[$field], OPERATION_FIELDS)
    );

    return $values;
}

function ensureCsrfToken(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function verifyCsrfToken(?string $token): void
{
    if (!$token || empty($_SESSION["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $token)) {
        throw new RuntimeException("Your session expired. Please try again.");
    }
}

function operationFilters(array $input): array
{
    $start = trim((string) ($input["date_start"] ?? ""));
    $end = trim((string) ($input["date_end"] ?? ""));

    foreach (["date_start" => $start, "date_end" => $end] as $name => $date) {
        if ($date !== "") {
            $dateObject = DateTime::createFromFormat("!Y-m-d", $date);
            if (!$dateObject || $dateObject->format("Y-m-d") !== $date) {
                throw new InvalidArgumentException("Please provide valid filter dates.");
            }
        }
    }

    if ($start !== "" && $end !== "" && $start > $end) {
        throw new InvalidArgumentException("The start date cannot be after the end date.");
    }

    return ["date_start" => $start, "date_end" => $end];
}
