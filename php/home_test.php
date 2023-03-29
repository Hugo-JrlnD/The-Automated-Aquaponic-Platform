<!DOCTYPE HTML>
<html>
  <head>
    <title>ESP32 WITH MYSQL DATABASE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="icon" href="data:,">
    <style>
      html {font-family: Arial; display: inline-block; text-align: center;}
      p {font-size: 1.2rem;}
      h4 {font-size: 0.8rem;}
      body {margin: 0;}
      .topnav {overflow: hidden; background-color: #0c6980; color: white; font-size: 1.2rem;}
      .content {padding: 5px; }
      .card {background-color: white; box-shadow: 0px 0px 10px 1px rgba(140,140,140,.5); border: 1px solid #0c6980; border-radius: 15px;}
      .card.header {background-color: #0c6980; color: white; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 12px; border-top-left-radius: 12px;}
      .cards {max-width: 700px; margin: 0 auto; display: grid; grid-gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));}
      .reading {font-size: 1.3rem;}
      .packet {color: #bebebe;}
      .waterlevelColor {color: #fd7e14;}
      .StatusWLSColor {color: #702963; font-size:12px;}
      
      /* ----------------------------------- Toggle Switch */
      .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
      }

      .switch input {display:none;}

      .sliderTS {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #D3D3D3;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
      }

      .sliderTS:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: #f7f7f7;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
      }

      input:checked + .sliderTS {
        background-color: #00878F;
      }

      input:focus + .sliderTS {
        box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .sliderTS:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
      }

      .sliderTS:after {
        content:'OFF';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 70%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
      }

      input:checked + .sliderTS:after {  
        left: 25%;
        content:'ON';
      }

      input:disabled + .sliderTS {  
        opacity: 0.3;
        cursor: not-allowed;
        pointer-events: none;
      }
      /* ----------------------------------- */
    </style>
  </head>
  
  <body>
  <!-- The navigation bar at the top of the page -->
  <div class="topnav">
    <h3>ESP32 WITH MYSQL DATABASE</h3>
  </div>

  <br>

  <!-- Content section for monitoring and controlling -->
  <div class="content">
    <div class="cards">
      <!-- Monitoring card -->
      <div class="card">
        <div class="card header">
          <h3 style="font-size: 1rem;">MONITORING</h3>
        </div>
        <!-- Displays the water level value received from ESP32. -->
        <h4 class="waterlevelColor"><i class="fas fa-thermometer-half"></i> WATER LEVEL</h4>
        <p class="waterlevelColor"><span class="reading"><span id="ESP32_01_WL"></span> mm</span></p>
        <!-- Displays the status of the water level sensor -->
        <p class="StatusWLSColor"><span>Water Level Sensor State: </span><span id="ESP32_01_StatusWLS"></span></p>
      </div>
    </div>
  </div>

  <br>

  <!-- Content section for displaying the last time data was received from ESP32 and a button to open a record table -->
  <div class="content">
    <div class="cards">
      <div class="card header" style="border-radius: 15px;">
        <h3 style="font-size: 0.7rem;">LAST TIME RECEIVED DATA FROM ESP32 [ <span id="ESP32_01_LTRD"></span> ]</h3>
        <button onclick="window.open('recordtable_test.php', '_blank');">Open Record Table</button>
        <h3 style="font-size: 0.7rem;"></h3>
      </div>
    </div>
  </div>

  <!-- Script section -->
  <script>
    // Set initial values for water level, water level sensor status, and last time data was received to "NN"
    document.getElementById("ESP32_01_WL").innerHTML = "NN"; 
    document.getElementById("ESP32_01_StatusWLS").innerHTML = "NN";
    document.getElementById("ESP32_01_LTRD").innerHTML = "NN";

    // Call the Get_Data function with "esp32_01" as the id
    Get_Data("esp32_01");

    // Call the myTimer function every 5 seconds
    setInterval(myTimer, 5000);

    // Get data function that sends a POST request to a PHP script to get data from a MySQL database
    function Get_Data(id) {
      // Create an XMLHttpRequest object
      if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      // Define a callback function that parses the response text and updates the page with the received data
      xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          const myObj = JSON.parse(this.responseText);
          if (myObj.id == "esp32_01") {
            document.getElementById("ESP32_01_WL").innerHTML = myObj.water_level;
            document.getElementById("ESP32_01_StatusWLS").innerHTML = myObj.Status_WLS;
            document.getElementById("ESP32_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " + myObj.ls_date + " (dd-mm-yyyy)";
          }
        }
      };
		// Open a POST request to the PHP script and send the id parameter
        xmlhttp.open("POST","getdata_test.php",true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("id="+id);
			}
      //------------------------------------------------------------
      
    </script>
  </body>
</html>