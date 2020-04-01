<?php
  ini_set("allow_url_fopen", 1);

  /* fetch historical API data */
  $json = file_get_contents('https://corona.lmao.ninja/all');
  $obj = json_decode($json);
  $jsonHistorial = file_get_contents('https://corona.lmao.ninja/v2/historical/all');
  $objHistorial = json_decode($jsonHistorial);
  $arrayHistorial = json_decode(json_encode($objHistorial), true);

  /* cases - chart[1] */
  $datesCases = array_keys($arrayHistorial['cases']);
  $datesFormatted = "'".implode("','",$datesCases)."'";
  $casesByDay = array_values($arrayHistorial['cases']);
  $casesByDayFormatted = "'".implode("','",$casesByDay)."'";

  /* deaths - chart[2] */
  $datesDeaths = array_keys($arrayHistorial['deaths']);
  $datesFormattedDeaths ="'".implode("','",$datesDeaths)."'";
  $deathsByDay = array_values($arrayHistorial['deaths']);
  $deathsByDayFormatted = "'".implode("','",$deathsByDay)."'";

  /* top card calculations */
  $yesterdayCases = end($arrayHistorial['cases']);
  $totalCases = ($obj-> cases);

  $yesterdayDeaths = end($arrayHistorial['deaths']);
  $totalDeaths = ($obj-> deaths);

  $yesterdayRecoveries = end($arrayHistorial['recovered']);
  $totalRecoveries= ($obj-> recovered);

  $activeCases = ($obj-> active);
  $activeYesterday = ($yesterdayCases - $yesterdayDeaths - $yesterdayRecoveries);

  /* calculate percentage change */
  function getPercentageChange($oldNumber, $newNumber){
    $decreaseValue = $newNumber - $oldNumber;
    $percentage = round(($decreaseValue / $oldNumber) * 100);
    $output = "";

    if ($percentage > 0) {
      $output = $percentage . "% increase";
    } elseif ($percentage < 0) {
      $output = $percentage . "% decrease";
    } else {
      $output = $percentage . "% increase";
    }

    return $output;
  }

  function getBadgeClass($percentage){
    $output = "";

    if ($percentage > 0) {
      $output = "badge-danger";
    } elseif ($percentage < 0) {
      $output = "badge-success";
    } else {
      $output = "badge-info";
    }

    return $output;
  }

  function thousandsCurrencyFormat($num) {
    if ($num > 1000) {
      $x = round($num);
      $x_number_format = number_format($x);
      $x_array = explode(',', $x_number_format);
      $x_parts = array('k', 'm', 'b', 't');
      $x_count_parts = count($x_array) - 1;
      $x_display = $x;
      $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
      $x_display .= $x_parts[$x_count_parts - 1];

      return $x_display;
    } else {
      return $num;
    }
  }
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>COVID-19 Tracker</title>
  <meta name="description" content="Track the spread of the Coronavirus Covid-19 outbreak">

  <link rel="stylesheet" href="assets/css/tachyons.min.css">
  <link rel="stylesheet" href="assets/css/site.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-162093056-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-162093056-1');
  </script>

  <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon//apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon//apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon//apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon//apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon//apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon//apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon//apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon//apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="assets/favicon//android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon//favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon//favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon//favicon-16x16.png">
  <link rel="manifest" href="assets/favicon//manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="assets/favicon//ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://viruscovid.tech">
  <meta property="og:title" content="🦠COVID-19 Tracker">
  <meta property="og:description" content="Track the spread of the Coronavirus Covid-19 outbreak">
  <meta property="og:image" content="https://viruscovid.tech/assets/img/meta-tags-16a33a6a8531e519cc0936fbba0ad904e52d35f34a46c97a2c9f6f7dd7d336f2.png">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://viruscovid.tech">
  <meta property="twitter:title" content="🦠COVID-19 Tracker">
  <meta property="twitter:description" content="Track the spread of the Coronavirus Covid-19 outbreak">
  <meta property="twitter:image" content="https://viruscovid.tech/assets/img/meta-tags-16a33a6a8531e519cc0936fbba0ad904e52d35f34a46c97a2c9f6f7dd7d336f2.png">

  <script src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>

  <!-- <script type="text/javascript" src="assets/js/Chart.bundle.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" />
</head>
<body>
  <div class="h-100 midnight-blue pa3 ph0-l pv6-l">
    <div class="center mw7">
      <article class="cf">
        <a class="product-hunt" href="https://www.producthunt.com/posts/covid-19-tracker-4?utm_source=badge-featured&utm_medium=badge&utm_souce=badge-covid-19-tracker-4" target="_blank"><img class="product-hunt"src="https://api.producthunt.com/widgets/embed-image/v1/featured.svg?post_id=190043&theme=light" alt="COVID-19 Tracker - Simple, no bullshit COVID-19 tracker. | Product Hunt Embed" style="width: 250px; height: 54px;" width="250px" height="54px" /></a>

        <header class="header mw5 mw7-ns tl pa3">
          <h1 class="mt0">🦠 COVID-19 Tracker</h1>
          <p class="lh-copy measure black-60">
            Track the spread of the Coronavirus Covid-19 outbreak
          </p>
        </header>

        <div class="fl w-50 tc stat-card">
          <div class="card-box tilebox-one">
            <span class="icon">
              <img src="assets/img/cases.svg">
            </span>
            <h6 class="black-40 ttu tl">Total Cases</h6>
            <h3 class="black tl" data-plugin="counterup"><?php echo number_format($obj-> cases) ?></h3>
            <div class="sub-info pt3 pb4">
              <span class="badge <?php echo getBadgeClass(getPercentageChange($yesterdayCases, $totalCases));?> mr-1"><?php echo getPercentageChange($yesterdayCases, $totalCases) ?></span>
              <span class="text-muted black-40">from yesterday (<?php echo thousandsCurrencyFormat($yesterdayCases) ?>)</span>
            </div>
          </div>
        </div>
        <div class="fl w-50 tc stat-card">
          <div class="card-box tilebox-one">
            <span class="icon">
              <img src="assets/img/deaths.svg">
            </span>
            <h6 class="black-40 ttu tl">Total Deaths</h6>
            <h3 class="black tl" data-plugin="counterup"><?php echo number_format($obj-> deaths) ?></h3>
            <div class="sub-info pt3 pb4">
              <span class="badge <?php echo getBadgeClass(getPercentageChange($yesterdayDeaths, $totalDeaths));?> mr-1"><?php echo getPercentageChange($yesterdayDeaths, $totalDeaths) ?></span>
              <span class="text-muted black-40">from yesterday (<?php echo thousandsCurrencyFormat($yesterdayDeaths) ?>)</span>
            </div>
          </div>
        </div>
      </article>
      <article class="cf">
        <div class="fl w-50 tc stat-card">
          <div class="card-box tilebox-one">
            <span class="icon"><img src="assets/img/recoveries.svg"></span>
            <h6 class="black-40 ttu tl">Total Recoveries</h6>
            <h3 class="black tl" data-plugin="counterup"><?php echo number_format($obj-> recovered) ?></h3>
            <div class="sub-info pt3 pb4">
              <span class="badge <?php echo getBadgeClass(getPercentageChange($totalRecoveries, $yesterdayRecoveries));?> mr-1"><?php echo getPercentageChange($yesterdayRecoveries, $totalRecoveries) ?></span>
              <span class="text-muted black-40">from yesterday (<?php echo thousandsCurrencyFormat($yesterdayRecoveries) ?>)</span>
            </div>
          </div>
        </div>
        <div class="fl w-50 tc stat-card">
          <div class="card-box tilebox-one">
            <span class="icon">
              <img src="assets/img/active_cases.svg">
            </span>
            <h6 class="black-40 ttu tl">Active Cases</h6>
            <h3 class="black tl" data-plugin="counterup"><?php echo number_format($obj-> active) ?></h3>
            <div class="sub-info pt3 pb4">
              <span class="badge <?php echo getBadgeClass(getPercentageChange($activeYesterday, $activeCases));?> mr-1"><?php echo getPercentageChange($activeYesterday, $activeCases) ?></span>
              <span class="text-muted black-40">from yesterday (<?php echo thousandsCurrencyFormat($activeYesterday) ?>)</span>
            </div>
          </div>
        </div>
      </article>
      <section class="country-table">
        <h1>🌎 Country Breakdown</h1>
        <div class="table-responsive">
          <?php
            $json = file_get_contents("https://corona.lmao.ninja/countries");
            $data = json_decode($json);
            $array = json_decode(json_encode($data), true);

            echo '<table id="country-table" class="table table-striped table-curved">';
            echo '<thead>
                    <tr>
                      <th>Rank</th>
                      <th>Country</th>
                      <th>Cases</th>
                      <th>Deaths</th>
                      <th>Critical</th>
                      <th>Recovered</th>
                      <th>Today\'s Cases</th>
                      <th>Today\'s Deaths</th>
                      <th>Cases Per 1M</th>
                      <th>Deaths Per 1M</th>
                    </tr>
                  </thead>';

            echo'<tbody id="tbody">';

            foreach($array as $result) {
              echo '<tr>';
              echo '<td></td>';
              echo '<td> <img src='.$result['countryInfo']['flag'].'>'.$result['country'].'</td>';
              echo '<td>' .number_format($result['cases']).'</td>';
              echo '<td>'.number_format($result['deaths']).'</td>';
              echo '<td>'.number_format($result['critical']).'</td>';
              echo '<td>'.number_format($result['recovered']).'</td>';
              echo '<td>'.number_format($result['todayCases']).'</td>';
              echo '<td>'.number_format($result['todayDeaths']).'</td>';
              echo '<td>'.number_format($result['casesPerOneMillion']).'</td>';
              echo '<td>'.number_format($result['deathsPerOneMillion']).'</td>';
              echo '</tr>';
            };
            echo'</tbody>';
            echo '</table>';
          ?>
        </div>
      </section>

      <section class="country-table">
        <div class="card-box chart-wrapper">
          <h1>Cases</h1>
          <div class="chart-container">
            <canvas id="cases"></canvas>
          </div>
        </div>
      </section>

      <section class="country-table">
        <div class="card-box chart-wrapper">
          <h1>Deaths</h1>
          <div class="chart-container">
            <canvas id="deaths"></canvas>
          </div>
        </div>
      </section>

      <script>
        var ctx = document.getElementById('cases');
        var myChart = new Chart(ctx, {
          type: 'line',
          responsive: true,
          data: {
            labels:  [<?php echo $datesFormatted; ?>],
            datasets: [{
              label: '# of Cases',
              data: [<?php echo $casesByDayFormatted; ?>],
              backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
              ],
              borderColor: [
                'rgba(54, 162, 235, 1)',
              ],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                }
              }]
            }
          }
        });
      </script>

      <script>
        var ctx = document.getElementById('deaths');
        var myChart = new Chart(ctx, {
          type: 'line',
          responsive: true,
          data: {
            labels:  [<?php echo $datesFormattedDeaths; ?>],
            datasets: [{
              label: '# of Deaths',
              data: [<?php echo $deathsByDayFormatted; ?>],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
              ],
              borderColor: [
                'rgba(255, 99, 132, 1)',
              ],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                }
              }]
            }
          }
        });
      </script>
      <footer class="">
        <a class="buy" href="https://www.buymeacoffee.com/kylerphillips">
          <button class="donate-btn-round">
            <img src="assets/img/coffee-buy.png">
          </button>
        </a>
        <div class="mt1">
          <a href="https://kyler.design" title="Kyler Phillips" class="f4 dib pr2 mid-gray dim">👨 Made by Kyler Phillips</a>
          <a href="https://github.com/NovelCOVID/API" title="Data Source" class="f4 dib pr2 mid-gray dim">📊 Data Source</a>
        </div>
      </footer>
    </div>
  </div>
  <script>
    $(() => {
      var t = $('#country-table').DataTable({
        "columnDefs": [ {
          "searchable": false,
          "orderable": false,
          "targets": 0
        } ],
        "order": [[ 2, 'desc' ]],
        "bLengthChange": false,
      });
      t.on('order.dt search.dt', () => {
        t.column(0, {search:'applied', order:'applied'}).nodes().each((cell, i) => {
          cell.innerHTML = i+1;
        });
      }).draw();
    });
  </script>
</body>
</html>
