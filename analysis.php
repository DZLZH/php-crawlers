<?PHP
/**
 *  +--------------------------------------------------------------
 *  | Copyright (c) 2016 DZLZH All rights reserved.
 *  +--------------------------------------------------------------
 *  | Author: DZLZH <dzlzh@null.net>
 *  +--------------------------------------------------------------
 *  | Filename: analysis.php
 *  +--------------------------------------------------------------
 *  | Last modified: 2016-06-20 16:23
 *  +--------------------------------------------------------------
 *  | Description: analysis lagou php-beijing
 *  +--------------------------------------------------------------
 */


require_once 'config.php';

$pdo = new PDOMysql();
$pdo->connect($dbconfig);
/*
$totalSql = 'SELECT count(uuid) AS number FROM lagou WHERE city="北京"';
$total = $pdo->findOne($totalSql)['number'];
echo 'Total:', $total, "\n";
echo "-------------------------\n";

$sql = array(
    'district'     => 'SELECT district, COUNT(district) AS number FROM lagou WHERE city="北京" GROUP BY district ORDER BY number DESC',
    'financeStage' => 'SELECT financeStage, COUNT(financeStage) AS number FROM lagou WHERE city="北京" GROUP BY financeStage ORDER BY number DESC',
    'workYear'     => 'SELECT workYear, COUNT(workYear) AS number FROM lagou WHERE city="北京" GROUP BY workYear ORDER BY number DESC',
    'education'    => 'SELECT education, COUNT(education) AS number FROM lagou WHERE city="北京" GROUP BY education ORDER BY number DESC',
);

foreach ($sql as $key => $value) {
    echo "\n\t", $key, "\n\n";
    $datas = $pdo->findAll($value);
    $data = array();
    foreach ($datas as $v) {
        $data[$v[$key]] = $v['number'];
    }
    output($data, $total);
    echo "-------------------------\n";
}

echo "\n\tindustryField\n\n";
$industryFieldSql = 'SELECT industryField FROM lagou';
$industryFieldData = $pdo->findAll($industryFieldSql);
foreach ($industryFieldData as $key => $value) {
    if (strstr($value['industryField'], ',') !== false) {
        $industryField = explode(',',$value['industryField']);
    } else {
        $industryField = explode(' · ',$value['industryField']);
    }
    $industryFieldData[$key] = trim($industryField[0]);
    for ($i = 1; $i < count($industryField); $i++) {
        $industryFieldData[] = trim($industryField[$i]);
    }
}
$industryFieldData = array_count_values($industryFieldData);
arsort($industryFieldData);
output($industryFieldData, $total);
echo "-------------------------\n";

echo "\n\tsalary\n\n";
$salarySql = 'SELECT salary FROM lagou';
$salaryData = $pdo->findAll($salarySql);
output(salary($salaryData), $total);
echo "-------------------------\n";

echo "\n\twork year —— salary\n\n";
$workYear = array(
    '不限',
    '应届毕业生',
    '1年以下',
    '1-3年',
    '3-5年',
    '5-10年',
    '10年以上',
);

foreach ($workYear as $value) {
    echo "\n",$value,"\n";
    $workYearSalarySql = 'SELECT salary FROM lagou WHERE workYear="' . $value . '"';
    $workYearSalaryData = $pdo->findAll($workYearSalarySql);
    output(salary($workYearSalaryData), count($workYearSalaryData));
    echo "-------------\n";
}


function output($data, $total)
{
    foreach ($data as $key => $value) {
        if ($value > 0) {
            $proportion = round(($value/$total)*100, 2) . '%';
            echo '|', $key, '|', $value, "|", $proportion, "|\n";
        }
    }
}

function salary($data)
{
    $salaryNum = array(
        '2k以下'  => 0,
        '2k-5k'    => 0,
        '5k-10k'   => 0,
        '10k-15k'  => 0,
        '15k-25k'  => 0,
        '25k-50k'  => 0,
        '50k以上' => 0,
    );
    foreach ($data as $value) {
        $isMatched = preg_match('/^(\d+)k/', $value['salary'], $matches);
        if ($isMatched) {
            if ($matches[1] < 2) {
                ++$salaryNum['2k以下'];
            } elseif ($matches[1] < 5) {
                ++$salaryNum['2k-5k'];
            } elseif ($matches[1] < 10) {
                ++$salaryNum['5k-10k'];
            } elseif ($matches[1] < 15) {
                ++$salaryNum['10k-15k'];
            } elseif ($matches[1] < 25) {
                ++$salaryNum['15k-25k'];
            } elseif ($matches[1] < 50) {
                ++$salaryNum['25k-50k'];
            } else {
                ++$salaryNum['50k以上'];
            }
        }
    }
    return $salaryNum;
}
*/
function dataCount($data)
{
    global $keywordsCount;
    foreach ($data as $value) {
        $value = strtolower($value);
        $keywordsCount[$value] = array_key_exists($value, $keywordsCount) ? $keywordsCount[$value] + 1 : 1;
    }
}
$keywordsCount = array();
$sql = 'SELECT `jobDescription` FROM lagou LIMIT 0, 2;';
$datas = $pdo->findAll($sql);
foreach ($datas as $key=>$value) {
    $isMatched = preg_match_all('/\b[a-zA-Z.]+\d?\b/', $value['jobDescription'], $matches);
    if ($isMatched) {
        // print_r(array_unique($matches[0]));
        dataCount(array_unique($matches[0]));
    }
}
print_r($keywordsCount);
die;
$keywords = array(
    'php', 'mysql', 'web', 'linux', 'css',
    'javascript', 'html', 'ajax', 'jquery', 'sql',
    'mvc', 'lamp', 'js', 'apache', 'xml',
    'unix', 'div', 'nginx', 'yii', 'thinkphp',
    'redis', 'xhtml', 'shell', 'oop', 'json',
    'memcache', 'zend', 'java', 'api', 'ci',
    'svn', 'python', 'codeigniter', 'html5', 'nosql',
    'discuz', 'smarty', 'mongodb', 'cms', 'oracle',
    'w3c', 'framework', 'lbs', 'git', 'memcached',
    'tcp', 'lnmp', 'cakephp', 'rest', 'crm',
    'android', 'uml', 'css3', 'webservice', 'php5',
    'tp', 'dhtml', 'ecshop', 'symfony', 'erp',
    'windows', 'sns', 'wordpress', 'seo', 'phpcms',
    'bootstrap', 'drupal', 'cache', 'o2o', 'ui',
    'postgresql', 'perl', 'github', 'oa', 'yaf',
);