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

$totalSql = 'SELECT count(uuid) AS number FROM lagou';
$total = $pdo->findOne($totalSql)['number'];
echo 'Total:', $total, "\n";
echo "-------------------------\n";

$sql = array(
    'district'     => 'SELECT district, COUNT(district) AS number FROM lagou GROUP BY district ORDER BY number DESC',
    'financeStage' => 'SELECT financeStage, COUNT(financeStage) AS number FROM lagou GROUP BY financeStage ORDER BY number DESC',
    'workYear'     => 'SELECT workYear, COUNT(workYear) AS number FROM lagou GROUP BY workYear ORDER BY number DESC',
    'education'    => 'SELECT education, COUNT(education) AS number FROM lagou GROUP BY education ORDER BY number DESC',
);

foreach ($sql as $key => $value) {
    echo "\n\t", $key, "\n\n";
    $datas = $pdo->findAll($value);
    $data = array();
    foreach ($datas as $v) {
        $data[$v[$key]] = $v['number'];
    }
    output($data);
    echo "-------------------------\n";
}

echo "\n\tindustryField\n\n";
$industryFieldSql = 'SELECT industryField FROM lagou';
$industryFieldData = $pdo->findAll($industryFieldSql);
foreach ($industryFieldData as $key => $value) {
    $industryField = explode(' · ',$value['industryField']);
    $industryFieldData[$key] = trim($industryField[0]);
    for ($i = 1; $i < count($industryField); $i++) {
        $industryFieldData[] = trim($industryField[$i]);
    }
}
$industryFieldData = array_count_values($industryFieldData);
arsort($industryFieldData);
output($industryFieldData);
echo "-------------------------\n";

echo "\nsalary\n\n";
$salaryNum = array(
    '2k以下'  => 0,
    '2k-5k'    => 0,
    '5k-10k'   => 0,
    '10k-15k'  => 0,
    '15k-25k'  => 0,
    '25k-50k'  => 0,
    '50k以上' => 0,
);
$salarySql = 'SELECT salary FROM lagou';
$salaryData = $pdo->findAll($salarySql);
foreach ($salaryData as $value) {
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
output($salaryNum);


function output($data)
{
    global $total;
    foreach ($data as $key => $value) {
        $proportion = round(($value/$total)*100, 2) . '%';
        echo $key, ':', $value, " —— ", $proportion, "\n";
    }
}


