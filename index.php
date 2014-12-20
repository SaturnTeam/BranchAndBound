<?php
namespace    TheSaturn\BranchAndBound;
spl_autoload_register(function ($class)
{
    $class = substr($class, strrpos($class, "\\") + 1);
    include 'lib/' . $class . '.php';
});
$messages = new    Messages;
BranchAndBound::$messages = $messages;
Node::$messages = $messages;
$t1 = microtime(true);
$tableBranchAndBound = new    TableBranchAndBound;
$root = new    Node($tableBranchAndBound->table);
$t2 = microtime(true);
$googleRows = new    RowsGoogleCharts($root);/*
function memoryCount(Node $root)
{
				$mem = 0;
				if ($root)
				{
								$mem += count($root->branchnBound->table)*count($root->branchnBound->table);
								foreach($root->childrens as $child)
								{
												$mem += memoryCount($child) + 1;
								}
				}
				return $mem;
}
$mem = memoryCount($root);
$mem = [];
$times = [];
for($i = 0; $i < 600; $i++)
{
				unset($root);
				unset($tableBranchAndBound);
				$t = microtime(true);
				$tableBranchAndBound	=	new	TableBranchAndBound;
				$root	=	new	Node($tableBranchAndBound->table);
				$times[$i] = microtime(true) - $t;
				$mem[] = memoryCount($root);
}
for($i = 0; $i < 100;$i++)
{

unset($times[$i]);
}

sort($times);
sort($mem);
echo min($times),"\n", max($times),"\n";
echo min($mem),"\n", max($mem);*/
/*for ($j = 3; $j <= 44; $j++)
{
    for ($i = 0; $i < 100; $i++)
    {
        unset($root);
        unset($tableBranchAndBound);
        $_POST['amount'] = $j;
        $t = microtime(true);
        $tableBranchAndBound = new    TableBranchAndBound;
        $root = new    Node($tableBranchAndBound->table);
        $times[$j][$i] = microtime(true) - $t;
    }
}
    foreach($times as &$time)
    {
        $time = array_sum($time) / count($time);
        //$time = min($time);
    }*/
?><!DOCTYPE html>
<html lang="ru">
<head>
    <base href="http://<?= $_SERVER['HTTP_HOST'] ?>"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Метод ветвей и границ</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="user.css" rel="stylesheet">
    <!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter27657369 = new Ya.Metrika({id:27657369,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/27657369" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
    <script src="template/default/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        var prevId = '';
        function myReadyHandler() {
            $('tr.google-visualization-orgchart-noderow-medium > td').click(function () {
                var id = $(this).find('val').text();
                //console.log($(this).find('val').text());
                $('.page').each(function () {
                    $(this).hide("fast");
                    if (prevId != id && $(this).data('id') == id) {
                        $(this).toggle("medium");
                    }
                });
                prevId = id;
            });
        }
        google.load("visualization", "1", {packages: ["orgchart", "corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            data.addRows([
                <?=$googleRows?>
            ]);
            var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
            google.visualization.events.addListener(chart, 'ready', myReadyHandler);
            chart.draw(data, {allowHtml: true});
            var data = google.visualization.arrayToDataTable([
             ['Номер', 'Время'],
            <?php
                                            /*foreach($times as $key => $time)
                                            {
                                                            //if($key%3 == 0)
                                                            echo "[".($key).", $time],";
                                            }*/
            ?>
             ]);

             var options = {
             title: 'Минимальное затраченное времяв зависимости от размерности',
                 curveType: 'function',
             hAxis: {title: 'Размерность', titleTextStyle: {color: 'red'}, minValue: 0}
             };

             var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));

             chart.draw(data, options);
             /*
             var data = google.visualization.arrayToDataTable([
             ['Номер', 'Кол-во элементов'],
            <?php
                                            /*foreach($mem as $key => $time)
                                            {
                                                            echo "['$key', $time],";
                                            }*/
            ?>
             ]);

             var options = {
             title: 'Память',
             hAxis: {title: 'Память', titleTextStyle: {color: 'red'}}
             };

             var chart = new google.visualization.ColumnChart(document.getElementById('chart_div3'));

             chart.draw(data, options);*/
            /*
             var data = google.visualization.arrayToDataTable([
             ['Номер', 'Кол-во элементов'],
            <?php
                                            /*foreach($mem as $key => $time)
                                            {
                                                            echo "['$key', $time],";
                                            }*/
            ?>
             ]);

             var options = {
             title: 'Память',
             hAxis: {title: 'Память', titleTextStyle: {color: 'red'}}
             };

             var chart = new google.visualization.ColumnChart(document.getElementById('chart_div3'));

             chart.draw(data, options);*/
        }
    </script>
</head>
<body>
<div class="container">
    <div class="page-header">
        <div class="row">
            <h1 class="col-md-9"><a href="<?= $_SERVER['REQUEST_URI'] ?>">Метод ветвей и границ</a></h1>

        </div>
        <p class="lead hidden-xs">Правильное нахождение минимального пути коммивояжера</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h3>Примеры из веба для сверки ответов:</h3>

            <form method="POST">
                <p>
                    <button name="pr1" class="btn btn-default">Пример1</button>
                    <a href="/2014_aisd_lektsia5-1.ppt">Лекция</a>
                </p>
                <p>
                    <button name="pr2" class="btn btn-default">Пример2</button>
                    <a href="http://baza-referat.ru/%D0%A0%D0%B5%D1%88%D0%B5%D0%BD%D0%B8%D0%B5_%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B8_%D0%BA%D0%BE%D0%BC%D0%BC%D0%B8%D0%B2%D0%BE%D1%8F%D0%B6%D0%B5%D1%80%D0%B0_%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D0%BE%D0%BC_%D0%B2%D0%B5%D1%82%D0%B2%D0%B5%D0%B9_%D0%B8_%D0%B3%D1%80%D0%B0%D0%BD%D0%B8%D1%86"
                       target="blank">Источник</a>
                </p>
                <p>
                    <button name="pr3" class="btn btn-default">Пример3</button>
                    <a href="http://stud-baza.ru/reshenie-zadachi-kommivoyajera-metodom-vetvey-i-granits-kursovaya-rabota-matematika"
                       target="blank">Источник</a>
                </p>
            </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h2>Таблица длин маршрутов</h2>

            <form method="POST" class="form-inline">
                <p>
                    <input type="number" class="form-control" placeholder="Размерность" id="amount" name="amount">
                </p>

                <p>
                    Отображать дерево<input type="checkbox" class="form-control" name="google" checked>
                </p>

                <p>
                    <button class="btn btn-default form-inline" type="submit" name="change">Изменить размер таблицы
                    </button>
                </p>
                <?= $tableBranchAndBound ?>
                <button class="btn btn-primary" type="submit">Посчитать!</button>
            </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h4>Легенда узла: <sub>Номер шага обработки</sub>(Строка:Колонка)<sub>Стоимость</sub></h4>

            <p>
                При клике на узел с номером обработки можно увидеть <b>лог вычислений</b> на данном этапе
            </p>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div id="chart_div"></div>
            <div id="chart_div2"></div>
            <div id="chart_div3"></div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h2><?= Node::$answer; ?></h2><?= 'Время:' . ($t2 - $t1) ?>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <?php
            $messages->printt();
            ?>
        </div>
    </div>

</div>

<script type="text/javascript">

</script>
</body>
</html>
