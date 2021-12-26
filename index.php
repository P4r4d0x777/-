
<!DOCTYPE html>
<html>
  
<head>
    <title>
        Home Page
    </title>
    
    <meta charset ="utf-8"> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1"> 
    <link rel ="stylesheet" href ="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel ="stylesheet" href = "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> 
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"> 
    </script> 
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"> 
    </script> 
    <script src = "https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"> 
    </script> 
      
    <style type="text/css">
        h1 {
            margin-top:-60px;
            color:green;
        }
        .xyz {
            background-size: auto;
            text-align: center;
            padding-top: 100px;
        }
        .btn-circle.btn-xl {
            width: 100px;
            height: 100px;
            padding: 10px 16px;
            border-radius: 60px;
            font-size: 17px;
            text-align: center;
        }
        .xyz small{
          font-size: 22px;
          color:gray;
        }
        a{
          position: relative;
          margin-right: 30px;
          margin-left: 30px;
          margin-top: 50px;
        }
        .first{
            width: 30%;
            position: absolute;
            margin-top: 50px;
            margin-left: 40px;
        }
        .first_img{

            height: 500px; 
        }
        .second_img{
            height:500px;
        }
        .second{
            width: 30%;
            position: absolute;
            margin-top: 50px;
            margin-left: 35%;
        }
        .third_img{
            height:500px;
        }
        .third{
            width: 30%;
            position: absolute;
            margin-top: 50px;
            margin-left: 68%;
        }
    </style>

</head> 
  
<body class="xyz">
    <p>I want change this file and push him with new commit (just for test)</p>
    <h1>Obtaining Exchange Rates
      <small>from the central bank of the Russian Federation</small>
    </h1>
    <h4>What information do you want to receive?</h4>
    <a type="button" href = "./pages/day.php" class="btn btn-success btn-circle btn-xl"><i class="bi bi-currency-exchange" style = "font-size:30px; color: white;"></i><br>Day
    </a>
    <a type="button" href = "./pages/period.php" class="btn btn-success btn-circle btn-xl"><i class="bi bi-calendar-date" style = "font-size:30px; color: white;"></i><br>Period</a>
    <a type="button" href = "./pages/graph.php" class="btn btn-success btn-circle btn-xl"><i class="bi bi-graph-up" style = "font-size:30px; color: white;"></i><br>Graph</a>

    <p class = "first"><img class = "first_img" src = "gifs/day.gif"><b>Day</b>.php<br>1) Choose a day<br>2) Select a list of currencies<br>3) Get a list of currency rates for a given day</p>

    <p class = "second"><img class = "second_img" src = "gifs/period.gif"><b>Period</b>.php<br>1) Choose a valute<br>2) Choose dateFrom and dateTo<br>3) Get a list of currency rate for a period</p>

    <p class = "third"><img class = "third_img" src = "gifs/graph.gif"><b>Graph</b>.php<br>1) Choose a valute<br>2) Choose dateFrom and dateTo<br>3) Get a graph of currency rate for a given day</p>
</body>
  
</html> 

