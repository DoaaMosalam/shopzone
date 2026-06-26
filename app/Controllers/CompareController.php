<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Review;

class CompareController extends Controller
{
    private Product $productModel;
    private Review $reviewModel;

    // ضع مفتاح الـ API الخاص بـ Gemini هنا (أو استخرجه من ملف config لاحقاً)
    private string $apiKey = 'AIzaSyAe7cX3BXI3x4GnVJwlPoIwUltJpjvCR_A';

    public function __construct()
    {
        $this->productModel = new Product();
        $this->reviewModel  = new Review();
    }

    /**
     * GET /compare
     * يعرض صفحة المقارنة ويجلب المنتجات المحددة
     */
    public function index(): void
    {
        $p1_id = (int) $this->get('p1', 0);
        $p2_id = (int) $this->get('p2', 0);

        $product1 = null;
        $product2 = null;
        $reviews1 = [];
        $reviews2 = [];

        if ($p1_id) {
            $product1 = $this->productModel->findDetail($p1_id);
            if ($product1) {
                $reviews1 = $this->reviewModel->forProduct($p1_id);
            }
        }

        if ($p2_id) {
            $product2 = $this->productModel->findDetail($p2_id);
            if ($product2) {
                $reviews2 = $this->reviewModel->forProduct($p2_id);
            }
        }

        // جلب قائمة بكل المنتجات لملء قوائم الاختيار (Select Dropdowns) في الواجهة
        $allProducts = $this->productModel->all('Name ASC');

        $this->render('compare.index', [
            'product1'    => $product1,
            'product2'    => $product2,
            'reviews1'    => $reviews1,
            'reviews2'    => $reviews2,
            'allProducts' => $allProducts,
            'p1_id'       => $p1_id,
            'p2_id'       => $p2_id
        ]);
    }

    /**
     * POST /compare/analyze
     * معالجة طلب الذكاء الاصطناعي عبر cURL وإعادة النتيجة كـ JSON لـ AJAX
     */
    public function analyze(): void
    {
        header('Content-Type: application/json');
        
        if (!$this->isPost()) {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $p1_id = (int) $this->post('p1', 0);
        $p2_id = (int) $this->post('p2', 0);

        $p1 = $this->productModel->findDetail($p1_id);
        $p2 = $this->productModel->findDetail($p2_id);

        if (!$p1 || !$p2) {
            echo json_encode(['error' => 'يرجى اختيار منتجين صالحين للمقارنة.']);
            return;
        }

        $rev1 = $this->reviewModel->forProduct($p1_id);
        $rev2 = $this->reviewModel->forProduct($p2_id);

        // 1. صياغة الـ Prompt الموجه للذكاء الاصطناعي بدقة بناءً على المواصفات والمراجعات المستخرجة
        $prompt = $this->buildPrompt($p1, $p2, $rev1, $rev2);

        // 2. الاتصال بـ Google Gemini API
        $response = $this->callGemini($prompt);

        echo json_encode(['analysis' => $response]);
    }

    /**
     * بناء الـ Prompt الذي يجمع كل خيوط الجداول
     */
    private function buildPrompt(array $p1, array $p2, array $rev1, array $rev2): string
    {
        $prompt = "أنت خبير تقني ومحلل مشتريات ذكي في متجرنا الإلكتروني (ShopZone). مطلوب منك إجراء مقارنة احترافية وحاسمة باللغة العربية بين منتجين بناءً على مواصفاتهما الفنية وآراء المشترين الحقيقية.\n\n";
        
        // تفاصيل المنتج الأول
        $prompt .= "المنتج الأول:\n";
        $prompt .= "- الاسم: {$p1['Name']}\n";
        $prompt .= "- الماركة: {$p1['Brand']}\n";
        $prompt .= "- السعر: {$p1['Price']}\n";
        $prompt .= "- المواصفات التقنية:\n";
        foreach ($p1['specs'] as $spec) {
            $prompt .= "  * {$spec['Spec_Key']}: {$spec['Spec_Value']}\n";
        }
        $prompt .= "- مراجعات العملاء الحقيقية:\n";
        foreach (array_slice($rev1, 0, 5) as $r) {
            $prompt .= "  * تعليق: {$r['Comment']} (تحليل مشاعر العميل الحالي: {$r['AI_Sentiment']})\n";
        }
        
        $prompt .= "\n----------------------------------------\n\n";

        // تفاصيل المنتج الثاني
        $prompt .= "المنتج الثاني:\n";
        $prompt .= "- الاسم: {$p2['Name']}\n";
        $prompt .= "- الماركة: {$p2['Brand']}\n";
        $prompt .= "- السعر: {$p2['Price']}\n";
        $prompt .= "- المواصفات التقنية:\n";
        foreach ($p2['specs'] as $spec) {
            $prompt .= "  * {$spec['Spec_Key']}: {$spec['Spec_Value']}\n";
        }
        $prompt .= "- مراجعات العملاء الحقيقية:\n";
        foreach (array_slice($rev2, 0, 5) as $r) {
            $prompt .= "  * تعليق: {$r['Comment']} (تحليل مشاعر العميل الحالي: {$r['AI_Sentiment']})\n";
        }

        $prompt .= "\n----------------------------------------\n\n";
        $prompt .= "المطلوب منك صياغة تقرير مقارنة منظم جداً يحتوي على:\n";
        $prompt .= "1. تحليل نقاط القوة والضعف لكل منتج تقنياً مقارنة بسعره.\n";
        $prompt .= "2. ملخص لتوجه مراجعات العملاء (هل يشتكون من عيب مصنعي مشترك؟ هل يمدحون البطارية؟ وهكذا).\n";
        $prompt .= "3. حكم نهائي قاطع: أيهما يقدم القيمة الأفضل مقابل السعر (Value for Money) ولمن تتوجه كل نصيحة شراء؟ واكتب التقرير بتنسيق Markdown رائع ومريح للعين.";

        return $prompt;
    }

    /**
     * دالة الـ cURL للاتصال بـ Gemini API
     */

    // private function callGemini(string $prompt): string
    // {
    //     // استخدام موديل Gemini 1.5 Flash السريع والمجاني والمتطور
    //     $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

    //     $data = [
    //         "contents" => [
    //             [
    //                 "parts" => [
    //                     ["text" => $prompt]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/json'
    //     ]);
    //     // لضمان عدم توقف السكريبت لو تأخر السيرفر
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 20); 

    //     $response = curl_exec($ch);
        
    //     if (curl_errno($ch)) {
    //         $error_msg = curl_error($ch);
    //         curl_close($ch);
    //         return "عذراً، حدث خطأ أثناء الاتصال بالذكاء الاصطناعي: " . $error_msg;
    //     }

    //     curl_close($ch);

    //     $result = json_encode($response);
    //     $resultData = json_decode($response, true);

    //     // استخراج النص الراجع من الهيكل الخاص بـ Gemini
    //     if (isset($resultData['candidates'][0]['content']['parts'][0]['text'])) {
    //         return $resultData['candidates'][0]['content']['parts'][0]['text'];
    //     }

    //     return "لم يتمكن الذكاء الاصطناعي من معالجة التقرير حالياً، يرجى التحقق من إعدادات الـ API Key.";
    // }
  
  private function callGemini(string $prompt): string
  {
    // استخدام الموديل القياسي الحالي المستقر والمجاني عبر الإصدار v1 الرسمي
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 

    // تجاوز فحص شهادة الـ SSL لحل مشاكل الاتصال المحلي في بيئة XAMPP/Localhost
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "عذراً، حدث خطأ في الاتصال (cURL): " . $error_msg;
    }

    curl_close($ch);

    $resultData = json_decode($response, true);

    // طباعة الخطأ القادم من السيرفر إذا وجد
    if (isset($resultData['error'])) {
        return "خطأ من سيرفر Google API: " . ($resultData['error']['message'] ?? 'غير معروف');
    }

    // استخراج النص الراجع بناءً على الهيكل المعتمد
    if (isset($resultData['candidates'][0]['content']['parts'][0]['text'])) {
        return $resultData['candidates'][0]['content']['parts'][0]['text'];
    }

    return "استجابة غير معروفة من السيرفر. يرجى التحقق من هيكل الرد الصادر.";
  }
}