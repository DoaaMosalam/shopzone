<?php

/**
 * Database Seeder - ShopZone
 * Inserts rich seed data across all product categories with real specs.
 * Run via CLI: php db/seed.php
 */

define('BASE_PATH', dirname(__DIR__));

$cfg = require BASE_PATH . '/config/database.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['dbname'], $cfg['charset']);

try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$now = date('Y-m-d H:i:s');

echo "Seeding ShopZone database…\n\n";
 
// ── 1. Clear all tables ───────────────────────────────────────────────────────
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
foreach ([
    'User_Phones','Admin','Customer','User',
    'Review','Specification','Manages','Product','Category',
] as $tbl) {
    $pdo->exec("TRUNCATE TABLE `{$tbl}`;");
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "Old data cleared.\n\n";

// ── 2. Users (1 Admin + 4 Customers) ─────────────────────────────────────────
$users = [
    ['Amr',     null,  'Hassan',   'Male',   'admin@shopzone.com',    'admin1234'],
    ['Sara',    'M.',  'Khalil',   'Female', 'sara@example.com',      'password1'],
    ['Mohamed', null,  'Ali',      'Male',   'mohamed@example.com',   'password2'],
    ['Layla',   null,  'Ibrahim',  'Female', 'layla@example.com',     'password3'],
    ['Omar',    null,  'Mostafa',  'Male',   'omar@example.com',      'password4'],
];

$userIds = [];
foreach ($users as $u) {
    $hash = password_hash($u[5], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        "INSERT INTO User (Fname, Mname, Lname, Gender, Email, Password, Created_At)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$u[0], $u[1], $u[2], $u[3], $u[4], $hash, $now]);
    $userIds[] = (int) $pdo->lastInsertId();
}

// Admin
$pdo->prepare("INSERT INTO Admin (Admin_ID, SSN, Last_Login) VALUES (?, ?, ?)")
    ->execute([$userIds[0], '123-45-6789', $now]);

// Customers
foreach ([1, 2, 3, 4] as $i) {
    $pdo->prepare(
        "INSERT INTO Customer (Customer_ID, Account_Status, Ban_Until, City, State)
         VALUES (?, 'Active', NULL, ?, ?)"
    )->execute([$userIds[$i], 'Cairo', 'Cairo Governorate']);
}

echo "Users seeded: 1 Admin + 4 Customers.\n\n";

// ── 3. Categories ─────────────────────────────────────────────────────────────
$categoryNames = [
    'Smartphones',
    'Laptops',
    'Tablets',
    'Smart Watches',
    'Monitors & TVs',
    'Cameras',
    'Gaming',
    'PC Cases',
    'Headphones',
    'Keyboards'
];

$catIds = [];
foreach ($categoryNames as $name) {
    $pdo->prepare("INSERT INTO Category (Name, Image) VALUES (?, NULL)")->execute([$name]);
    $catIds[$name] = (int) $pdo->lastInsertId();
    echo "Category: {$name}\n";
}
echo "\n";

// ── 4. Products per category ──────────────────────────────────────────────────
$productsData = [

    // ─────────────────────────────────────────────────────────────────────────
    // Smartphones
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['iPhone 15 Pro Max', 62000.00, 'Apple',
            'أحدث هاتف رائد من آبل بهيكل تيتانيوم قوي وكاميرا بيريسكوب متطورة.',
            15, $catIds['Smartphones'],
            'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=500&q=80'],
        'specs' => [['OS','iOS 17'],['Processor','Apple A17 Pro'],['RAM','8 GB'],
                    ['Storage','256 GB'],['Screen','6.7" OLED'],['Battery','4441 mAh']],
    ],
    [
        'info'  => ['Samsung Galaxy S24 Ultra', 58000.00, 'Samsung',
            'عملاق أندرويد المدعوم بميزات الذكاء الاصطناعي وقلم S-Pen مدمج.',
            20, $catIds['Smartphones'],
            'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=500&q=80'],
        'specs' => [['OS','Android 14 (One UI 6.1)'],['Processor','Snapdragon 8 Gen 3'],
                    ['RAM','12 GB'],['Storage','512 GB'],['Screen','6.8" Dynamic AMOLED 2X'],['Battery','5000 mAh']],
    ],
    [
        'info'  => ['Google Pixel 8 Pro', 42000.00, 'Google',
            'تجربة أندرويد الخام وأفضل معالجة صور بالذكاء الاصطناعي.',
            12, $catIds['Smartphones'],
            'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=500&q=80'],
        'specs' => [['OS','Android 14'],['Processor','Google Tensor G3'],['RAM','12 GB'],
                    ['Storage','128 GB'],['Screen','6.7" LTPO OLED'],['Battery','5050 mAh']],
    ],
    [
        'info'  => ['Xiaomi 14 Ultra', 48000.00, 'Xiaomi',
            'وحش التصوير المطور بالتعاون مع شركة Leica العالمية.',
            8, $catIds['Smartphones'],
            'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&q=80'],
        'specs' => [['OS','Android 14 (HyperOS)'],['Processor','Snapdragon 8 Gen 3'],
                    ['RAM','16 GB'],['Storage','512 GB'],['Screen','6.73" AMOLED 120Hz'],['Battery','5000 mAh']],
    ],
    [
        'info'  => ['OnePlus 12', 36000.00, 'OnePlus',
            'أداء فائق بسعر منافس مع نظام شحن سريع SUPERVOOC بقوة 100 واط.',
            18, $catIds['Smartphones'],
            'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&q=80'],
        'specs' => [['OS','Android 14 (OxygenOS 14)'],['Processor','Snapdragon 8 Gen 3'],
                    ['RAM','12 GB'],['Storage','256 GB'],['Screen','6.82" AMOLED 120Hz'],['Battery','5400 mAh']],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Laptops
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['MacBook Air M3', 68000.00, 'Apple',
            'لاب توب فائق النحافة مع معالج M3 وبطارية تدوم طوال اليوم.',
            10, $catIds['Laptops'],
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=500&q=80'],
        'specs' => [['OS','macOS Sonoma'],['Processor','Apple M3 (8-Core)'],['RAM','16 GB'],
                    ['Storage','512 GB SSD'],['Screen','13.6" Liquid Retina'],['Battery','Up to 18 hours']],
    ],
    [
        'info'  => ['ASUS ROG Zephyrus G14', 75000.00, 'ASUS',
            'لاب توب جيمنج احترافي يجمع بين قوة الأداء وخفة الوزن.',
            5, $catIds['Laptops'],
            'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&q=80'],
        'specs' => [['OS','Windows 11 Home'],['Processor','AMD Ryzen 9 8945HS'],['RAM','32 GB DDR5'],
                    ['Storage','1 TB NVMe SSD'],['Screen','14" OLED 120Hz'],['Graphics','NVIDIA RTX 4070']],
    ],
    [
        'info'  => ['Dell XPS 13 9340', 64000.00, 'Dell',
            'اللاب توب المفضل لرجال الأعمال والمطورين بتصميم عصري.',
            7, $catIds['Laptops'],
            'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=500&q=80'],
        'specs' => [['OS','Windows 11 Pro'],['Processor','Intel Core Ultra 7 155H'],['RAM','16 GB LPDDR5x'],
                    ['Storage','512 GB SSD'],['Screen','13.4" FHD+ InfinityEdge'],['Graphics','Intel Arc']],
    ],
    [
        'info'  => ['Lenovo Legion Pro 5', 71000.00, 'Lenovo',
            'قوة برمجية ورسومية هائلة مع نظام تبريد متطور.',
            6, $catIds['Laptops'],
            'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&q=80'],
        'specs' => [['OS','Windows 11'],['Processor','Intel Core i9-14900HX'],['RAM','32 GB'],
                    ['Storage','1 TB SSD'],['Screen','16" WQXGA 240Hz'],['Graphics','NVIDIA RTX 4060']],
    ],
    [
        'info'  => ['HP Spectre x360 14', 67000.00, 'HP',
            'لاب توب 2-in-1 فاخر بشاشة OLED قابلة للطي مع قلم HP رقمي.',
            9, $catIds['Laptops'],
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80'],
        'specs' => [['OS','Windows 11 Home'],['Processor','Intel Core Ultra 5 125H'],['RAM','16 GB'],
                    ['Storage','1 TB SSD'],['Screen','14" 2.8K OLED Touch'],['Battery','Up to 17 hours']],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Tablets
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['iPad Pro 13-inch M4', 59000.00, 'Apple',
            'الجهاز اللوحي الأقوى على الإطلاق مع شاشة Ultra Retina XDR.',
            11, $catIds['Tablets'],
            'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&q=80'],
        'specs' => [['Chip','Apple M4'],['RAM','8 GB'],['Storage','256 GB'],
                    ['Screen','13" Ultra Retina XDR OLED'],['Battery','10 hours'],['Connectivity','Wi-Fi 6E + 5G']],
    ],
    [
        'info'  => ['Samsung Galaxy Tab S9 Ultra', 52000.00, 'Samsung',
            'تابلت بشاشة AMOLED عملاقة 14.6 بوصة مع S-Pen مرفق.',
            8, $catIds['Tablets'],
            'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=500&q=80'],
        'specs' => [['Processor','Snapdragon 8 Gen 2'],['RAM','12 GB'],['Storage','256 GB'],
                    ['Screen','14.6" Dynamic AMOLED 2X 120Hz'],['Battery','11200 mAh'],['S-Pen','Included']],
    ],
    [
        'info'  => ['Microsoft Surface Pro 9', 48000.00, 'Microsoft',
            'جهاز لوحي بقدرة الحاسوب الكامل يعمل بـ Windows 11.',
            6, $catIds['Tablets'],
            'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=500&q=80'],
        'specs' => [['Processor','Intel Core i5-1235U'],['RAM','8 GB'],['Storage','256 GB SSD'],
                    ['Screen','13" PixelSense 120Hz'],['OS','Windows 11 Home'],['Battery','Up to 15.5 hours']],
    ],
    [
        'info'  => ['Xiaomi Pad 6 Pro', 18000.00, 'Xiaomi',
            'أداء ممتاز بسعر مناسب مع شاشة 144 هرتز ومعالج Snapdragon.',
            14, $catIds['Tablets'],
            'https://images.unsplash.com/photo-1585790050230-5dd28404ccb9?w=500&q=80'],
        'specs' => [['Processor','Snapdragon 8+ Gen 1'],['RAM','8 GB'],['Storage','128 GB'],
                    ['Screen','11" IPS LCD 144Hz'],['Battery','8600 mAh'],['Charging','67W Turbo']],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Smart Watches
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['Apple Watch Series 9', 18000.00, 'Apple',
            'الساعة الذكية الأكثر تقدماً مع شاشة Always-On ورقاقة S9.',
            20, $catIds['Smart Watches'],
            'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=500&q=80'],
        'specs' => [['Chip','Apple S9 SiP'],['Screen','Always-On LTPO OLED'],['Health','Blood Oxygen + ECG'],
                    ['GPS','Built-in'],['Battery','18 hours'],['Water Resistance','50m']],
    ],
    [
        'info'  => ['Samsung Galaxy Watch 6 Classic', 12000.00, 'Samsung',
            'ساعة فاخرة بإطار دوار كلاسيكي ومتابعة صحية متقدمة.',
            16, $catIds['Smart Watches'],
            'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=500&q=80'],
        'specs' => [['Processor','Exynos W930'],['Screen','Super AMOLED 1.5"'],['Health','BIA Body Composition'],
                    ['OS','Wear OS + One UI Watch 5'],['Battery','40 hours'],['Water Resistance','5ATM']],
    ],
    [
        'info'  => ['Garmin Fenix 7 Pro', 22000.00, 'Garmin',
            'ساعة رياضية احترافية مع GPS دقيق وعمر بطارية أسطوري.',
            9, $catIds['Smart Watches'],
            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80'],
        'specs' => [['Screen','1.3" MIP Display'],['GPS','Multi-band GPS + GLONASS'],['Battery','Up to 22 days'],
                    ['Water Resistance','100m'],['Sensors','Pulse Ox + Heart Rate'],['Material','Titanium']],
    ],
    [
        'info'  => ['Huawei Watch GT 4', 8500.00, 'Huawei',
            'تصميم أنيق وعمر بطارية يصل إلى 14 يوماً.',
            18, $catIds['Smart Watches'],
            'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=500&q=80'],
        'specs' => [['Screen','1.43" AMOLED'],['Battery','14 days'],['Health','Heart Rate + SpO2 + Stress'],
                    ['GPS','Built-in'],['Water Resistance','5ATM'],['Connectivity','Bluetooth 5.2']],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Cameras
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['Sony Alpha A7 IV', 95000.00, 'Sony',
            'كاميرا فل فريم كاملة بـ 33 ميجابكسل وتصوير 4K بدقة استثنائية.',
            4, $catIds['Cameras'],
            'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=500&q=80'],
        'specs' => [['Sensor','33MP Full-Frame BSI CMOS'],['Video','4K 60fps'],['ISO','102400'],
                    ['AF Points','759 Phase-Detect'],['Stabilization','5-Axis IBIS'],['Mount','Sony E-Mount']],
    ],
    [
        'info'  => ['Canon EOS R6 Mark II', 85000.00, 'Canon',
            'كاميرا مرايا بدون مرآة للمحترفين بمعدل تصوير 40 إطار في الثانية.',
            5, $catIds['Cameras'],
            'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=500&q=80'],
        'specs' => [['Sensor','24.2MP Full-Frame CMOS'],['Video','4K 60fps Uncropped'],['FPS','40 fps RAW'],
                    ['AF','Dual Pixel CMOS AF II'],['Stabilization','8-stop IBIS'],['Mount','Canon RF']],
    ],
    [
        'info'  => ['GoPro HERO 12 Black', 16000.00, 'GoPro',
            'كاميرا مغامرات لا تقهر بتصوير 5.3K وتثبيت HyperSmooth 6.0.',
            15, $catIds['Cameras'],
            'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=500&q=80'],
        'specs' => [['Video','5.3K 60fps / 4K 120fps'],['Stabilization','HyperSmooth 6.0'],
                    ['Water','10m Waterproof'],['Battery','2.5 hours'],['Weight','154 g'],['Lens','Wide + Linear']],
    ],
    [
        'info'  => ['Nikon Z50 II', 42000.00, 'Nikon',
            'كاميرا APS-C مدمجة مثالية للمبتدئين والمحترفين بمواصفات عالية.',
            7, $catIds['Cameras'],
            'https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?w=500&q=80'],
        'specs' => [['Sensor','20.9MP APS-C BSI CMOS'],['Video','4K 30fps'],['Viewfinder','2.36M-dot EVF'],
                    ['AF','Phase Detect + Subject Recognition'],['Stabilization','Electronic VR'],['Mount','Nikon Z']],
    ],

    // ─────────────────────────────────────────────────────────────────────────
    // Gaming
    // ─────────────────────────────────────────────────────────────────────────
    [
        'info'  => ['PlayStation 5 Digital Edition', 25000.00, 'Sony',
            'منصة الجيل الجديد بمعالج SSD فائق السرعة وصوت Tempest 3D.',
            8, $catIds['Gaming'],
            'https://images.unsplash.com/photo-1607853202273-797f1c22a38e?w=500&q=80'],
        'specs' => [['CPU','AMD Zen 2 / 8 Cores 3.5GHz'],['GPU','AMD RDNA 2 / 10.28 TFLOPs'],
                    ['RAM','16 GB GDDR6'],['Storage','825 GB NVMe SSD'],['Resolution','Up to 8K'],['FPS','Up to 120']],
    ],
    [
        'info'  => ['Xbox Series X', 22000.00, 'Microsoft',
            'أقوى كونسول من مايكروسوفت بـ 12 تيرافلوبس وتوافق رجعي كامل.',
            10, $catIds['Gaming'],
            'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?w=500&q=80'],
        'specs' => [['CPU','AMD Zen 2 / 8 Cores 3.8GHz'],['GPU','12 TFLOPs Custom RDNA 2'],
                    ['RAM','16 GB GDDR6'],['Storage','1 TB NVMe SSD'],['Resolution','Up to 8K'],['FPS','Up to 120']],
    ],
    [
        'info'  => ['Nintendo Switch OLED', 12000.00, 'Nintendo',
            'جهاز الألعاب المحمول والمنزلي الشهير بشاشة OLED نابضة بالألوان.',
            18, $catIds['Gaming'],
            'https://images.unsplash.com/photo-1617096200347-cb04ae810b1d?w=500&q=80'],
        'specs' => [['Screen','7" OLED Multi-Touch'],['Battery','4.5 – 9 hours'],['Storage','64 GB'],
                    ['Resolution','1080p (TV) / 720p (Handheld)'],['Modes','TV, Tabletop, Handheld'],['Weight','320 g']],
    ],
    [
        'info'  => ['Razer DeathAdder V3 Pro', 5800.00, 'Razer',
            'ماوس جيمنج لاسلكي خفيف الوزن بمستشعر Focus Pro 30K.',
            22, $catIds['Gaming'],
            'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500&q=80'],
        'specs' => [['Sensor','Focus Pro 30K Optical'],['DPI','100 – 30,000'],['Buttons','6 Programmable'],
                    ['Battery','90 hours'],['Weight','64 g'],['Connectivity','2.4GHz Wireless']],
    ],
    [
        'info'  => ['SteelSeries Apex Pro TKL', 7200.00, 'SteelSeries',
            'كيبورد جيمنج احترافي بمفاتيح مغناطيسية قابلة للضبط.',
            12, $catIds['Gaming'],
            'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=500&q=80'],
        'specs' => [['Switches','OmniPoint 2.0 Magnetic'],['Actuation','0.1 – 4.0mm Adjustable'],
                    ['Layout','TKL (Tenkeyless)'],['RGB','Per-Key RGB'],['Interface','USB-C'],['Polling Rate','8000 Hz']],
    ],
// ─────────────────────────────────────────────────────────────────────────
// Keyboards
// ─────────────────────────────────────────────────────────────────────────

    [
        'info' => [
            'SteelSeries Apex Pro TKL',
            7200.00,
            'SteelSeries',
            'كيبورد ميكانيكي احترافي بمفاتيح OmniPoint 2.0 القابلة للتعديل.',
            12,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'OmniPoint 2.0'],
            ['Layout', 'TKL'],
            ['RGB', 'Per-Key RGB'],
            ['Connection', 'USB-C'],
            ['Polling Rate', '8000 Hz'],
            ['Weight', '960 g'],
        ],
    ],

    [
        'info' => [
            'Logitech G Pro X',
            5200.00,
            'Logitech',
            'كيبورد ألعاب احترافي قابل لتغيير السويتشات.',
            18,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'GX Blue Clicky'],
            ['Layout', 'TKL'],
            ['RGB', 'LIGHTSYNC RGB'],
            ['Connection', 'USB'],
            ['Cable', 'Detachable'],
            ['Weight', '980 g'],
        ],
    ],

    [
        'info' => [
            'Corsair K70 RGB Pro',
            6900.00,
            'Corsair',
            'كيبورد ميكانيكي للألعاب مزود بمفاتيح Cherry MX.',
            15,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Cherry MX Red'],
            ['Layout', 'Full Size'],
            ['RGB', 'Dynamic RGB'],
            ['Connection', 'USB'],
            ['Polling Rate', '8000 Hz'],
            ['Palm Rest', 'Included'],
        ],
    ],

    [
        'info' => [
            'Razer BlackWidow V4 Pro',
            8500.00,
            'Razer',
            'كيبورد ألعاب احترافي بإضاءة Chroma RGB ومفاتيح ميكانيكية.',
            10,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Razer Green'],
            ['Layout', 'Full Size'],
            ['RGB', 'Razer Chroma RGB'],
            ['Connection', 'USB'],
            ['Media Controls', 'Yes'],
            ['Polling Rate', '1000 Hz'],
        ],
    ],

    [
        'info' => [
            'Keychron K8 Pro',
            4800.00,
            'Keychron',
            'كيبورد ميكانيكي لاسلكي مناسب للبرمجة والعمل والألعاب.',
            20,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Gateron G Pro Brown'],
            ['Layout', 'TKL'],
            ['Connection', 'Bluetooth / USB-C'],
            ['Battery', '4000 mAh'],
            ['Compatible', 'Windows / macOS'],
            ['RGB', 'South-facing RGB'],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
// Keyboards
// ─────────────────────────────────────────────────────────────────────────

    [
        'info' => [
            'SteelSeries Apex Pro TKL',
            7200.00,
            'SteelSeries',
            'كيبورد ميكانيكي احترافي بمفاتيح OmniPoint 2.0 القابلة للتعديل.',
            12,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'OmniPoint 2.0'],
            ['Layout', 'TKL'],
            ['RGB', 'Per-Key RGB'],
            ['Connection', 'USB-C'],
            ['Polling Rate', '8000 Hz'],
            ['Weight', '960 g'],
        ],
    ],

    [
        'info' => [
            'Logitech G Pro X',
            5200.00,
            'Logitech',
            'كيبورد ألعاب احترافي قابل لتغيير السويتشات.',
            18,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'GX Blue Clicky'],
            ['Layout', 'TKL'],
            ['RGB', 'LIGHTSYNC RGB'],
            ['Connection', 'USB'],
            ['Cable', 'Detachable'],
            ['Weight', '980 g'],
        ],
    ],

    [
        'info' => [
            'Corsair K70 RGB Pro',
            6900.00,
            'Corsair',
            'كيبورد ميكانيكي للألعاب مزود بمفاتيح Cherry MX.',
            15,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Cherry MX Red'],
            ['Layout', 'Full Size'],
            ['RGB', 'Dynamic RGB'],
            ['Connection', 'USB'],
            ['Polling Rate', '8000 Hz'],
            ['Palm Rest', 'Included'],
        ],
    ],

    [
        'info' => [
            'Razer BlackWidow V4 Pro',
            8500.00,
            'Razer',
            'كيبورد ألعاب احترافي بإضاءة Chroma RGB ومفاتيح ميكانيكية.',
            10,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Razer Green'],
            ['Layout', 'Full Size'],
            ['RGB', 'Razer Chroma RGB'],
            ['Connection', 'USB'],
            ['Media Controls', 'Yes'],
            ['Polling Rate', '1000 Hz'],
        ],
    ],

    [
        'info' => [
            'Keychron K8 Pro',
            4800.00,
            'Keychron',
            'كيبورد ميكانيكي لاسلكي مناسب للبرمجة والعمل والألعاب.',
            20,
            $catIds['Keyboards'],
            'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&q=80'
        ],

        'specs' => [
            ['Switches', 'Gateron G Pro Brown'],
            ['Layout', 'TKL'],
            ['Connection', 'Bluetooth / USB-C'],
            ['Battery', '4000 mAh'],
            ['Compatible', 'Windows / macOS'],
            ['RGB', 'South-facing RGB'],
        ],
    ],

    // ─────────────────────────────────────────────────────────────────────────
// Monitors & TVs
// ─────────────────────────────────────────────────────────────────────────

    [
        'info' => [
            'Samsung Odyssey G7',
            18000.00,
            'Samsung',
            'شاشة ألعاب 27 بوصة بدقة QHD ومعدل تحديث 240Hz.',
            10,
            $catIds['Monitors & TVs'],
            'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&q=80'
        ],

        'specs' => [
            ['Size', '27 Inch'],
            ['Resolution', '2560×1440 QHD'],
            ['Refresh Rate', '240Hz'],
            ['Panel', 'VA'],
            ['Response Time', '1ms'],
            ['Ports', 'HDMI, DisplayPort'],
        ],
    ],

    [
        'info' => [
            'LG UltraGear 27GP850',
            16500.00,
            'LG',
            'شاشة جيمنج IPS بدقة QHD ومعدل تحديث 180Hz.',
            8,
            $catIds['Monitors & TVs'],
            'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500&q=80'
        ],

        'specs' => [
            ['Size', '27 Inch'],
            ['Resolution', 'QHD'],
            ['Refresh Rate', '180Hz'],
            ['Panel', 'Nano IPS'],
            ['HDR', 'HDR400'],
            ['Ports', 'HDMI, DisplayPort'],
        ],
    ],

    [
        'info' => [
            'Sony Bravia XR 55"',
            42000.00,
            'Sony',
            'تلفزيون 4K OLED ذكي مزود بمعالج Cognitive XR.',
            6,
            $catIds['Monitors & TVs'],
            'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=500&q=80'
        ],

        'specs' => [
            ['Size', '55 Inch'],
            ['Resolution', '4K UHD'],
            ['Panel', 'OLED'],
            ['Smart TV', 'Google TV'],
            ['HDR', 'Dolby Vision'],
            ['HDMI', '4 Ports'],
        ],
    ],

    [
        'info' => [
            'Samsung Crystal UHD CU8000',
            26000.00,
            'Samsung',
            'تلفزيون ذكي بدقة 4K مع نظام Tizen.',
            12,
            $catIds['Monitors & TVs'],
            'https://images.unsplash.com/photo-1461151304267-38535e780c79?w=500&q=80'
        ],

        'specs' => [
            ['Size', '50 Inch'],
            ['Resolution', '4K UHD'],
            ['Smart TV', 'Tizen OS'],
            ['HDR', 'HDR10+'],
            ['HDMI', '3 Ports'],
            ['Wi-Fi', 'Yes'],
        ],
    ],
    // ─────────────────────────────────────────────────────────────────────────
// Headphones
// ─────────────────────────────────────────────────────────────────────────

    [
        'info' => [
            'Sony WH-1000XM5',
            16500.00,
            'Sony',
            'سماعة لاسلكية رائدة بعزل ضوضاء احترافي وجودة صوت ممتازة.',
            14,
            $catIds['Headphones'],
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Over-Ear'],
            ['Connectivity', 'Bluetooth 5.2'],
            ['Noise Cancellation', 'Active Noise Cancelling'],
            ['Battery', '30 Hours'],
            ['Charging', 'USB-C Fast Charging'],
            ['Weight', '250 g'],
        ],
    ],

    [
        'info' => [
            'Apple AirPods Pro (2nd Gen)',
            11800.00,
            'Apple',
            'سماعات أذن لاسلكية مع خاصية العزل النشط للضوضاء والصوت المكاني.',
            20,
            $catIds['Headphones'],
            'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'True Wireless'],
            ['Connectivity', 'Bluetooth 5.3'],
            ['Noise Cancellation', 'Active Noise Cancelling'],
            ['Battery', '30 Hours with Case'],
            ['Water Resistance', 'IP54'],
            ['Charging', 'MagSafe / USB-C'],
        ],
    ],

    [
        'info' => [
            'JBL Tune 770NC',
            6200.00,
            'JBL',
            'سماعة لاسلكية بعزل ضوضاء وصوت JBL Pure Bass.',
            18,
            $catIds['Headphones'],
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Over-Ear'],
            ['Connectivity', 'Bluetooth 5.3'],
            ['Battery', '70 Hours'],
            ['Noise Cancellation', 'Adaptive ANC'],
            ['Charging', 'USB-C'],
            ['Weight', '232 g'],
        ],
    ],

    [
        'info' => [
            'HyperX Cloud III',
            4800.00,
            'HyperX',
            'سماعة ألعاب احترافية بميكروفون قابل للفصل وصوت DTS.',
            15,
            $catIds['Headphones'],
            'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Gaming Headset'],
            ['Connection', 'USB-C / 3.5 mm'],
            ['Microphone', 'Detachable'],
            ['Audio', 'DTS Headphone:X'],
            ['Drivers', '53 mm'],
            ['Compatibility', 'PC, PS5, Xbox'],
        ],
    ],

    [
        'info' => [
            'SteelSeries Arctis Nova 7',
            9800.00,
            'SteelSeries',
            'سماعة ألعاب لاسلكية تدعم Bluetooth و2.4GHz في نفس الوقت.',
            12,
            $catIds['Headphones'],
            'https://images.unsplash.com/photo-1612444530582-fc66183b16f0?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Wireless Gaming Headset'],
            ['Connectivity', '2.4GHz + Bluetooth'],
            ['Battery', '38 Hours'],
            ['Microphone', 'ClearCast Gen2'],
            ['Drivers', '40 mm'],
            ['Compatibility', 'PC, PlayStation, Nintendo Switch'],
        ],
    ],
    // ─────────────────────────────────────────────────────────────────────────
// PC Cases
// ─────────────────────────────────────────────────────────────────────────

    [
        'info' => [
            'NZXT H9 Flow',
            7800.00,
            'NZXT',
            'كيس احترافي بزجاج مقوى وتدفق هواء ممتاز.',
            12,
            $catIds['PC Cases'],
            'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Mid Tower'],
            ['Motherboard', 'ATX / Micro-ATX / Mini-ITX'],
            ['Fans Included', '4'],
            ['Side Panel', 'Tempered Glass'],
            ['GPU Clearance', '435 mm'],
            ['Color', 'Black'],
        ],
    ],

    [
        'info' => [
            'Corsair 4000D Airflow',
            5200.00,
            'Corsair',
            'كيس بتدفق هواء ممتاز مناسب للألعاب.',
            18,
            $catIds['PC Cases'],
            'https://images.unsplash.com/photo-1587202372616-b43abea06c2a?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Mid Tower'],
            ['Motherboard', 'ATX'],
            ['Fans Included', '2'],
            ['Side Panel', 'Tempered Glass'],
            ['Front Panel', 'High Airflow'],
            ['Color', 'White'],
        ],
    ],

    [
        'info' => [
            'Lian Li O11 Dynamic EVO',
            8900.00,
            'Lian Li',
            'كيس فاخر مناسب لتجميعات الألعاب والـ Water Cooling.',
            10,
            $catIds['PC Cases'],
            'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Mid Tower'],
            ['Motherboard', 'E-ATX / ATX'],
            ['Side Panel', 'Tempered Glass'],
            ['Radiator Support', '360 mm'],
            ['GPU Clearance', '426 mm'],
            ['Color', 'Black'],
        ],
    ],

    [
        'info' => [
            'Cooler Master MasterBox TD500 Mesh',
            4700.00,
            'Cooler Master',
            'كيس ألعاب مزود بثلاث مراوح RGB.',
            15,
            $catIds['PC Cases'],
            'https://images.unsplash.com/photo-1591799265444-d66432b91588?w=500&q=80'
        ],

        'specs' => [
            ['Type', 'Mid Tower'],
            ['Fans Included', '3 ARGB'],
            ['Motherboard', 'ATX'],
            ['Side Panel', 'Tempered Glass'],
            ['Cooling', 'High Airflow'],
            ['Color', 'Black'],
        ],
    ],
];

// ── 5. Insert products + specs + manages ─────────────────────────────────────
$stmtProduct = $pdo->prepare(
    "INSERT INTO Product
        (Name, Price, Brand, Description, Product_Quantity, Category_ID, Image_URL, Rating_No, Release_Date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmtSpec    = $pdo->prepare(
    "INSERT INTO Specification (Product_ID, Spec_Key, Spec_Value) VALUES (?, ?, ?)"
);
$stmtManages = $pdo->prepare(
    "INSERT IGNORE INTO Manages (Admin_ID, Product_ID) VALUES (?, ?)"
);

$adminId = $userIds[0];

foreach ($productsData as $data) {
    $p             = $data['info'];
    $randomRating  = round(4.0 + mt_rand(0, 9) / 10, 1);
    $randomDate    = date('Y-m-d', strtotime('-' . mt_rand(10, 365) . ' days'));

    $stmtProduct->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $randomRating, $randomDate]);
    $productId = (int) $pdo->lastInsertId();

    foreach ($data['specs'] as $spec) {
        $stmtSpec->execute([$productId, $spec[0], $spec[1]]);
    }

    $stmtManages->execute([$adminId, $productId]);
    echo "Product: {$p[0]} (ID: {$productId})\n";
}

// ── 6. Sample reviews ─────────────────────────────────────────────────────────
echo "\nAdding sample reviews…\n";

$allPIds = $pdo->query("SELECT Product_ID FROM Product LIMIT 4")->fetchAll(PDO::FETCH_COLUMN);

if (count($allPIds) >= 2) {
    $reviews = [
        [$allPIds[0], $userIds[1], 'منتج ممتاز جداً، مواصفات رائعة وجودة عالية. أنصح به بشدة.'],
        [$allPIds[0], $userIds[2], 'جيد لكن السعر مرتفع قليلاً مقارنة بالمنافسين.'],
        [$allPIds[1], $userIds[1], 'شاشة رائعة وأداء لا يُصدق. أفضل شراء قمت به.'],
        [$allPIds[1], $userIds[3], 'مميزات الذكاء الاصطناعي مذهلة حقاً!'],
    ];
    if (count($allPIds) >= 4) {
        $reviews[] = [$allPIds[2], $userIds[2], 'تجربة استخدام سلسة جداً. البطارية تدوم طويلاً.'];
        $reviews[] = [$allPIds[3], $userIds[4], 'أفضل من توقعاتي، سعيد بالشراء.'];
    }

    $stmtRev = $pdo->prepare(
        "INSERT IGNORE INTO Review (Product_ID, Customer_ID, Comment, Created_At) VALUES (?, ?, ?, ?)"
    );
    foreach ($reviews as $rev) {
        $stmtRev->execute([$rev[0], $rev[1], $rev[2], $now]);
    }
    echo "Sample reviews added.\n";
}

echo "\n✔ Database seeding completed successfully!\n";
echo "Categories: " . count($categoryNames) . "\n";
echo "Products:   " . count($productsData) . "\n";
echo "Users:      " . count($users) . " (1 Admin + " . (count($users) - 1) . " Customers)\n";
echo "\nAdmin Login:    admin@shopzone.com / admin1234\n";
echo "Customer Login: sara@example.com  / password1\n";
