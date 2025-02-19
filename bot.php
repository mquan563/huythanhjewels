<?php
$api_key = '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';
$api_url = 'https://api.openai.com/v1/chat/completions';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = strtolower(trim($_POST['text']));

    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ["role" => "system", "content" => "Bạn là nhân viên tư vấn chuyên nghiệp của shop trang sức HuyThanhJewelry. Hãy trả lời khách hàng một cách thân thiện và chuyên nghiệp, nhưng không vượt quá 218 ký tự."],
            ["role" => "user", "content" => $userMessage]
        ],
        'max_tokens' => 150
    ];

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        echo $result['choices'][0]['message']['content'];
    } else {
        echo "Xin lỗi, tôi chưa hiểu ý bạn. Bạn muốn tìm hiểu sản phẩm nào ạ?";
    }
    exit;
}
?>
