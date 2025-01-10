<?php
// connecting to database
$conn = mysqli_connect("localhost", "root", "", "bantrangsuc-chatbot") or die("Database Error");

// getting user message through ajax
$getMesg = mysqli_real_escape_string($conn, $_POST['text']);

//checking user query to database query
$slug = slugify_vi($getMesg);
$question = explode('-', $slug);
$name_product = count($question) > 2 ? implode('-', array_slice($question, count($question) - 2, 2)) : '';
if (count($question) > 2) {
    $question = array_slice($question, 0, 3);
}
$question = implode('-', $question);
$check_data = "SELECT * FROM chatbot WHERE queries LIKE '%$getMesg%' or queries LIKE '%$question%'";
$run_query = mysqli_query($conn, $check_data) or die("Error");

// if user query matched to database query we'll show the reply otherwise it goes to else statement
if (mysqli_num_rows($run_query) > 0) {
    //fetching reply from the database according to the user query
    $fetch_data = mysqli_fetch_assoc($run_query);
    //storing reply to a variable which we'll send to ajax
    $reply = $fetch_data['replies'];

    if ($fetch_data['type'] == 1) {
        // retrieve data product categories from database
        $get_product_categories = "SELECT * FROM product_categories";
        $fetch_product_categories = mysqli_query($conn, $get_product_categories);
        $result_product_categories = [];
        while ($row = mysqli_fetch_assoc($fetch_product_categories)) {
            if (str_contains(slugify_vi($row['product_category_title']), $name_product)) {
                $result_product_categories[] = $row;
            }
        }
        if (count($result_product_categories) === 0) {
            echo 'Hiện tại sản phẩm này chúng tôi chưa có! Bạn có thể tìm kiếm các sản phẩm khác';
        } else {
            echo $reply;
            foreach ($result_product_categories as $result_category) {
                echo '
                <div class="">
                    <a href="./shop.php?product_category=' . $result_category['product_category_id'] . '" target="_blank">
                        ' . $result_category['product_category_title'] . '
                    </a>
                </div><br>';
            }
        }
    } elseif ($fetch_data['type'] == 2) {
        // retrieve data products from database
        $get_products = "SELECT * FROM products";
        $fetch_products = mysqli_query($conn, $get_products);
        $result_products = [];
        while ($row = mysqli_fetch_assoc($fetch_products)) {
            if (str_contains(slugify_vi($row['product_title']), $name_product)) {
                $result_products[] = $row;
            }
        }
        if (count($result_products) === 0) {
            echo 'Hiện tại sản phẩm này chúng tôi chưa có! Bạn có thể tìm kiếm các sản phẩm khác';
        } else {
            echo $reply;
            foreach ($result_products as $product) {
                echo '
                <div class="">
                    <a href="./details.php?product_id=' . $product['product_id'] . '" target="_blank">
                        ' . $product['product_title']. '
                    </a>
                </div><br>';
            }
        }
    } elseif ($fetch_data['type'] == 3) {
        // Provide a button linking to intro.php
        echo $reply;
        echo '
        <div class="">
            <a href="./intro.php" <br> Nhấn vào đây>
            <button type="button"></button>
            </a>
        </div><br>';
    } else {
        echo $reply;
    }
} else {
    echo "Xin lỗi không thể thể hiểu câu hỏi của bạn!";
}

function slugify_vi($text, string $divider = '-', bool $transliterate = false): string
{
    // Make the string lowercase
    $text = mb_strtolower($text, 'UTF-8');

    // Replace non-letter or digits with the divider
    $text = preg_replace('~[^\p{L}\d]+~u', $divider, $text);

    // Convert accented characters to their non-accented equivalents
    $text = remove_accents($text);

    // Optionally transliterate non-latin characters
    if ($transliterate) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // Remove any remaining unwanted characters
    $text = trim(preg_replace('~[^-\w]+~', '', $text), $divider);

    // Remove duplicate dividers
    $text = preg_replace('~-+~', $divider, $text);

    return $text;
}

// Function to remove accents from Vietnamese characters
function remove_accents($text)
{
    $vietnameseMap = array(
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ặ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        'đ' => 'd',
        'Đ' => 'D'
    );

    return strtr($text, $vietnameseMap);
}
?>
