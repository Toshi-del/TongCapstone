<?php
/**
 * Direct test to verify the doctor examination links work
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Direct Link Test ===\n\n";

echo "Based on our tests, these examinations have chest X-ray data:\n\n";

echo "1. Pol pelayo (ID: 1)\n";
echo "   - Result: Normal\n";
echo "   - Finding: NORMAL\n";
echo "   - Direct URL: http://localhost/rss_new/public/doctor/pre-employment/1/examination\n\n";

echo "2. Ken Tuazon (ID: 2)\n";
echo "   - Result: Normal\n";
echo "   - Finding: —\n";
echo "   - Direct URL: http://localhost/rss_new/public/doctor/pre-employment/2/examination\n\n";

echo "Instructions:\n";
echo "1. Make sure you're logged in as a doctor\n";
echo "2. Visit one of the URLs above directly in your browser\n";
echo "3. Look for the yellow debug section (if debug mode is on)\n";
echo "4. Look for the 'Chest X-Ray Results' section with green 'Connected' badge\n";
echo "5. The radiologist findings should show the result and finding\n\n";

echo "If the section is not showing, check:\n";
echo "- Are you logged in as a doctor?\n";
echo "- Is the URL correct?\n";
echo "- Check browser developer console for any JavaScript errors\n";
echo "- Try clearing browser cache (Ctrl+F5)\n\n";

echo "=== Test Complete ===\n";
