function changeExportType() {

    let type = document.getElementById("exportType").value;

    let singleDate = document.getElementById("singleDate");
    let dateRange = document.getElementById("dateRange");

    singleDate.style.display = "none";
    dateRange.style.display = "none";

    if (type === "date") {
        singleDate.style.display = "block";
    }

    if (type === "range") {
        dateRange.style.display = "block";
    }
}