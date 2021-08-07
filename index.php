<?php
require 'classes/EmployeesApi.php';
require 'classes/DotEnv.php';
require 'classes/HelperFunctions.php';

(new DotEnv(__DIR__ . '/config/.env'))->load();

$username = getenv('API_USERNAME');
$password = getenv('API_PASSWORD');
$urlEmployeesList = getenv('API_BASE_URL') . '/list';

$employeesApi = new EmployeesApi($username, $password);
$apiResult = $employeesApi->getApiResponse($urlEmployeesList);
$apiProfilesResult = json_decode($apiResult, true);

//echo count($result);exit;

$responseError = false;
if (isset($apiProfilesResult['api_error'])) {
    $responseError = $apiProfilesResult['api_error'];
}

if (!$responseError) {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// The number of records to display per page
    $page_size = 9;

// Calculate the position of the first record of the page to display
    $offset = ($page - 1) * $page_size;

// Get the subset of records to be displayed from the array
    $currentProfilesArray = array_slice($apiProfilesResult, $offset, $page_size);

    $total_records = count($apiProfilesResult);
    $total_pages = ceil($total_records / $page_size);

    if ($page > $total_pages) {
        $page = $total_pages;
    }

// Page to display can not be less than 1
    if ($page < 1) {
        $page = 1;
    }
}
?>

<?php include 'inc/html/header.php' ?>

<?php if (!$responseError) : ?>

    <div class="record-count"><?= " About $total_records results" ?></div>
    <div class="profiles">
        <h2>Employees list</h2>
        <ul>
            <?php
            HelperFunctions::renderProfilesFromArray($currentProfilesArray);
            ?>
        </ul>

        <div class="pagination">
            <?php
            HelperFunctions::renderPaginationWithPrevAndNextLinks($page, $total_pages);
            ?>
        </div>
    </div>

<?php else : ?>
    <div class="error-response">
        <?= $responseError ?>
    </div>
<?php endif; ?>

<?php include 'inc/html/footer.php' ?>