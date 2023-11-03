<?php
require_once 'header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unisex Каталог</title>
    <link rel="stylesheet" type="text/css" href="/css/catalog.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="/node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="/node_modules/select2/dist/js/select2.min.js"></script>
</head>
<body>

<?php
require_once 'header.php';

$servername = "127.0.0.1";
$username = "rovel";
$password = "12072004Pavel";
$dbname = "devauled";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<label class="label">Сортировать по:</label>
<select id="sort">
    <option class="select-option" value="popularity" data-circle="false" data-icon="/images/empty-circle.png">По популярности</option>
    <option class="select-option" value="ascend" data-circle="false" data-icon="/images/empty-circle.png">По возрастанию</option>
    <option class="select-option" value="descend" data-circle="false" data-icon="/images/empty-circle.png">По убыванию</option>
</select>
<button class="filter-button" type="button">Филтры</button>
<div class="main-container">
    <?php
    $product_class = isset($_GET['class']) ? $_GET['class'] : null;

    // The SQL query will filter products by class if it is provided.
    $sql = "SELECT * FROM products WHERE gender='Unisex'";
    if ($product_class !== null) {
        $sql .= " AND product_class='" . $conn->real_escape_string($product_class) . "'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="product-card">';
            echo '  <a href="product_details.php?id=' . $row["id"] . '" style="text-decoration: none; color: inherit;">';
            echo '    <div class="image-container">';
            echo '      <div class="heart" onclick="toggleHeart(event, this)">';
            echo '        <img src="/images/heart_empty.png" alt="Like">';
            echo '      </div>';
            echo '      <img src="' . $row["image"] . '" alt="' . $row["name"] . '">';
            echo '    </div>';
            echo '    <h3>' . $row["name"] . '</h3>';
            echo '    <p class="price">' . $row["price"] . '₽</p>';
            echo '  </a>';
            echo '</div>';
        }
    } else {
        echo "0 results";
    }

    $conn->close();
    ?>

</div>
<script>
    function toggleHeart(event, element) {
        event.preventDefault();  // Отменить стандартное действие
        event.stopPropagation();  // Остановить всплытие события
        var img = element.querySelector("img");
        if (img.getAttribute("src") === "/images/heart_empty.png") {
            img.setAttribute("src", "/images/heart_filled.png");
        } else {
            img.setAttribute("src", "/images/heart_empty.png");
        }
    }
    function showDetails(id) {
        window.location.href = "product_details.php?id=" + id;
    }
    $(document).ready(function() {
        var selectedId = null;

        function formatState(state) {
            if (!state.id) return state.text;
            var circleClass = (selectedId === state.id) ? 'circle-filled' : 'circle-empty';
            return $('<span><div class="' + circleClass + '"></div> ' + state.text + '</span>');
        }

        function formatSelection(state) {
            return state.text;
        }

        var $sortSelect = $('#sort').select2({
            minimumResultsForSearch: -1,
            templateResult: formatState,
            templateSelection: formatSelection,
            closeOnSelect: false
        });

        $sortSelect.on('select2:select', function(e) {
            selectedId = e.params.data.id;
            $sortSelect.select2('destroy').select2({
                minimumResultsForSearch: -1,
                templateResult: formatState,
                templateSelection: formatSelection,
                closeOnSelect: false
            });
        });

        $(document).on('mousedown', function(e) {
            if (!$('.select2-dropdown').has(e.target).length) {
                $sortSelect.select2('close');
            }
        });
    });

</script>
</body>
</html>