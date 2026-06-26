<?php $pageTitle = 'مقارنة المنتجات بالذكاء الاصطناعي - ShopZone'; ?>

<div class="container" style="direction: rtl; text-align: right; padding: 20px 0;">
    
    <div class="compare-header" style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 10px;">المقارنة الذكية المدعومة بـ AI 🤖</h1>
        <p style="color: #7f8c8d; font-size: 1.1rem;">اختر منتجين للمقارنة بين المواصفات التقنية الفنية وتحليل مشاعر مراجعات العملاء</p>
    </div>

    <form method="GET" action="<?= url('compare') ?>" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e2e8f0;">
        <div style="display: flex; gap: 20px; justify-content: center; align-items: flex-end; flex-wrap: wrap;">
            
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #34495e;">المنتج الأول:</label>
                <select name="p1" class="form-control" style="width: 100%; height: 42px; border-radius: 6px;">
                    <option value="0">-- اختر المنتج الأول --</option>
                    <?php foreach ($allProducts as $p): ?>
                        <option value="<?= $p['Product_ID'] ?>" <?= $p1_id == $p['Product_ID'] ? 'selected' : '' ?>>
                            <?= eXSS($p['Name']) ?> (<?= eXSS($p['Brand']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="font-size: 1.5rem; font-weight: bold; color: #bdc3c7; align-self: center; margin: 0 10px;">VS</div>

            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #34495e;">المنتج الثاني:</label>
                <select name="p2" class="form-control" style="width: 100%; height: 42px; border-radius: 6px;">
                    <option value="0">-- اختر المنتج الثاني --</option>
                    <?php foreach ($allProducts as $p): ?>
                        <option value="<?= $p['Product_ID'] ?>" <?= $p2_id == $p['Product_ID'] ? 'selected' : '' ?>>
                            <?= eXSS($p['Name']) ?> (<?= eXSS($p['Brand']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn--primary" style="height: 42px; padding: 0 30px; font-weight: bold;">قارن الآن</button>
            </div>
        </div>
    </form>

    <?php if ($product1 && $product2): ?>
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 40px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <thead>
                    <tr style="background: #34495e; color: #fff;">
                        <th style="padding: 15px; width: 20%;">الخاصية</th>
                        <th style="padding: 15px; width: 40%; font-size: 1.2rem;"><?= eXSS($product1['Name']) ?></th>
                        <th style="padding: 15px; width: 40%; font-size: 1.2rem;"><?= eXSS($product2['Name']) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #edf2f7;">
                      <td style="padding: 15px; font-weight: bold; background: #fdfefe; vertical-align: middle;">الصورة</td>
                      
                      <td style="padding: 15px; text-align: center; vertical-align: middle;">
                          <img src="<?= eXSS(product_image($product1['Image_URL'] ?? null)) ?>"
                              alt="<?= eXSS($product1['Name']) ?>"
                              style="width: 100%; max-width: 200px; height: 200px; object-fit: contain; display: block; margin: 0 auto;"
                              loading="lazy"
                              onerror="this.onerror=null;this.src='<?= asset('images/no-image.svg') ?>';"> 
                      </td>
                      
                      <td style="padding: 15px; text-align: center; vertical-align: middle;">
                          <img src="<?= eXSS(product_image($product2['Image_URL'] ?? null)) ?>"
                              alt="<?= eXSS($product2['Name']) ?>"
                              style="width: 100%; max-width: 200px; height: 200px; object-fit: contain; display: block; margin: 0 auto;"
                              loading="lazy"
                              onerror="this.onerror=null;this.src='<?= asset('images/no-image.svg') ?>';"> 
                      </td>
                  </tr>
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 15px; font-weight: bold; background: #fdfefe;">الماركة</td>
                        <td style="padding: 15px; font-size: 1.1rem;"><?= eXSS($product1['Brand'] ?? '-') ?></td>
                        <td style="padding: 15px; font-size: 1.1rem;"><?= eXSS($product2['Brand'] ?? '-') ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 15px; font-weight: bold; background: #fdfefe;">السعر</td>
                        <td style="padding: 15px; color: #e74c3c; font-weight: bold; font-size: 1.2rem;"><?= money($product1['Price']) ?></td>
                        <td style="padding: 15px; color: #e74c3c; font-weight: bold; font-size: 1.2rem;"><?= money($product2['Price']) ?></td>
                    </tr>
                    
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 15px; font-weight: bold; background: #fdfefe;">تقييم المتجر</td>
                        <td style="padding: 15px;">
                            <?= \Core\View::stars((float) $product1['Rating_No']) ?> (<?= number_format((float) $product1['Rating_No'], 1) ?>/5)
                        </td>
                        <td style="padding: 15px;">
                            <?= \Core\View::stars((float) $product2['Rating_No']) ?> (<?= number_format((float) $product2['Rating_No'], 1) ?>/5)
                        </td>
                    </tr>

                    <?php 
                    // تجميع كل المفاتيح الفريدة للمواصفات من المنتجين
                    $allKeys = [];
                    foreach ($product1['specs'] as $s) $allKeys[] = $s['Spec_Key'];
                    foreach ($product2['specs'] as $s) $allKeys[] = $s['Spec_Key'];
                    $allKeys = array_unique($allKeys);
                    
                    foreach ($allKeys as $key): 
                        $val1 = '-';
                        $val2 = '-';
                        foreach ($product1['specs'] as $s) if ($s['Spec_Key'] === $key) $val1 = $s['Spec_Value'];
                        foreach ($product2['specs'] as $s) if ($s['Spec_Key'] === $key) $val2 = $s['Spec_Value'];
                    ?>
                    <tr style="border-bottom: 1px solid #edf2f7;">
                        <td style="padding: 12px; font-weight: bold; background: #fdfefe; color: #7f8c8d;"><?= eXSS($key) ?></td>
                        <td style="padding: 12px;"><?= eXSS($val1) ?></td>
                        <td style="padding: 12px;"><?= eXSS($val2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="background: #fff; border: 1px solid #3498db; border-radius: 8px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative;">
            <h3 style="color: #2980b9; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <span>تقرير التحليل الذكي التنافسي (AI Insights)</span>
            </h3>

            <div id="ai-trigger-zone" style="text-align: center; padding: 20px 0;">
                <p style="color: #555; margin-bottom: 15px;">قم بتوليد تقرير شراء تفصيلي فوري يجمع المواصفات وآراء المستخدمين معاً بواسطة AI.</p>
                <button id="btn-generate-ai" class="btn btn--primary" style="padding: 12px 35px; font-size: 1.1rem; font-weight: bold; background: #3498db;">
                    ✨ استشير الذكاء الاصطناعي الآن
                </button>
            </div>

            <div id="ai-loader" style="display: none; text-align: center; padding: 40px 0;">
                <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 15px;"></div>
                <p style="color: #3498db; font-weight: bold;">برجاء الانتظار.. يقوم الـ AI حالياً بقراءة المواصفات وتحليل مشاعر المراجعات وصياغة التقرير الكلي...</p>
            </div>

            <div id="ai-result" style="display: none; line-height: 1.8; color: #2c3e50; font-size: 1.1rem; background: #fafafa; padding: 20px; border-radius: 6px; border-right: 4px solid #3498db;">
                </div>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 60px 0; color: #95a5a6; background: #fafafa; border-radius: 8px; border: 2px dashed #e2e8f0;">
            <p style="font-size: 1.2rem; margin: 0;">يرجى اختيار منتجين من القوائم بالأعلى للبدء في المقارنة الفنية واستخراج تقارير الشراء الذكية.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGenerate = document.getElementById('btn-generate-ai');
    if (!btnGenerate) return;

    btnGenerate.addEventListener('click', function() {
        const triggerZone = document.getElementById('ai-trigger-zone');
        const loader = document.getElementById('ai-loader');
        const resultDiv = document.getElementById('ai-result');

        // تبديل واجهات العرض لبدء التحميل
        triggerZone.style.display = 'none';
        loader.style.display = 'block';

        // تجهيز بيانات الطلب (معرفات المنتجين الحاليين)
        const formData = new FormData();
        formData.append('p1', '<?= $p1_id ?>');
        formData.append('p2', '<?= $p2_id ?>');

        // إرسال الطلب إلى دالة analyze() بالـ CompareController
        fetch('<?= url("compare/analyze") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loader.style.display = 'none';
            resultDiv.style.display = 'block';

            if (data.error) {
                resultDiv.innerHTML = `<p style="color: #e74c3c; font-weight: bold;">❌ خطأ: ${data.error}</p>`;
            } else {
                // استخدام دالة بسيطة لتحويل الـ Markdown الأساسي القادم من جيميناي إلى HTML مريح للعين
                resultDiv.innerHTML = formatMarkdown(data.analysis);
            }
        })
        .catch(err => {
            loader.style.display = 'none';
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `<p style="color: #e74c3c; font-weight: bold;">❌ عذراً، فشل الاتصال بالخادم. يرجى المحاولة مرة أخرى لاحقاً.</p>`;
        });
    });

    // دالة مساعدة لتحويل الـ Markdown الخفيف إلى تنسيق فقرات وقوائم HTML
    function formatMarkdown(text) {
        if (!text) return '';
        let html = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // تحويل النص العريض
            .replace(/\*(.*?)\*/g, '<em>$1</em>')             // تحويل النص المائل
            .replace(/^\s*[\-\*]\s+(.*)$/gm, '<li style="margin-right: 20px; list-style-type: square;">$1</li>') // القوائم النقطية
            .replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>'); // الحفاظ على السطور الجديدة
        
        // لف عناصر القوائم بداخل علامة <ul>
        html = html.replace(/(<li.*?>.*?<\/li>)/gs, '<ul style="margin: 10px 0; padding-right: 20px;">$1</ul>');
        return html;
    }
});
</script>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.form-control:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52,152,219,0.2);
}
</style>